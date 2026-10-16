<?php

namespace app\controllers;

use Yii;
use app\models\TempInvoicePurchase;
use app\models\TempInvoicePurchaseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use app\models\Stocks;
use yii\filters\AccessControl;
use app\models\Totalinventory;
use Exception;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use app\models\Category;

/**
 * TempInvoicePurchaseController implements the CRUD actions for TempInvoicePurchase model.
 */
class TempInvoicePurchaseController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'view', 'get-inv', 'delete', 'delete-all', 'temp-back-sales/itemlist', 'upload', 'update'],
                        'roles' => ['createPurchases'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all TempInvoicePurchase models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TempInvoicePurchaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->post('hasEditable')) {
            $model = new TempInvoicePurchase();
            $bookId = Yii::$app->request->post('editableKey');
            $model = TempInvoicePurchase::findOne($bookId);

            $post = [];
            $posted = current($_POST['TempInvoicePurchase']);
            $post['TempInvoicePurchase'] = $posted;
            // Load model like any single model validation
            if ($model->load($post)) {
                // When doing $result = $model->save(); I get a return value of false
                if ($model->save()) {
                    if (isset($posted['quantity'])) {
                        $output = $model->quantity;
                    }
                    $out = Json::encode(['output' => $output, 'message' => '']);
                }
            }
            // Return AJAX JSON encoded response and exit
            echo $out;
            die; // write die instead of fo return and check it.
        }
        return $this->render('index', [

            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TempInvoicePurchase model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TempInvoicePurchase model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TempInvoicePurchase();

        $searchModel = new TempInvoicePurchaseSearch();

        $searchModel->created_by = Yii::$app->user->identity->id;
        $searchModel->state = 0;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (yii::$app->request->post('hasEditable')) {
            // تعديل داخل الجدول: أسطر المستخدم نفسه فقط، وحقول محددة فقط، وبقيم رقمية صحيحة
            $key = Yii::$app->request->post('editableKey');
            $result = TempInvoicePurchase::findOne(['id' => $key, 'created_by' => Yii::$app->user->id, 'state' => 0]);
            if ($result === null) {
                return Json::encode(['output' => '', 'message' => 'السطر غير موجود']);
            }

            $posted = current((array)Yii::$app->request->post('TempInvoicePurchase', []));
            $posted = array_intersect_key((array)$posted, array_flip(['costPrice', 'quantity', 'costTotal', 'salePrice', 'salePrice_']));
            if (!$posted) {
                return Json::encode(['output' => '', 'message' => 'حقل غير مسموح بتعديله']);
            }
            foreach ($posted as $attr => $val) {
                if (!is_numeric($val) || $val < 0 || ($attr === 'quantity' && $val <= 0)) {
                    return Json::encode(['output' => '', 'message' => 'الرجاء إدخال قيمة رقمية صحيحة']);
                }
            }

            $result->load(['TempInvoicePurchase' => $posted]);
            $result->save(false);
            $attr = key($posted);

            return Json::encode(['output' => $result->$attr]);
        }

        //        if ($model->loadAll(Yii::$app->request->post()) ) {
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->category == null) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry You Can not Add Empty Model"));
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }
            if ($model->quantity <= 0 || $model->costPrice < 0 || $model->salePrice < 0) {
                $msg = 'الكمية يجب أن تكون أكبر من صفر، والأسعار لا تكون سالبة';
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['success' => false, 'message' => $msg];
                }
                Yii::$app->session->setFlash('error', $msg);
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }

            // الحالة والمستخدم يحدّدهما الخادم (كانا يُقبلان من المتصفح)
            $model->state = 0;

            if ($model->rate != 0) {
                $model->costTotal = ($model->costPrice * $model->rate);
                //$model->costPrice = ($model->costPrice / $model->derhamRate);
            } else {
                $model->costTotal =  $model->costPrice;
            }

            if ($model->derhamRate != 0) {
                $model->costTotal = ($model->costTotal / $model->derhamRate);
                $model->costPrice = ($model->costPrice / $model->derhamRate);
            }

            // الرقم يُحسب يدوياً (max + 1) فيلزم قفل قصير كي لا يأخذ مستخدمان الرقم نفسه
            $lockName = 'temp_invoice_purchase_id';
            $db = Yii::$app->db;
            if ((int)$db->createCommand('SELECT GET_LOCK(:n, 10)', [':n' => $lockName])->queryScalar() !== 1) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'النظام مشغول حالياً، أعد المحاولة'];
            }
            try {
                $model->id = (int)TempInvoicePurchase::find()->max('id') + 1;
                $saved = $model->saveAll();
            } finally {
                $db->createCommand('SELECT RELEASE_LOCK(:n)', [':n' => $lockName])->execute();
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!$saved) {
                return ['success' => false, 'message' => implode(' ', $model->getErrorSummary(true))];
            }
            return ['success' => true, 'message' => Yii::t('app', "تمت اضافة الصنف")];

        } else {
            return $this->render('create', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'totalCost' => null,
                'totalInvoice' => null,
                'rate' => null,
                'derhamRate' => null
            ]);
        }
    }

    /**
     * Updates an existing TempInvoicePurchase model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->request->post('_asnew') == '1') {
            $model = new TempInvoicePurchase();
        } else {
            $model = $this->findModel($id);
        }

        if ($model->loadAll(Yii::$app->request->post())) {
            $model->saveAll(false);
            // Yii::$app->response->format = Response::FORMAT_JSON;
            // return ['success' => true, 'message' => Yii::t('app', "تمت اضافة الصنف")];
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TempInvoicePurchase model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteWithRelated();
        return $this->redirect(['create']);
    }

    public function actionDeleteAll()
    {
        $total = TempInvoicePurchase::find()->where('created_by	=' . Yii::$app->user->identity->id)->andWhere('state =0')->sum('costPrice*quantity');

        if ($total == 0) {
            Yii::$app->session->setFlash('error', Yii::t('app', "Sorry There is no Items at Invoice"));
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }

        TempInvoicePurchase::deleteAll(['created_by' => Yii::$app->user->identity->id, 'state' => '0']);
        return $this->redirect(['create']);
    }


    /**
     * Finds the TempInvoicePurchase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TempInvoicePurchase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        // أسطر المستخدم الحالي فقط (كان يمكن عرض أو تعديل أو حذف أسطر أي مستخدم آخر)
        if (($model = TempInvoicePurchase::findOne(['id' => $id, 'created_by' => Yii::$app->user->id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    public function actionGetInv($category)
    {
        if (Yii::$app->user->Identity->seeOtherBranchQ == 0) {
            $whereBranch = 'branch=' . Yii::$app->user->Identity->branch;
        } else {
            $whereBranch = 'branch in (1,2,3,4,5,6,7,8,9)';
        }

        $data = Stocks::find()
            ->leftJoin('category', 'stocks.category = category.id')
            ->leftJoin('prices', 'category.id = prices.category')
            ->select(['stocks.category', 'stocks.quantity as quantity', 'costPrice', 'prices.minPrice', 'prices.maxPrice', 'prices.minPrice2', 'prices.minPrice3'])
            // ->andWhere($whereBranch)
            ->andwhere(['stocks.category' => $category])
            ->andwhere(['=', 'stocks.type', 1])
            ->asArray()->one();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
        //    echo json::encode($data);
    }

    public function actionUpload()
    {
        $model = new TempInvoicePurchase();

        if ($model->load(Yii::$app->request->post())) {

            $file = UploadedFile::getInstance($model, 'file');
            if ($file === null || !in_array(strtolower($file->extension), ['xls', 'xlsx'], true)) {
                Yii::$app->session->setFlash('error', 'الرجاء اختيار ملف Excel بصيغة xls أو xlsx');
                return $this->render('upload', ['model' => $model]);
            }

            // الملف يُحفظ خارج المجلد العام (لا يمكن فتحه من المتصفح) باسم عشوائي وامتداد مسموح فقط، ويُحذف بعد الاستيراد
            $dir = Yii::getAlias('@runtime/uploads');
            FileHelper::createDirectory($dir);
            $inputFile = $dir . '/' . Yii::$app->security->generateRandomString(16) . '.' . strtolower($file->extension);
            $file->saveAs($inputFile, false);

            $lockName = 'temp_invoice_purchase_id';
            $db = Yii::$app->db;
            $locked = false;
            try {
                $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
                $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFile);
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // قراءة الصفوف والتحقق منها كلها قبل حفظ أي شيء
                $rows = [];
                $errors = [];
                for ($row = 2; $row <= $highestRow; $row++) {
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                    $category = $rowData[0][1] ?? null;
                    $quantity = $rowData[0][5] ?? null;
                    $cost     = $rowData[0][6] ?? null;
                    if ($category === null && $quantity === null && $cost === null) {
                        continue;   // صف فارغ
                    }
                    if (!is_numeric($category) || !is_numeric($quantity) || $quantity <= 0 || !is_numeric($cost) || $cost < 0) {
                        $errors[] = "السطر $row: بيانات غير صالحة";
                        continue;
                    }
                    $rows[$row] = [(int)$category, $quantity, $cost];
                }
                if ($rows) {
                    $existing = Category::find()->select('id')->where(['id' => array_unique(array_column($rows, 0))])->column();
                    foreach ($rows as $row => $r) {
                        if (!in_array($r[0], array_map('intval', $existing), true)) {
                            $errors[] = "السطر $row: الصنف رقم {$r[0]} غير موجود";
                        }
                    }
                }
                if ($errors) {
                    Yii::$app->session->setFlash('error', 'لم يُستورد شيء. ' . implode(' | ', array_slice($errors, 0, 10)));
                    return $this->render('upload', ['model' => $model]);
                }
                if (!$rows) {
                    Yii::$app->session->setFlash('error', 'الملف لا يحتوي أصنافاً');
                    return $this->render('upload', ['model' => $model]);
                }

                $locked = (int)$db->createCommand('SELECT GET_LOCK(:n, 10)', [':n' => $lockName])->queryScalar() === 1;
                if (!$locked) {
                    Yii::$app->session->setFlash('error', 'النظام مشغول حالياً، أعد المحاولة');
                    return $this->render('upload', ['model' => $model]);
                }

                $transaction = $db->beginTransaction();
                try {
                    $id = (int)TempInvoicePurchase::find()->max('id');
                    foreach ($rows as $r) {
                        $workSheet = new TempInvoicePurchase();
                        $workSheet->id = ++$id;
                        $workSheet->category = $r[0];
                        $workSheet->quantity = $r[1];
                        $workSheet->costPrice = $r[2];
                        $workSheet->costTotal = $r[2];
                        $workSheet->salePrice = 0;
                        $workSheet->salePrice_ = 0;
                        $workSheet->box = 1;

                        $workSheet->created_by = Yii::$app->user->identity->id;
                        $workSheet->created_at = date('Y-m-d H:i:s');
                        $workSheet->updated_by = Yii::$app->user->identity->id;
                        $workSheet->updated_at = date('Y-m-d H:i:s');
                        $workSheet->state = 0;

                        $workSheet->save(false);
                    }
                    $transaction->commit();
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            } catch (\Throwable $e) {
                Yii::error($e->getMessage(), __METHOD__);
                Yii::$app->session->setFlash('error', 'تعذّر قراءة الملف أو استيراده، ولم يُضف شيء: ' . $e->getMessage());
                return $this->render('upload', ['model' => $model]);
            } finally {
                if ($locked) {
                    $db->createCommand('SELECT RELEASE_LOCK(:n)', [':n' => $lockName])->execute();
                }
                @unlink($inputFile);
            }

            return $this->redirect([
                'create',
                'id' => 1,
                'totalCost' => null,
                'totalInvoice' => null,
                'rate' => null,
                'derhamRate' => null
            ]);
        }

        return $this->render('upload', [
            'model' => $model,
        ]);
    }

}
