<?php

namespace app\controllers;

use Yii;
use app\models\TempInvoice;
use app\models\TempInvoiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use app\models\Category;
use app\models\Inventory;
use yii\data\ActiveDataProvider;
use app\models\Totalinventory;
use yii\db\Query;
use yii\filters\AccessControl;
use app\models\CompanyInfo;
use app\models\Stocks;
use yii\web\Response;

/**
 * TempInvoiceController implements the CRUD actions for TempInvoice model.
 */
class TempInvoiceController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'view', 'get-inv', 'delete', 'updatqyt', 'deleteing',
                        'save', 'delete-all', 'create-ajax', 'createfast', 'hold', 'holded', 'fast',
                        'unhold', 'add', 'ajax-comment'],
                        'roles' => ['createSales'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all TempInvoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TempInvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (yii::$app->request->post('hasEditable')) {

            $id = Yii::$app->request->post('editableKey');

            $result = TempInvoice::findOne($id);


            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['TempInvoice']);

            $post['TempInvoice'] = $posted;
            if ($result->load($post)) {
                $result->save(false);

                $output = ($result->quantity);

                $out = Json::encode(['output' => $output]);

                return $out;
            }
            return;
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TempInvoice model.
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
     * Creates a new TempInvoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $company = CompanyInfo::find()->one();
        $model = new TempInvoice();
        if (Yii::$app->user->can('saleOnHoldItems')) {
            $type = [1, 2, 3];
        } else {
            $type = [1, 2, 3];
        }
       
        $searchModel = new TempInvoiceSearch();
        $searchModel->created_by = Yii::$app->user->identity->id;
        $searchModel->state = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result = TempInvoice::findOne($id);

            Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['TempInvoice']);

            $post['TempInvoice'] = $posted;
            if ($result->load($post)) {
                if(Yii::$app->user->identity->seeOtherBranchQ == 0){
                    $branch = Yii::$app->user->identity->branch;
                }else{
                    $branch = [1, 2, 3];
                }
                $data = Stocks::find()
                ->select(['stocks.category', 'stocks.quantity as quantity', 'prices.costPrice',
                          'prices.minPrice', 'prices.maxPrice'])
                ->leftJoin('prices', 'stocks.category = prices.category')
                    ->Where(['stocks.branch' => Yii::$app->user->identity->branch])
                    ->andwhere(['stocks.category' => $result->category])
                    ->andwhere(['in', 'stocks.type', $type])
                    ->one();

                if (isset($posted['quantity'])) {
                    if ($data->quantity < $result->quantity) {
                        $result->quantity = $data->quantity;
                    }
                    $outMessage = $result->quantity;
                    $result->save(false);
                } elseif (isset($posted['serial_number'])) {
                    $outMessage = $result->serial_number;
                } else {
                    if (Yii::$app->user->can('selling_by_costprice')) {
                        if ($data->costPrice > $result->salePrice) {
                            $result->salePrice = $data->costPrice;
                        }
                    } elseif ($data->minPrice > $result->salePrice) {
                        $result->salePrice = $data->minPrice;
                    }
                    if (Yii::$app->user->identity->client <> null) {
                        $result->waitQnty  = 0;
                    }
                    $outMessage = $result->salePrice;
                    $result->save(false);
                }
                $output = $outMessage;
                $out = Json::encode(['output' => $output]);
                return $out;
            }
        }
        /**
         * في الاعلي اذا كان سعر الكمية المطلوبه أكبر من الكمية الموجوده يتم تغيير الكيه حسب الموجود فقط
         *
         * في حالة محاولة تعديل سعر البيع، يتم النظر اذا كان السعر اصغر من اقل سعر بيع تقوم المنضومه بتغيير السعر
         * الي اقل سعر بيع ولا يمكن البيع باقل منه
         */
         //else {
            // Code to handle non-Ajax request
        //}
         if ($model->load(Yii::$app->request->post()) && $model->validate()) {
             if(Yii::$app->user->identity->seeOtherBranchQ == 0){
                 $branch = Yii::$app->user->identity->branch;
             }else{
                 $branch = [1, 2, 3];
             }
        $item = Stocks::find()
        ->select(['stocks.category as id', 'stocks.quantity as quantity',
                  'prices.costPrice', 'prices.minPrice', 'prices.maxPrice'])
            ->leftJoin('prices', 'stocks.category = prices.category')
            ->where(['stocks.category' => $model->category])
            ->andWhere(['<>', 'stocks.quantity', 0])
            ->andwhere(['in', 'stocks.type', $type])
            ->andWhere(['in', 'stocks.branch', $branch])
            ->one();

        if ($company->repeatCategory == 0) {
            
            $exist = TempInvoice::find()
                ->where(['category' => $model->category, 'created_by' => Yii::$app->user->identity->id,])
                ->andWhere(['=', 'state', 1])
                ->one();
            if ($exist) {
                
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['error' => true, 'message' =>  Yii::t('app', "Sorry The Item You Insert already exists")];
            }
        }
        
        if ($model->quantity <= 0) {
           Yii::$app->response->format = Response::FORMAT_JSON;
           return ['error' => true, 'message' => Yii::t('app', "Sorry You Enter Zero Value As Quantity")];
        }
        
        if ($model->salePrice <= 0) {
            Yii::$app->response->format = Response::FORMAT_JSON;
           return ['error' => true, 'message' => Yii::t('app', "Sorry You Enter Zero Value As Sale Price")];
        }
        
        if ($model->quantity > $item->quantity) {
            $model->quantity = $item->quantity;
        }
       
        if ($model->salePrice <= 0) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['error' => true, 'message' => Yii::t('app', "Sorry The SalePrice
            You Enter Less Than The MinPrice")];
        }
        if ($model->salePrice < $item->costPrice) {
            echo '<script type="text/javascript">
            alert("عفوا سعر البيع الذي ادخلته اقل من سعر التكلفة");
           </script>';
           exit;
        } else {
            $model->category = $item->id;
            $model->costPrice = $item->costPrice;
            $model->box = 1;
            $model->state = 1;
            $model->created_by = Yii::$app->user->identity->id;
            $model->created_at = 
            $model->saveAll();
        }
        // increment the counter
         $counter = TempInvoice::find()
         ->where(['=','created_by', Yii::$app->user->identity->id])
         ->andWhere(['=','state', 1])
         ->count();
         Yii::$app->session->set('submitCounter', $counter);
         // return the response
         Yii::$app->response->format = Response::FORMAT_JSON;
         return ['success' => true, 'counter' => $counter];
         }else {
            return $this->render('create', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'company' => $company,
            ]);
         }
    }

    public function actionCreatefast()
    {
        $company = CompanyInfo::find()->one();
        $model = new TempInvoice();
        if (Yii::$app->user->can('saleOnHoldItems')) {
            $type = [1, 2, 3];
        } else {
            $type = [1, 2];
        }

        $data = Totalinventory::find()
            ->Where(['branch' => Yii::$app->user->identity->branch])
            ->andwhere(['in', 'type', $type])
            ->all();

        $searchModel = new TempInvoiceSearch();
        $searchModel->created_by = Yii::$app->user->identity->id;
        $searchModel->state = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        /**
         * في الاعلي اذا كان سعر الكمية المطلوبه أكبر من الكمية الموجوده يتم تغيير الكيه حسب الموجود فقط 
         * 
         * في حالة محاولة تعديل سعر البيع، يتم النظر اذا كان السعر اصغر من اقل سعر بيع تقوم المنضومه بتغيير السعر 
         * الي اقل سعر بيع ولا يمكن البيع باقل منه
         */

        if ($model->loadAll(Yii::$app->request->post())) {
            $item = Totalinventory::find()
                ->where(['id' => $model->category])->one();

            if ($company->repeatCategory == 0) {
                $exist = TempInvoice::find()->where([
                    'category' => $model->category,
                    'created_by' => Yii::$app->user->identity->id,
                ])->one();

                if ($exist) {
                    Yii::$app->session->setFlash('error', Yii::t('app', "Sorry The Item You Insert already exists"));
                    return $this->redirect(['create', 'id' => 1]);
                }
            }

            if ($model->quantity <= 0) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry You Enter Zero Value As Quantity"));
                return $this->redirect(['create', 'id' => 1]);
            }

            if ($model->quantity > $item->quantity) {
                $model->quantity = $item->quantity;
            }

            if ($model->salePrice <= 0) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry The SalePrice You Enter Less Than The MinPrice"));
            }

            if ($model->salePrice < $item->costPrice) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry The SalePrice You Enter Less Than The MinPrice"));
                return $this->redirect(['create', 'id' => 1]);
                //exit;
            } else {
                $model->category = $item->id;
                // $model->salePrice = $item->maxPrice;
                $model->costPrice = $item->costPrice;
                $model->box = $item->box;
                $model->state = 1;
                $model->saveAll();
            }


            return $this->redirect(['createfast', 'id' => 1]);
        } else {
            //  $company = CompanyInfo::find()->one();
            return $this->render('createfaster', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'company' => $company,
                // 'data'=>$data
                // 'category'=>$category,
                // 'client'=>$client,
            ]);
        }
    }

    public function actionCreateAjax()
    {
        $model = new TempInvoice();

        if (Yii::$app->user->can('saleOnHoldItems')) {
            $type = [1, 3];
        } else {
            $type = [1];
        }
        $data = Totalinventory::find()
            ->Where(['branch' => Yii::$app->user->identity->branch])
            ->andwhere(['in', 'type', $type])
            ->all();

        $searchModel = new TempInvoiceSearch();
        $searchModel->created_by = Yii::$app->user->identity->id;
        $searchModel->state = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result = TempInvoice::findOne($id);
            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['TempInvoice']);

            $post['TempInvoice'] = $posted;
            if ($result->load($post)) {

                $data = Totalinventory::find()
                    ->Where(['branch' => Yii::$app->user->identity->branch])
                    ->andwhere(['id' => $result->category])
                    ->andwhere(['in', 'type', $type])
                    ->one();

                if (isset($posted['quantity'])) {
                    if ($data->quantity < $result->quantity) {
                        $result->quantity = $data->quantity;
                    }
                    $outMessage = $result->quantity;
                    $result->save(false);
                } elseif (isset($posted['serial_number'])) {
                    $outMessage = $result->serial_number;
                } else {
                    if ($data->minPrice > $result->salePrice) {

                        $result->salePrice = $data->minPrice;
                    }
                    $outMessage = $result->salePrice;
                    $result->save(false);
                }
                $output = $outMessage;
                $out = Json::encode(['output' => $output]);
                return $out;
            }
        }
        /**
         * في الاعلي اذا كان سعر الكمية المطلوبه أكبر من الكمية الموجوده يتم تغيير الكيه حسب الموجود فقط 
         * 
         * في حالة محاولة تعديل سعر البيع، يتم النظر اذا كان السعر اصغر من اقل سعر بيع تقوم المنضومه بتغيير السعر 
         * الي اقل سعر بيع ولا يمكن البيع باقل منه
         */



        if ($model->loadAll(Yii::$app->request->post())) {

            $category = new Category();
            $category = Inventory::find()
                ->joinWith('prices')
                ->where(['prices.category' => $model->category])->one();

            $temp = TempInvoice::find()->where([
                'category' => $model->category,
                'created_by' => Yii::$app->user->identity->id,
                'salePrice' => $category->prices->maxPrice,
                'serial_number' => $model->serial_number,
            ])->one();

            if (!isset($temp)) {
                $model->quantity = 1;
                $model->salePrice = $category->prices->maxPrice;
                $model->costPrice = $category->prices->costPrice;
                $model->box = $category->box;
                $model->state = 1;

                $model->save(false);
            } else {
                $temp->quantity = $temp->quantity + 1;
                $model->salePrice = $category->prices->maxPrice;
                $model->costPrice = $category->prices->costPrice;
                $model->box = $category->box;
                $model->state = 1;

                $temp->save();
            }

            return $this->redirect(['createAjax', 'id' => 1]);
        } else {
            return $this->render('createAjax', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data' => $data
                // 'category'=>$category,
                // 'client'=>$client,
            ]);
        }
    }

    /**
     * Updates an existing TempInvoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->request->post('_asnew') == '1') {
            $model = new TempInvoice();
        } else {
            $model = $this->findModel($id);
        }

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TempInvoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */

    public function actionUpdatqyt($id, $qyt)
    {
        //die(print_r($id));

        $item = TempInvoice::find()->where(['id' => $id])->one();
        $item->quantity = $qyt;
        $item->save(false);


        return true;
    }

    public function actionDeleteing($id)
    {
        //die(print_r($id));

        $this->findModel($id)->delete();

        return true;
    }

    public function actionDelete($id)
    {
        //die(print_r($id));
        $this->findModel($id)->delete();

        return $this->redirect(['create']);
    }

