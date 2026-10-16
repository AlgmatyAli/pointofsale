<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\TempTransferItemsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\base\TempTransferItems;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * TempTransferItemsController implements the CRUD actions for TempTransferItems model.
 * (قائمة النقل المؤقتة لكل مستخدم قبل الضغط على «حفظ»؛ لا تحرّك المخزون.)
 */
class TempTransferItemsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-all' => ['POST'],   // الواجهة ترسله POST أصلاً
                ],
            ],

            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'view', 'delete-all', 'delete'],
                        'roles' => ['createTransferItems'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all TempTransferItems models.
     * (الإجراء غير مسموح في قواعد الوصول أعلاه؛ أُزيل منه كود التعديل المباشر الميت الذي كان ينتهي بـ die.)
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TempTransferItemsSearch();
        $searchModel->created_by = Yii::$app->user->identity->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [

            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TempTransferItems model.
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
     * Creates a new TempTransferItems model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TempTransferItems();
        $searchModel = new TempTransferItemsSearch();
        $userId = (int) Yii::$app->user->identity->id;

        $searchModel->created_by = $userId;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->post('hasEditable')) {
            return $this->inlineEdit($userId);
        }

        if ($model->loadAll(Yii::$app->request->post())) {
            $category = (int) $model->category;
            $branch = (int) $model->branch;
            $quantity = abs((float) $model->quantity);

            if ($category <= 0 || !Category::find()->where(['id' => $category])->exists()) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry You Can not Add Empty Model"));
                return $this->redirect(['create']);
            }
            if ($branch <= 0 || !(new Query())->from('branches')->where(['id' => $branch])->exists()) {
                Yii::$app->session->setFlash('error', 'الفرع المسحوب منه غير صحيح. اختر الصنف من القائمة من جديد.');
                return $this->redirect(['create']);
            }
            if ($quantity <= 0) {
                Yii::$app->session->setFlash('error', 'الكمية يجب أن تكون أكبر من صفر.');
                return $this->redirect(['create']);
            }

            $stockQty = $this->stockQty($category, $branch);
            if ($stockQty <= 0) {
                Yii::$app->session->setFlash('error', Yii::t('app', "عفوا كمية هذا الصنف صفر لايمكنك الاستمرار"));
                return $this->redirect(['create']);
            }

            // ما أُضيف سابقاً لنفس الصنف والفرع في قائمة هذا المستخدم
            $sumQnty = (float) TempTransferItems::find()
                ->where(['category' => $category, 'created_by' => $userId])
                ->sum('quantity');

            $balance = $stockQty - $sumQnty;
            if ($balance <= 0) {
                Yii::$app->session->setFlash('error', "عفوا لقد تجاوزت الكمية الموجودة لايمكنك الاستمرار");
                return $this->redirect(['create']);
            }

            $model->category = $category;
            $model->branch = $branch;
            $model->quantity = ($quantity > $balance) ? $balance : $quantity;
            $model->created_by = $userId;
            $model->created_at = date('Y-m-d');

            // رقم السجل يدوي (max+1): القفل يمنع تكراره عند الإضافة المتزامنة
            $db = Yii::$app->db;
            $gotLock = (int) $db->createCommand('SELECT GET_LOCK(:n, 5)', [':n' => 'pos_temp_transfer_id'])->queryScalar() === 1;
            if (!$gotLock) {
                Yii::$app->session->setFlash('error', 'النظام مشغول. أعد المحاولة.');
                return $this->redirect(['create']);
            }
            try {
                $model->id = (int) TempTransferItems::find()->max('id') + 1;
                $model->save(false);
            } finally {
                $db->createCommand('SELECT RELEASE_LOCK(:n)', [':n' => 'pos_temp_transfer_id'])->queryScalar();
            }

            return $this->redirect(['create']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Updates an existing TempTransferItems model.
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
     * Deletes an existing TempTransferItems model.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteWithRelated();

        return $this->redirect(['create']);
    }


    /**
     * Finds the TempTransferItems model based on its primary key value.
     * يُقيَّد بسجلات المستخدم الحالي فقط (كان أي مستخدم يستطيع فتح أو حذف سجلات غيره بالرقم).
     * @param integer $id
     * @return TempTransferItems the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = TempTransferItems::findOne(['id' => (int) $id, 'created_by' => Yii::$app->user->identity->id]);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionDeleteAll()
    {
        TempTransferItems::deleteAll(['created_by' => Yii::$app->user->identity->id]);
        return $this->redirect(['create']);
    }

    // =====================================================================
    // أدوات داخلية
    // =====================================================================

    /**
     * التعديل المباشر لكمية سطر في القائمة المؤقتة.
     * الكمية يجب أن تكون أكبر من صفر ولا تتجاوز الرصيد المتاح (بعد خصم بقية أسطر نفس الصنف والفرع).
     */
    private function inlineEdit($userId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $key = (int) Yii::$app->request->post('editableKey');
        $all = Yii::$app->request->post('TempTransferItems');
        $posted = is_array($all) ? current($all) : null;

        $result = TempTransferItems::findOne(['id' => $key, 'created_by' => $userId]);
        if ($result === null || !is_array($posted)) {
            return ['output' => '', 'message' => 'السطر غير موجود.'];
        }
        if (isset($posted['category']) || isset($posted['branch'])) {
            return ['output' => '', 'message' => 'لا يمكن تغيير الصنف أو الفرع. احذف السطر وأضف من جديد.'];
        }
        if (!isset($posted['quantity']) || !is_numeric($posted['quantity']) || (float) $posted['quantity'] <= 0) {
            return ['output' => '', 'message' => 'الكمية يجب أن تكون رقماً أكبر من صفر.'];
        }

        // الفرع هنا فرع السطر نفسه (كان الكود يقرأ فرع كائن جديد فارغ فيفشل الفحص)
        $stockQty = $this->stockQty($result->category, Yii::$app->user->identity->branch);

        $others = (float) TempTransferItems::find()
            ->where(['category' => $result->category, 'created_by' => $userId])
            ->andWhere(['!=', 'id', $result->id])
            ->sum('quantity');

        $balance = $stockQty - $others;
        if ($balance <= 0) {
            return ['output' => '', 'message' => 'لا يوجد رصيد متاح لهذا الصنف.'];
        }

        $result->quantity = min((float) $posted['quantity'], $balance);
        $result->save(false);

        return ['output' => $result->quantity, 'message' => ''];
    }

    /** رصيد الصنف في الفرع (النوع 1 فقط، وهو الذي يتحرك فعلياً عند حفظ النقل) */
    private function stockQty($category, $branch)
    {
        return (float) Yii::$app->db->createCommand(
            'SELECT COALESCE(SUM(quantity), 0) FROM stocks WHERE category = :c AND branch = :b AND type = 1',
            [':c' => $category, ':b' => $branch]
        )->queryScalar();
    }
}
