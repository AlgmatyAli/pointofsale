<?php

namespace app\controllers;

use Yii;
use app\models\Arrangement;
use app\models\ArrangementSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\TempArrangement;
use app\models\ArrangementDetails;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use app\models\ArrangementDetailsSearch;

/**
 * ArrangementController implements the CRUD actions for Arrangement model.
 *
 * تسوية الجرد: الرصيد (stocks، النوع 1، فرع الجرد) يتغير بمقدار  الكمية × النوع  (النوع 1 إضافة، -1 إنقاص).
 *  - الحفظ يطبّق ذلك لكل سطر، والتعديل يطبّق الفرق، والحذف يعكس الأثر بإشارة معكوسة.
 *  - كل عملية داخل transaction مع قفل MySQL مسمّى مشترك مع النقل (STOCK_LOCK).
 */
class ArrangementController extends Controller
{
    /** اسم القفل المشترك لعمليات المخزون (النقل والجرد) */
    const STOCK_LOCK = 'pos_stock_change';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'view', 'print', 'stock-print', 'stock-taking'],
                        'roles' => ['createArrangment'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'view', 'print',  'stock-print',  'stock-taking'],
                        'roles' => ['updateArrangment'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteArrangment'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['indexArrangment'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Arrangement models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArrangementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Arrangement model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Arrangement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Arrangement();

        if ($model->load(Yii::$app->request->post())) {
            $userId = (int) Yii::$app->user->identity->id;
            $branch = Yii::$app->user->identity->branch;

            $model->branch = $branch;
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = $userId;

            $temp_arrangement = TempArrangement::find()->where(['created_by' => $userId, 'state' => 0])->all();

            $error = $this->checkNewArrangement($model, $temp_arrangement);
            if ($error === null) {
                $error = $this->withStockLock(function () use ($model, $temp_arrangement, $userId, $branch) {
                    if (!$model->save()) {
                        return implode(' ', array_values($model->getFirstErrors()));
                    }

                    foreach ($temp_arrangement as $data) {
                        $modelDetails = new ArrangementDetails();
                        $modelDetails->arrangement = $model->id;
                        $modelDetails->category = $data->category;
                        $modelDetails->quantity = $data->quantity;
                        $modelDetails->box = 1;
                        $modelDetails->type = $data->type;
                        $modelDetails->stockTaking = $data->stockTaking;
                        $modelDetails->save(false);

                        // الرصيد += الكمية × النوع. وإن لم يوجد سجل رصيد يُنشأ بالقيمة الموقّعة نفسها
                        // (كان يُنشأ بالكمية موجبة دائماً حتى لو كان النوع إنقاصاً)
                        $this->changeStock($data->category, $branch, (float) $data->quantity * (int) $data->type, true);
                    }

                    TempArrangement::deleteAll(['created_by' => $userId, 'state' => 0]);
                    return null;
                });
            }

            if ($error === null) {
                return $this->redirect(['view', 'id' => $model->id]);
            }

            // قائمة التسوية المؤقتة تبقى كما هي ليعيد المستخدم المحاولة
            Yii::$app->session->setFlash('error', 'لم تُحفظ التسوية: ' . $error);
            return $this->redirect(['/temp-arrangement/create']);
        } elseif (Yii::$app->request->isAjax) {
            $model->at = date('Y-m-d');
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        } else {

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Arrangement model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $searchModel = new ArrangementDetailsSearch();
        $searchModel->arrangement = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->post('hasEditable')) {
            return $this->inlineEditDetail($model);
        }

        // الفرع وبيانات الإنشاء ثابتة: الأرصدة تحركت على هذا الفرع عند الحفظ
        $fixedBranch = $model->branch;
        $fixedCreatedAt = $model->created_at;
        $fixedCreatedBy = $model->created_by;

        if ($model->load(Yii::$app->request->post())) {
            $model->branch = $fixedBranch;
            $model->created_at = $fixedCreatedAt;
            $model->created_by = $fixedCreatedBy;
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing Arrangement model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $error = $this->withStockLock(function () use ($model) {
            $arrangementDetails = ArrangementDetails::find()->where(['arrangement' => $model->id])->all();

            foreach ($arrangementDetails as $value) {
                // عكس أثر الحفظ بإشارة معاكسة، على فرع التسوية نفسه (كان يُعكس على فرع المستخدم الحالي).
                // النوع -1 كان يُنقص الرصيد مرة ثانية عند الحذف بدل أن يعيده.
                $this->changeStock($value->category, $model->branch, -((float) $value->quantity * (int) $value->type), true);
            }

            ArrangementDetails::deleteAll(['arrangement' => $model->id]);
            $model->delete();
            return null;
        });

        if ($error !== null) {
            Yii::$app->session->setFlash('error', 'لم تُحذف التسوية: ' . $error);
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Arrangement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Arrangement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Arrangement::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionPrint($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ArrangementDetails::find()
                ->select('arrangementDetails.*, category.name, category.serialNo')
                ->leftJoin('category', 'category.id = arrangementDetails.category')
                ->where(['arrangementDetails.arrangement' => $id])
                ->orderBy('category.name'),

            'pagination' => ['pageSize' => false],
            'sort' => false,

        ]);

        return $this->render('print', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider

        ]);
    }

    public function actionStockPrint($id)
    {

        $dataProvider = new ActiveDataProvider([
            'query' => ArrangementDetails::find()
                ->select('arrangementDetails.*, category.name, category.serialNo, category.company')
                ->leftJoin('category', 'category.id = arrangementDetails.category')
                ->where(['arrangementDetails.arrangement' => $id])
                ->andWhere(['arrangementDetails.stockTaking' => 1])
                ->orderBy('category.name'),

            'pagination' => ['pageSize' => false],
            'sort' => false,

        ]);

        return $this->render('stockPrint', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider

        ]);
    }

    /**
     * تقرير الجرد السنوي.
     * السنة تُمرَّر كمعامل مربوط (كانت تُلصق في نص SQL مباشرة، أي ثغرة SQL injection).
     * @return mixed
     */
    public function actionStockTaking()
    {
        $model = new Arrangement();
        if ($model->load(Yii::$app->request->post())) {

            $year = (int) $model->at;
            if ($year < 1900 || $year > 2200) {
                Yii::$app->session->setFlash('error', 'اختر سنة صحيحة (أربعة أرقام).');
                return $this->render('stockTaking', [
                    'model' => $model,
                ]);
            }

            $sql = " select at, category.name, arrangementDetails.quantity, DATE_FORMAT(arrangement.at, '%Y')year,
        category.serialNo, category.company, arrangementDetails.type
        FROM arrangementDetails, arrangement, category
        where arrangement.id=arrangementDetails.arrangement
        and DATE_FORMAT(arrangement.at, '%Y')  =  :year and stockTaking =1
        and arrangementDetails.category = category.id
        order by  arrangement.id";

            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql, [':year' => (string) $year]);
            $info = $data->queryAll();
            if ($info == null) {
                Yii::$app->session->setFlash('warning', 'عفوا لايوجد بيانات للعرض');
                return $this->render('stockTaking', [
                    'model' => $model,
                ]);
            }
            return $this->render('stockTakingRep', [
                'models' => $info,
                'year' => $year,
            ]);
        }

        return $this->render('stockTaking', [
            'model' => $model,
        ]);
    }

    // =====================================================================
    // أدوات داخلية
    // =====================================================================

    /**
     * التعديل المباشر لسطر تسوية: الكمية و/أو النوع فقط. يطبّق فرق الأثر على فرع التسوية.
     * (الأثر = الكمية × النوع، فتعديل الكمية أو النوع يعالجان بنفس المعادلة.)
     */
    private function inlineEditDetail(Arrangement $header)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $key = (int) Yii::$app->request->post('editableKey');
        $all = Yii::$app->request->post('ArrangementDetails');
        $posted = is_array($all) ? current($all) : null;

        $detail = ArrangementDetails::findOne(['id' => $key, 'arrangement' => $header->id]);
        if ($detail === null || !is_array($posted)) {
            return ['output' => '', 'message' => 'السطر غير موجود.'];
        }
        if (!isset($posted['quantity']) && !isset($posted['type'])) {
            return ['output' => '', 'message' => 'يمكن تعديل الكمية أو نوع الحركة فقط.'];
        }

        $newQty = (float) $detail->quantity;
        $newType = (int) $detail->type;
        if (isset($posted['quantity'])) {
            if (!is_numeric($posted['quantity']) || (float) $posted['quantity'] < 0) {
                return ['output' => '', 'message' => 'الكمية يجب أن تكون رقماً صفراً أو أكبر.'];
            }
            $newQty = round((float) $posted['quantity'], 3);
        }
        if (isset($posted['type'])) {
            if (!in_array((int) $posted['type'], [1, -1], true)) {
                return ['output' => '', 'message' => 'نوع الحركة غير صحيح.'];
            }
            $newType = (int) $posted['type'];
        }

        $error = $this->withStockLock(function () use ($header, $detail, $newQty, $newType) {
            $detail->refresh();
            $oldEffect = (float) $detail->quantity * (int) $detail->type;
            $newEffect = $newQty * $newType;

            $this->changeStock($detail->category, $header->branch, $newEffect - $oldEffect, true);

            $detail->quantity = $newQty;
            $detail->type = $newType;
            $detail->save(false);
            return null;
        });

        if ($error !== null) {
            return ['output' => '', 'message' => $error];
        }
        return ['output' => isset($posted['quantity']) ? $detail->quantity : $detail->type, 'message' => ''];
    }

    /** فحوص ما قبل الحفظ (بدون لمس قاعدة البيانات). ترجع نص الخطأ أو null. */
    private function checkNewArrangement(Arrangement $model, array $rows)
    {
        if (!$model->validate()) {
            return implode(' ', array_values($model->getFirstErrors()));
        }
        if (empty($rows)) {
            return 'لا توجد أصناف في قائمة التسوية.';
        }
        foreach ($rows as $data) {
            if (!in_array((int) $data->type, [1, -1], true) || !is_numeric($data->quantity) || (float) $data->quantity < 0) {
                return 'بيانات أحد الأصناف في القائمة غير صحيحة (الكمية أو نوع الحركة). احذفه وأضفه من جديد.';
            }
        }
        return null;
    }

    /**
     * ينفّذ العملية تحت القفل وفي transaction. إن أرجعت $callback نصاً فهو خطأ: يُتراجع عن كل شيء.
     * @return string|null نص الخطأ أو null عند النجاح
     */
    private function withStockLock(callable $callback)
    {
        $db = Yii::$app->db;
        $gotLock = (int) $db->createCommand('SELECT GET_LOCK(:n, 10)', [':n' => self::STOCK_LOCK])->queryScalar() === 1;
        if (!$gotLock) {
            return 'النظام مشغول بعملية مخزون أخرى. أعد المحاولة بعد لحظات.';
        }

        $transaction = $db->beginTransaction();
        try {
            $error = $callback();
            if ($error !== null) {
                $transaction->rollBack();
                return $error;
            }
            $transaction->commit();
            return null;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage() . "\n" . $e->getTraceAsString(), __METHOD__);
            return 'تعذر تنفيذ العملية بسبب خطأ غير متوقع. لم يتغير أي رصيد.';
        } finally {
            $db->createCommand('SELECT RELEASE_LOCK(:n)', [':n' => self::STOCK_LOCK])->queryScalar();
        }
    }

    /**
     * يغيّر رصيد الصنف (النوع 1) في الفرع بفرق موقّع (UPDATE بالقيمة النسبية وليس قراءة ثم كتابة).
     * إن لم يوجد سجل رصيد وكان $createIfMissing: يُنشأ بالقيمة الموقّعة نفسها.
     */
    private function changeStock($category, $branch, $delta, $createIfMissing)
    {
        $delta = round((float) $delta, 3);
        if ($delta == 0.0) {
            return;
        }
        $db = Yii::$app->db;
        $affected = $db->createCommand(
            'UPDATE stocks SET quantity = quantity + :d WHERE category = :c AND branch = :b AND type = 1',
            [':d' => $delta, ':c' => $category, ':b' => $branch]
        )->execute();

        if ($affected === 0 && $createIfMissing) {
            $db->createCommand(
                'INSERT INTO stocks (category, quantity, branch, type) VALUES (:c, :q, :b, 1)',
                [':c' => $category, ':q' => $delta, ':b' => $branch]
            )->execute();
        }
    }
}
