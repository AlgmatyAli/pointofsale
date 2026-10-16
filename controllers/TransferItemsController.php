<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\TransferItems;
use app\models\TransferItemsSearch;
use app\models\TransferItemsDetails;
use yii\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\base\TempTransferItems;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

/**
 * TransferItemsController implements the CRUD actions for TransferItems model.
 *
 * قواعد المخزون (جدول stocks، النوع 1 فقط):
 *  - الحفظ: يزيد رصيد الفرع المستلم ويخصم من الفرع المرسل.
 *  - إضافة صنف / تعديل كمية / حذف سطر داخل شاشة التعديل: تعدّل الفرعين معاً بنفس الفرق.
 *  - الحذف: يعكس كل الأسطر الحالية.
 *  - لا يُسمح بخصم أكثر من رصيد الفرع المرسل، ولا بعكس أكثر من رصيد الفرع المستلم.
 *  - كل عملية مخزون داخل transaction مع قفل MySQL مسمّى واحد (STOCK_LOCK).
 */
class TransferItemsController extends Controller
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
                    'remove' => ['POST'],   // كان رابط GET يحذف السطر؛ الواجهة ترسله POST أصلاً
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'view', 'print'],
                        'roles' => ['createTransferItems'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'view', 'print', 'add', 'remove'],
                        'roles' => ['updateTransferItems'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteTransferItems'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['indexTransferItems'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all TransferItems models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransferItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TransferItems model.
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
     * Creates a new TransferItems model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TransferItems();

        if ($model->load(Yii::$app->request->post())) {
            $userId = (int) Yii::$app->user->identity->id;
            $model->created_at = date('Y-m-d H:i:s');
            $model->user_insert = $userId;

            $items = TempTransferItems::find()->where(['created_by' => $userId])->all();

            $error = $this->checkNewTransfer($model, $items);
            if ($error === null) {
                $error = $this->saveNewTransfer($model, $items, $userId);
            }

            if ($error === null) {
                return $this->redirect(['view', 'id' => $model->id]);
            }

            // الأصناف المؤقتة تبقى كما هي ليصحح المستخدم ويعيد المحاولة
            Yii::$app->session->setFlash('error', 'لم يُحفظ النقل: ' . $error);
            return $this->redirect(['/temp-transfer-items/create']);
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
     * Updates an existing TransferItems model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $dataProvider = new ActiveDataProvider([
            'query' => TransferItemsDetails::find()->where(['=', 'transfer', $id]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
            'pagination' => false,
        ]);

        if (Yii::$app->request->post('hasEditable')) {
            return $this->inlineEditDetail($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            // الأرصدة تحركت عند الحفظ حسب الفرعين؛ تغيير أي منهما الآن يترك الأرصدة غير متسقة
            if (
                (int) $model->fromBranch !== (int) $model->getOldAttribute('fromBranch')
                || (int) $model->toBranch !== (int) $model->getOldAttribute('toBranch')
            ) {
                Yii::$app->session->setFlash('error', 'لا يمكن تغيير الفرع المسحوب منه أو المودع له بعد حفظ النقل. احذف النقل وأنشئ نقلاً جديداً.');
                return $this->redirect(['update', 'id' => $id]);
            }

            $model->update_at = date('Y-m-d H:i:s');
            $model->user_update = Yii::$app->user->identity->id;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Deletes an existing TransferItems model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $transferItems = $this->findModel($id);

        $error = $this->withStockLock(function () use ($transferItems) {
            $details = TransferItemsDetails::find()->where(['transfer' => $transferItems->id])->all();

            // الفرع المستلم يجب أن يملك الكميات التي سنعكسها
            foreach ($this->sumByCategory($details) as $category => $qty) {
                $available = $this->stockQty($category, $transferItems->toBranch);
                if ($qty > $available + 0.0005) {
                    return 'لا يمكن حذف النقل: الفرع المستلم لا يملك الكمية الكافية من الصنف '
                        . $this->categoryName($category) . ' (المتاح ' . $available . ' والمطلوب عكسه ' . $qty . ').';
                }
            }

            foreach ($details as $data) {
                $this->changeStock($data->category, $transferItems->fromBranch, (float) $data->quantity, true);
                $this->changeStock($data->category, $transferItems->toBranch, -(float) $data->quantity, false);
            }

            TransferItemsDetails::deleteAll(['transfer' => $transferItems->id]);
            $transferItems->delete();
            return null;
        });

        if ($error !== null) {
            Yii::$app->session->setFlash('error', $error);
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TransferItems model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TransferItems the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TransferItems::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionPrint($id)
    {

        $dataProvider = new ActiveDataProvider([
            'query' => TransferItemsDetails::find()
                ->select('transferItemsDetails.*, category.name')
                ->leftJoin('category', 'category.id = transferItemsDetails.category')
                ->where(['transferItemsDetails.transfer' => $id])
                ->orderBy('category.name'),

            'pagination' => ['pageSize' => false],
            'sort' => false,
        ]);

        return $this->render('print', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider

        ]);
    }

    /**
     * إضافة وحدة واحدة من الصنف إلى أمر النقل (تستدعيها الواجهة بـ $.get ثم تعيد رسم الجدول).
     * تزيد رصيد الفرع المستلم وتخصم من المرسل، وترفض الإضافة إن لم يكفِ رصيد المرسل.
     */
    public function actionAdd($categoryid, $transfer)
    {
        $transferData = $this->findModel((int) $transfer);
        $categoryid = (int) $categoryid;

        $error = null;
        if (!Category::find()->where(['id' => $categoryid])->exists()) {
            $error = 'الصنف غير موجود.';
        } else {
            $error = $this->withStockLock(function () use ($transferData, $categoryid) {
                $available = $this->stockQty($categoryid, $transferData->fromBranch);
                if ($available < 1) {
                    return 'رصيد الفرع المسحوب منه من الصنف ' . $this->categoryName($categoryid) . ' غير كافٍ (المتاح ' . $available . ').';
                }

                $temp = TransferItemsDetails::find()->where([
                    'category' => $categoryid,
                    'transfer' => $transferData->id,
                ])->one();

                if ($temp === null) {
                    $temp = new TransferItemsDetails();
                    $temp->transfer = $transferData->id;
                    $temp->category = $categoryid;
                    $temp->quantity = 1;
                } else {
                    $temp->quantity = $temp->quantity + 1;
                }
                $temp->save(false);

                $this->changeStock($categoryid, $transferData->toBranch, 1, true);
                $this->changeStock($categoryid, $transferData->fromBranch, -1, false);
                return null;
            });
        }

        if (Yii::$app->request->isAjax) {
            if ($error !== null) {
                // تُظهر الواجهة رسالة الخطأ العامة عند فشل الطلب
                throw new BadRequestHttpException($error);
            }
            return 'ok';
        }

        if ($error !== null) {
            Yii::$app->session->setFlash('error', $error);
        }
        return $this->redirect(['transfer-items/update', 'id' => $transferData->id]);
    }

    /**
     * حذف سطر من أمر النقل مع عكس أثره على الفرعين (POST).
     */
    public function actionRemove($id)
    {
        $details = TransferItemsDetails::findOne((int) $id);
        if ($details === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $transferData = $this->findModel($details->transfer);

        $error = $this->withStockLock(function () use ($details, $transferData) {
            $qty = (float) $details->quantity;
            $available = $this->stockQty($details->category, $transferData->toBranch);
            if ($qty > $available + 0.0005) {
                return 'لا يمكن حذف السطر: الفرع المستلم لا يملك الكمية الكافية من الصنف '
                    . $this->categoryName($details->category) . ' (المتاح ' . $available . ').';
            }

            $this->changeStock($details->category, $transferData->fromBranch, $qty, true);
            $this->changeStock($details->category, $transferData->toBranch, -$qty, false);
            $details->delete();
            return null;
        });

        if ($error !== null) {
            Yii::$app->session->setFlash('error', $error);
        }
        return $this->redirect(['update', 'id' => $transferData->id]);
    }

    // =====================================================================
    // أدوات داخلية
    // =====================================================================

    /**
     * التعديل المباشر لكمية سطر داخل الجدول. الكمية فقط؛ تغيير الصنف غير مسموح.
     */
    private function inlineEditDetail(TransferItems $header)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $key = (int) Yii::$app->request->post('editableKey');
        $all = Yii::$app->request->post('TransferItemsDetails');
        $posted = is_array($all) ? current($all) : null;

        $detail = TransferItemsDetails::findOne(['id' => $key, 'transfer' => $header->id]);
        if ($detail === null || !is_array($posted)) {
            return ['output' => '', 'message' => 'السطر غير موجود.'];
        }
        if (isset($posted['category'])) {
            return ['output' => '', 'message' => 'لا يمكن تغيير الصنف. احذف السطر وأضف الصنف الصحيح.'];
        }
        if (!isset($posted['quantity']) || !is_numeric($posted['quantity']) || (float) $posted['quantity'] <= 0) {
            return ['output' => '', 'message' => 'الكمية يجب أن تكون رقماً أكبر من صفر.'];
        }
        $newQty = round((float) $posted['quantity'], 3);

        $error = $this->withStockLock(function () use ($header, $detail, $newQty) {
            $detail->refresh();
            $delta = round($newQty - (float) $detail->quantity, 3);

            if ($delta > 0) {
                $available = $this->stockQty($detail->category, $header->fromBranch);
                if ($delta > $available + 0.0005) {
                    return 'رصيد الفرع المسحوب منه غير كافٍ (المتاح ' . $available . ').';
                }
            } elseif ($delta < 0) {
                $available = $this->stockQty($detail->category, $header->toBranch);
                if (-$delta > $available + 0.0005) {
                    return 'الفرع المستلم لا يملك الكمية الكافية لتخفيض النقل (المتاح ' . $available . ').';
                }
            }

            $this->changeStock($detail->category, $header->toBranch, $delta, $delta > 0);
            $this->changeStock($detail->category, $header->fromBranch, -$delta, $delta < 0);

            $detail->quantity = $newQty;
            $detail->save(false);
            return null;
        });

        if ($error !== null) {
            return ['output' => '', 'message' => $error];
        }
        return ['output' => $detail->quantity, 'message' => ''];
    }

    /**
     * فحوص ما قبل الحفظ (بدون لمس قاعدة البيانات). ترجع نص الخطأ أو null.
     */
    private function checkNewTransfer(TransferItems $model, array $items)
    {
        if (!$model->validate()) {
            return implode(' ', array_values($model->getFirstErrors()));
        }
        if (empty($model->fromBranch) || empty($model->toBranch)) {
            return 'اختر الفرع المسحوب منه والفرع المودع له.';
        }
        if ((int) $model->fromBranch === (int) $model->toBranch) {
            return 'الفرع المسحوب منه والمودع له متطابقان.';
        }
        if (empty($items)) {
            return 'لا توجد أصناف في قائمة النقل.';
        }
        foreach ($items as $item) {
            if (!is_numeric($item->quantity) || (float) $item->quantity <= 0) {
                return 'كمية الصنف ' . $this->categoryName($item->category) . ' غير صحيحة.';
            }
        }
        return null;
    }

    /**
     * حفظ النقل وتحريك الأرصدة في transaction واحدة تحت القفل. ترجع نص الخطأ أو null.
     */
    private function saveNewTransfer(TransferItems $model, array $items, $userId)
    {
        return $this->withStockLock(function () use ($model, $items, $userId) {
            // الرصيد وقت الحفظ (وليس وقت الإضافة للقائمة المؤقتة)
            foreach ($this->sumByCategory($items) as $category => $qty) {
                $available = $this->stockQty($category, $model->fromBranch);
                if ($qty > $available + 0.0005) {
                    return 'رصيد الصنف ' . $this->categoryName($category) . ' في الفرع المسحوب منه غير كافٍ (المتاح ' . $available . ' والمطلوب ' . $qty . ').';
                }
            }

            if (!$model->save()) {
                return implode(' ', array_values($model->getFirstErrors()));
            }

            foreach ($items as $value) {
                $details = new TransferItemsDetails();
                $details->transfer = $model->id;
                $details->category = $value->category;
                $details->quantity = $value->quantity;
                $details->save(false);

                $this->changeStock($value->category, $model->toBranch, (float) $value->quantity, true);
                $this->changeStock($value->category, $model->fromBranch, -(float) $value->quantity, false);
            }

            TempTransferItems::deleteAll(['created_by' => $userId]);
            return null;
        });
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

    /** رصيد الصنف في الفرع (النوع 1) */
    private function stockQty($category, $branch)
    {
        return (float) Yii::$app->db->createCommand(
            'SELECT COALESCE(SUM(quantity), 0) FROM stocks WHERE category = :c AND branch = :b AND type = 1',
            [':c' => $category, ':b' => $branch]
        )->queryScalar();
    }

    /**
     * يغيّر رصيد الصنف في الفرع بفرق موقّع (UPDATE بالقيمة النسبية وليس قراءة ثم كتابة).
     * إن لم يوجد سجل رصيد: يُنشأ عند $createIfMissing، وإلا لا شيء (لا يمكن الخصم من رصيد غير موجود).
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

    /** مجموع الكميات لكل صنف (قد يتكرر الصنف في عدة أسطر) */
    private function sumByCategory(array $rows)
    {
        $sum = [];
        foreach ($rows as $row) {
            $sum[$row->category] = round(($sum[$row->category] ?? 0) + (float) $row->quantity, 3);
        }
        return $sum;
    }

    private function categoryName($id)
    {
        $category = Category::findOne($id);
        return $category !== null ? $category->name : ('#' . $id);
    }
}