//     public function actionDeleteAll()
// {
//     Yii::$app->response->format = Response::FORMAT_JSON;

//     if (Yii::$app->request->isAjax) {
//         // perform delete operation
//         // ...
//         TempInvoice::deleteAll(['state' => 1, 'created_by' => Yii::$app->user->identity->id]);
//         return ['success' => true];
//     } else {
//         return ['success' => false];
//     }
// }

    public function actionDeleteAll()
    {
        TempInvoice::deleteAll(['state' => 1, 'created_by' => Yii::$app->user->identity->id]);
        // $this->findModel($id)->deleteWithRelated();
        return $this->redirect(['create']);
    }

    /**
     * 
     * Export TempInvoice information into PDF format.
     * @param integer $id
     * @return mixed
     */
    public function actionPdf($id)
    {
        $model = $this->findModel($id);

        $content = $this->renderAjax('_pdf', [
            'model' => $model,
        ]);

        $pdf = new \kartik\mpdf\Pdf([
            'mode' => \kartik\mpdf\Pdf::MODE_CORE,
            'format' => \kartik\mpdf\Pdf::FORMAT_A4,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            'destination' => \kartik\mpdf\Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => \Yii::$app->name],
            'methods' => [
                'SetHeader' => [\Yii::$app->name],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }

    /**
     * Creates a new TempInvoice model by another data,
     * so user don't need to input all field from scratch.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param mixed $id
     * @return mixed
     */
    public function actionSaveAsNew($id)
    {
        $model = new TempInvoice();

        if (Yii::$app->request->post('_asnew') != '1') {
            $model = $this->findModel($id);
        }

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('saveAsNew', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the TempInvoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TempInvoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TempInvoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    public function actionHold()
    {
        $temp  = TempInvoice::find()->where(['=', 'state', 2])->max('invoice_number') + 1;

        $condition = [
            'and',
            ['=', 'state', 1],
            ['=', 'created_by', Yii::$app->user->identity->id]
        ];

        TempInvoice::updateAll(['state' => 2, 'invoice_number' => $temp], $condition);

        // ['=', 'state', 1], 
        // ['=', 'created_by', Yii::$app->user->identity->id]);
        return $this->redirect(['create']);
    }

    public function actionHolded()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TempInvoice::find()
                ->select('sum(salePrice*quantity) as salePrice ,invoice_number ')
                ->where(['state' => 2])
                ->andWhere(['created_by' => Yii::$app->user->identity->id])
                ->groupBy('invoice_number'),
            'pagination' => [
                'pageSize' => 50
            ],
        ]);

        return $this->renderAjax('holded', [
            // 'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUnhold($id)
    {
        $temp  = TempInvoice::find()->where(['=', 'invoice_number', $id]);

        $data =  TempInvoice::find()
            ->where(['state' => 1])
            ->andWhere(['created_by' => Yii::$app->user->identity->id])->one();
        if (isset($data)) {

            Yii::$app->session->setFlash('error', Yii::t('app', "There is another invoice Finish or hold it before !"));
            return $this->redirect(['create']);
        }
        TempInvoice::updateAll(['state' => 1], ['=', 'invoice_number', $id]);
        return $this->redirect(['create']);
    }

    public function actionAdd($categoryid, $type)
    {
        $model = new TempInvoice();
        $category = new Category();
        $category = Inventory::find()
            ->joinWith('prices')
            ->where(['prices.category' => $categoryid])->one();

        $temp = TempInvoice::find()->where([
            'category' => $categoryid,
            'created_by' => Yii::$app->user->identity->id,
            'salePrice' => $category->prices->maxPrice,
            'serial_number' => $model->serial_number,
            'type' => $type,
            'state' => 1,
        ])->one();

        if (!isset($temp)) {
            $model->quantity = 1;
            $model->salePrice = $category->prices->maxPrice;
            $model->costPrice = $category->prices->costPrice;
            $model->box = $category->box;
            $model->state = 1;
            $model->type = $type;
            $model->category = $categoryid;

            $model->save(false);
        } else {
            $temp->quantity = $temp->quantity + 1;
            $model->salePrice = $category->prices->maxPrice;
            $model->costPrice = $category->prices->costPrice;
            $model->box = $category->box;
            $model->state = 1;
            $model->category = $categoryid;

            $temp->save();
        }
        $this->redirect(['create']);
    }

    public function actionInventory($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '1', 'text' => 'test']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, name AS text')
                ->from('Totalinventory')
                ->where(['like', 'name', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Totalinventory::find($id)->name];
        }
        return $out;
    }

    public function actionDoc($q = null)
    {
        // JSON format result.
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out['results'] = '';
        if ($q) {
            $query = Totalinventory::find()->select(['id', 'name'])
                ->where(['like', 'name', $q])
                ->orWhere(['like', 'id', $q])
                ->all();
            // Group titles by author.
            $authorArray = ArrayHelper::map($query, 'id', 'name');
            // Previous array lacks keywords needed to use optgroups
            // in AJAX-based Select2: 'results', 'id', 'text', 'children'.
            // Let's insert them.
            $results = [];
            foreach ($authorArray as $author => $docArray) {
                $docs  = [];
                foreach ($docArray as $id => $name) {
                    $docs[] = ['id' => $id, 'text' => $name];
                }
                $results[] = ['text' => $author, 'children' => $docs];
            }
            $out['results'] = $results;
        }
        return $out;
    }

    public function actionFast()
    {
        $model = new TempInvoice();
        $dataProvider = new ActiveDataProvider([
           // 'query' => Totalinventory::find()
            'query' => Stocks::find()
                        ->select(' category.id, max(category.name) as name, sum(stocks.quantity) as quantity, max(category.unit) as unit, max(category.company) as company,
                        max(category.box) as box, max(category.class) as class, max(category.serialNo) as serialNo, branch, max(category.commCode) as commCode
                        ,max(maxPrice) as maxPrice, max(costPrice) as costPrice, max(minPrice) as minPrice'
                        )
                        ->leftJoin('category', 'category.id = stocks.category')
                        ->leftJoin('prices', 'category.id = prices.category')
                        ->groupBy('stocks.category, stocks.branch')
                        ->having('sum(stocks.quantity) <>0')
                //->Where(['branch' => Yii::$app->user->identity->branch,]),
                ->Where(['category.id' => 23]),
                //->andWhere(['<>', 'quantity', 0]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
            'pagination' => ['pageSize' => 70],
        ]);


        if ($model->loadAll(Yii::$app->request->post())) {

            //$select = Yii::$app->request->post('TempInvoice')['checkboxValues'];
            // die(var_dump($select));
            // foreach ($select as $id) {
            //     $prices = Prices::find()->where(['category' => $id])->one();
            //     $category = Category::find()->where(['id' => $id])->one();

            //     $model = new TempInvoice();
            //     $model->category = $id;

            //     $model->quantity = 0;
            //     $model->costPrice = $prices->costPrice;
            //     $model->salePrice = $prices->maxPrice;
            //     $model->box = $category->box;
            //     $model->state = 1;

            //     $model->save(false);
            // }

            $textInputValues = Yii::$app->request->post('TempInvoice')['textInputValues'];
            if (!empty($textInputValues)) {
                foreach ($textInputValues as $id => $textInputValue) {
                    if (Yii::$app->request->post('selection') && in_array($id, Yii::$app->request->post('selection'))) {
                        //$prices = Prices::find()->where(['category' => $id])->one();
                        //$category = Category::find()->where(['id' => $id])->one();
                        $item = Totalinventory::find()->where(['id' => $id])->one();

                        if ($textInputValue < $item->quantity && $textInputValue <> 0) {
                            $newModel = new TempInvoice();
                            $newModel->category = $id;
                            $newModel->quantity = $textInputValue;
                            $newModel->salePrice = $item->maxPrice;
                            $newModel->costPrice = $item->costPrice;
                            $newModel->box = 1;
                            $newModel->state = 1;
                            $newModel->save();
                        }
                    }
                }
            }

            return $this->redirect(['create']);
        }

        return $this->renderAjax('createFast', [
            // 'searchModel' => $searchModel,
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);

        // $select = Yii::$app->request->post('selection');

        // foreach ($select as $id) {
        //     $prices = Prices::find()->where(['category' => $id])->one();
        //     $category = Category::find()->where(['id' => $id])->one();

        //     $model = new TempInvoice();
        //     $model->category = $id;

        //     $model->quantity = 1;
        //     $model->costPrice = $prices->costPrice;
        //     $model->salePrice = $prices->maxPrice;
        //     $model->box = $category->box;
        //     $model->state = 1;

        //     $model->save(false);
        // }

        // return $this->redirect(['create']);
    }

    public function actionSave($id, $salePrice, $costPrice)
    {

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            // die(var_dump((float) $maxPrice));
            $model =  new TempInvoice();

            $model->salePrice = (float) $salePrice;
            $model->category = (float) $id;
            $model->quantity = 1;
            $model->box = 1;
            $model->state = 1;
            $model->costPrice = $costPrice;
            $model->save(false);
            $modelid  = $model->id;

            return $modelid;
        } else
            return false;
    }

}
