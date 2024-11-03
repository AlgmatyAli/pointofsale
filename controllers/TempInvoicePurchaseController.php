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

/**
 * TempInvoicePurchaseController implements the CRUD actions for TempInvoicePurchase model.
 */
class TempInvoicePurchaseController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
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
            $id = Yii::$app->request->post('editableKey');
            $result = TempInvoicePurchase::findOne($id);
            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['TempInvoicePurchase']);
            $post['TempInvoicePurchase'] = $posted;
            if ($result->load($post)) {
                $result->save(false);
                if (isset($posted['quantity'])) {
                    $outMessage = $result->quantity;
                } else {
                    $outMessage = $result->salePrice;
                }

                $output = $outMessage;

                $out = Json::encode(['output' => $output]);

                return $out;
            }
        }

        //        if ($model->loadAll(Yii::$app->request->post()) ) {
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->category == null) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry You Can not Add Empty Model"));
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }

            $id = TempInvoicePurchase::find()->max('id') + 1;
            $model->id = $id;

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

            $model->saveAll();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true, 'message' => Yii::t('app', "تمت اضافة الصنف")];
            // $totalInvoice = $model->totalInvoice;
            // $totalCost = $model->totalCost;
            // $rate = $model->rate;
            // $derhamRate = $model->derhamRate;

            // return $this->redirect(['create',
            // 'id' => 1,
            // 'totalCost' => $totalCost,
            // 'totalInvoice' => $totalInvoice,
            // 'rate' => $rate,
            // 'derhamRate' => $derhamRate
            // ]);

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
        if (($model = TempInvoicePurchase::findOne($id)) !== null) {
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
        Yii::$app->response->format = Yii\web\Response::FORMAT_JSON;
        return $data;
        //    echo json::encode($data);
    }

    public function actionUpload()
    {
        $model = new TempInvoicePurchase();

        if ($model->load(Yii::$app->request->post())) {

            if (!is_dir("img/upload")) {
                mkdir("img/upload");
                $path = "img/upload";
            } else {
                $path = "img/upload";
            }

            $model->file = UploadedFile::getInstance($model, 'file');
            $ext = substr(strrchr($model->file, '.'), 1);
            if ($ext != null) {
                $uniqid = uniqid();
                $model->file->saveAs($path . '/' . $uniqid . '.' . $model->file->extension);
                $model->path = $path . '/' . $uniqid . '.' . $model->file->extension;
            }
            //=========
            $inputFile = $path . '/' . $uniqid . '.' . $model->file->extension;
            //die(var_dump( $inputFile));     
            try {
                $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
                $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFile);
            } catch (Exception $e) {
                die('Erorr');
            }
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            for ($row = 1; $row <= $highestRow; $row++) {

                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                if ($row == 1) {
                    continue;
                }

                $id = TempInvoicePurchase::find()->max('id') + 1;
                $workSheet =   new  TempInvoicePurchase();
                $workSheet->id = $id;
                $workSheet->category = $rowData[0][1];
                $workSheet->quantity = $rowData[0][5];
                $workSheet->costPrice = $rowData[0][6];
                $workSheet->costTotal = $rowData[0][6];
                $workSheet->salePrice = 0;
                $workSheet->salePrice_ = 0;
                $workSheet->box = 1;

                $workSheet->created_by = Yii::$app->user->identity->id;
                $workSheet->created_at = date('Y-m-d H:i:s');
                $workSheet->updated_by = Yii::$app->user->identity->id;;
                $workSheet->updated_at = date('Y-m-d H:i:s');
                $workSheet->state = 0;

                $workSheet->save(false);
            }

            return $this->redirect([
                'create',
                'id' => 1,
                'totalCost' => null,
                'totalInvoice' => null,
                'rate' => null,
                'derhamRate' => null
            ]);

            //return $this->redirect(['create', 'id' => $model->id]);
        }

        return $this->render('upload', [
            'model' => $model,
        ]);
    }
}
