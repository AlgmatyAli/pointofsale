<?php

namespace app\controllers;

use Yii;
use app\models\TempArrangement;
use app\models\TempArrangementSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Category;
use yii\filters\AccessControl;
use app\models\ArrangementDetails;
use app\models\CompanyInfo;
use app\models\Stocks;
use yii\db\Query;
use yii\web\Response;

/**
 * TempArrangementController implements the CRUD actions for TempArrangement model.
 * (قائمة تسوية الجرد المؤقتة لكل مستخدم قبل «إتمام»؛ لا تحرّك المخزون.)
 */
class TempArrangementController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'delete-all' => ['post'],   // الواجهة ترسله POST أصلاً
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'view', 'delete-all', 'delete', 'itemlist', 'get-inv'],
                        'roles' => ['createArrangment'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all TempArrangement models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TempArrangementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TempArrangement model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TempArrangement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * الحقول في النموذج:  realQuantity = رصيد النظام (تعبئه الواجهة من get-inv)،  quantity = الكمية المجرودة فعلياً.
     * الفرق = المجرود - رصيد النظام:  ≥ 0 يُسجَّل «إضافة» (type 1)، وإلا «إنقاص» (type -1) بقيمة مطلقة.
     * رصيد النظام يُقرأ الآن من جدول stocks على الخادم (القيمة المرسلة من المتصفح تُهمل).
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TempArrangement();
        $userId = (int) Yii::$app->user->identity->id;
        $branch = Yii::$app->user->identity->branch;

        $searchModel = new TempArrangementSearch();
        $searchModel->created_by = $userId;
        $searchModel->state = 0;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->post('hasEditable')) {
            return $this->inlineEdit($userId);
        }

        if ($model->loadAll(Yii::$app->request->post())) {
            $category = (int) $model->category;
            $categoryModel = $category > 0 ? Category::findOne($category) : null;
            if ($categoryModel === null) {
                Yii::$app->session->setFlash('error', 'اختر الصنف من القائمة.');
                return $this->redirect(['create']);
            }

            if (!is_numeric($model->quantity) || (float) $model->quantity < 0) {
                Yii::$app->session->setFlash('error', 'الكمية المجرودة يجب أن تكون رقماً صفراً أو أكبر.');
                return $this->redirect(['create']);
            }
            $counted = round((float) $model->quantity, 3);

            $temp = TempArrangement::find()->where([
                'category' => $category,
                'stockTaking' => 1,
            ])->one();

            if ($model->stockTaking == 1 && $temp <> NULL) {
                Yii::$app->session->setFlash('error', Yii::t('app', "This Item Alrady stockTaking") . ' : ' . $categoryModel->name);
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }

            $tempD = ArrangementDetails::find()
                ->leftJoin('arrangement', 'arrangement.id = arrangementDetails.arrangement')
                ->where([
                    'category' => $category,
                    'stockTaking' => 1,
                ])
                ->andWhere('year(arrangement.at) = ' . (int) date("Y"))
                ->one();

            if ($model->stockTaking == 1 && $tempD <> NULL) {
                Yii::$app->session->setFlash('error', Yii::t('app', "This Item Alrady stockTaking") . ' : ' . $categoryModel->name);
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }

            // رصيد النظام من الخادم
            $system = $this->stockQty($category, $branch);
            $deferent = round($counted - $system, 3);

            $model->category = $category;
            $model->realQuantity = $system;
            if ($deferent >= 0) {
                $model->type  = 1;
                $model->quantity = $deferent;
            }
            if ($deferent < 0) {
                $model->type  = -1;
                $model->quantity = ($deferent * -1);
            }

            $model->state = 0;
            $model->box = 1;
            $model->branch = $branch;
            $model->created_by = $userId;
            $model->save(false);

            return $this->redirect(['create', 'id' => 1]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Updates an existing TempArrangement model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TempArrangement model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteWithRelated();

        return $this->redirect(['create']);
    }


    /**
     * Finds the TempArrangement model based on its primary key value.
     * يُقيَّد بسجلات المستخدم الحالي فقط (كان أي مستخدم يستطيع فتح أو حذف سجلات غيره بالرقم).
     * @param integer $id
     * @return TempArrangement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = TempArrangement::findOne(['id' => (int) $id, 'created_by' => Yii::$app->user->identity->id]);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    public function actionDeleteAll()
    {
        TempArrangement::deleteAll(['state' => 0, 'created_by' => Yii::$app->user->identity->id]);
        return $this->redirect(['create']);
    }

    /**
     * بحث الأصناف لقائمة Select2.
     * كل المدخلات مربوطة كمعاملات (كان $q يُلصق في نص SQL مباشرة، أي ثغرة SQL injection).
     * costPrice لا يُرجَع إلا لمن يملك صلاحية seeCostPrice.
     */
    public function actionItemlist($q = null, $id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $q = str_replace(' ', '%', (string) $q);
            $query = new Query;
            $secript = [
                'category.id',
                'category.name AS text',
                'company AS company',
                'stocks.quantity as quantity',
                'maxPrice as maxPrice',
                'costPrice as costPrice',
                'serialNo AS serialNo',
                'minPrice AS minPrice',
                'place',
                'commCode',
                'branches.name AS BRNAME'
            ];
            $query->select(
                $secript
            )
                ->from('category')
                ->leftJoin('prices', 'prices.category = category.id')
                ->leftJoin('stocks', 'stocks.category = category.id')
                ->leftJoin('branches', 'branches.id = stocks.branch')
                ->where([
                    'or',
                    'category.name LIKE :nameLike',
                    ['like', "REPLACE(category.serialNo, '-', '')", str_replace('-', '', $q)],
                    ['like', 'category.commCode', $q],
                    ['like', 'category.id', $q],
                    ['like', 'category.place', $q],
                    ['like', 'category.company', $q],
                ])
                ->addParams([':nameLike' => '%' . $q . '%'])
                ->andWhere(['=', 'stocks.branch', Yii::$app->user->identity->branch])
                ->andWhere(['=', 'category.status', 0])
                ->andWhere(['=', 'stocks.type', 1])
                ->limit(60);
            $command = $query->createCommand();
            $data = $command->queryAll();

            if (!Yii::$app->user->can('seeCostPrice')) {
                foreach ($data as &$row) {
                    unset($row['costPrice']);
                }
                unset($row);
            }

            $out['results'] = array_values($data);
        }
        return $out;
    }

    public function actionGetInv($category)
    {
        $company = CompanyInfo::find()->one();
        if ($company->criteriaـvalue != 0) {
            $criteriaـvalue = $company->criteriaـvalue;
        } else {
            $criteriaـvalue = 1;
        }

        if ($company->rate != 0) {
            $rate = ($company->rate / 100);
            $maxPrice = 'CASE
        WHEN maxPrice >= ' . $criteriaـvalue . ' THEN round(maxPrice * "' . $rate . '" + maxPrice)
        ELSE maxPrice
        END  as maxPrice';

            $minPrice = 'CASE
        WHEN maxPrice >= ' . $criteriaـvalue . ' THEN round(minPrice * "' . $rate . '" + minPrice)
        ELSE minPrice
        END  as minPrice';
        } else {
            $maxPrice = 'maxPrice';
            $minPrice = 'minPrice';
        }

        $value = Stocks::find()
            ->leftJoin('prices', 'stocks.category = prices.category')
            ->select([
                'stocks.category AS id',
                'stocks.quantity as quantity',
                'costPrice',
                $maxPrice,
                $minPrice,
                'prices.minPrice2',
                'prices.minPrice3'
            ])
            ->Where(['stocks.branch' => Yii::$app->user->identity->branch])
            ->andwhere(['stocks.category' => (int) $category])
            ->andwhere(['in', 'type',  [1]])
            ->asArray()->one();

        // سعر التكلفة لا يُرجَع إلا لمن يملك صلاحية seeCostPrice
        if (is_array($value) && !Yii::$app->user->can('seeCostPrice')) {
            unset($value['costPrice']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $value;
    }

    // =====================================================================
    // أدوات داخلية
    // =====================================================================

    /**
     * التعديل المباشر لسطر في القائمة المؤقتة: الكمية ونوع الحركة فقط.
     */
    private function inlineEdit($userId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $key = (int) Yii::$app->request->post('editableKey');
        $all = Yii::$app->request->post('TempArrangement');
        $posted = is_array($all) ? current($all) : null;

        $result = TempArrangement::findOne(['id' => $key, 'created_by' => $userId, 'state' => 0]);
        if ($result === null || !is_array($posted)) {
            return ['output' => '', 'message' => 'السطر غير موجود.'];
        }
        if (!isset($posted['quantity']) && !isset($posted['type'])) {
            return ['output' => '', 'message' => 'يمكن تعديل الكمية أو نوع الحركة فقط.'];
        }

        if (isset($posted['quantity'])) {
            if (!is_numeric($posted['quantity']) || (float) $posted['quantity'] < 0) {
                return ['output' => '', 'message' => 'الكمية يجب أن تكون رقماً صفراً أو أكبر.'];
            }
            $result->quantity = $posted['quantity'];
        }
        if (isset($posted['type'])) {
            if (!in_array((int) $posted['type'], [1, -1], true)) {
                return ['output' => '', 'message' => 'نوع الحركة غير صحيح.'];
            }
            $result->type = (int) $posted['type'];
        }
        $result->save(false);

        return ['output' => isset($posted['quantity']) ? $result->quantity : $result->type];
    }

    /** رصيد الصنف في الفرع (النوع 1) */
    private function stockQty($category, $branch)
    {
        return (float) Yii::$app->db->createCommand(
            'SELECT COALESCE(SUM(quantity), 0) FROM stocks WHERE category = :c AND branch = :b AND type = 1',
            [':c' => $category, ':b' => $branch]
        )->queryScalar();
    }
}
