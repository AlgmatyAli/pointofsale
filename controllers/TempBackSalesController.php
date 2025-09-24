<?php

namespace app\controllers;

use Yii;
use app\models\TempBackSales;
use app\models\TempBackSalesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Category;
use app\models\Inventory;
use app\models\SalesDetails;
use yii\helpers\Json;
use yii\filters\AccessControl;

/**
 * TempBackSalesController implements the CRUD actions for TempBackSales model.
 */
class TempBackSalesController extends Controller
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
                        'actions' => ['create', 'view', 'delete-all', 'itemlist', 'add', 'delete', 'create-by-client', 'get-info'],
                        'roles' => ['createBackSales'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all TempBackSales models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TempBackSalesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (yii::$app->request->post('hasEditable')) {

            $id = Yii::$app->request->post('editableKey');

            $result = TempBackSales::findOne($id);


            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['TempBackSales']);

            $post['TempBackSales'] = $posted;
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
     * Displays a single TempBackSales model.
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
     * Creates a new TempBackSales model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TempBackSales();

        $searchModel = new TempBackSalesSearch();
        $searchModel->created_by = Yii::$app->user->identity->id;
        $searchModel->state = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result = TempBackSales::findOne($id);

            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['TempBackSales']);
            $post['TempBackSales'] = $posted;
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

        if ($model->loadAll(Yii::$app->request->post())) {

            $category = new Category();
            $category = Inventory::find()
                ->joinWith('prices0')
                ->where(['prices.category' => $model->category])->one();

            $temp = TempBackSales::find()->where([
                'category' => $model->category,
                'created_by' => Yii::$app->user->identity->id,
                'salePrice' => $category->prices0->maxPrice,
                'serial_number' => $model->serial_number,
            ])->one();

            if (!isset($temp)) {
                $model->quantity = 1;
                $model->salePrice = $category->prices0->maxPrice;
                $model->costPrice = $category->prices0->costPrice;
                $model->box = $category->box;
                $model->state = 1;

                $model->save(false);
            } else {
                $temp->quantity = $temp->quantity + 1;
                $model->salePrice = $category->prices0->maxPrice;
                $model->costPrice = $category->prices0->costPrice;
                $model->box = $category->box;
                $model->state = 1;

                $temp->save();
            }

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
     * Updates an existing TempBackSales model.
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
     * Deletes an existing TempBackSales model.
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
     * Finds the TempBackSales model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TempBackSales the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TempBackSales::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    public function actionAdd($categoryid, $type)
    {
        $model = new TempBackSales();
        $category = new Category();
        $category = Inventory::find()
            ->joinWith('prices0')
            ->where(['prices.category' => $categoryid])->one();

        $temp = TempBackSales::find()->where([
            'category' => $categoryid,
            'created_by' => Yii::$app->user->identity->id,
            'salePrice' => $category->prices0->maxPrice,
            'serial_number' => $model->serial_number,
            'state' => 1,
        ])->one();

        if (!isset($temp)) {
            $model->quantity = 1;
            $model->salePrice = $category->prices0->maxPrice;
            $model->costPrice = $category->prices0->costPrice;
            $model->box = $category->box;
            $model->state = 1;
            $model->category = $categoryid;

            $model->save(false);
        } else {
            $temp->quantity = $temp->quantity + 1;
            $model->salePrice = $category->prices0->maxPrice;
            $model->costPrice = $category->prices0->costPrice;
            $model->box = $category->box;
            $model->state = 1;
            $model->category = $categoryid;

            $temp->save();
        }
        $this->redirect(['create']);
    }

    public function actionDeleteAll()
    {
        TempBackSales::deleteAll(['state' => 1, 'created_by' => Yii::$app->user->identity->id]);
        return $this->redirect(Yii::$app->request->referrer ?: ['create']);
    }

    public function actionCreateByClient()
    {
        $model = new TempBackSales();
        $searchModel = new TempBackSalesSearch();
        $searchModel->created_by = Yii::$app->user->identity->id;
        $searchModel->state = 1;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result = TempBackSales::findOne($id);

            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['TempBackSales']);
            $post['TempBackSales'] = $posted;
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

        if ($model->loadAll(Yii::$app->request->post())) {
            $model->serial_number = $model->client;
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = Yii::$app->user->identity->id;
            $model->state = 1;
            $model->box = 1;
            if ($model->save(false)) {
                return 'success';
            }

            // $model->save(false);
            // return $this->redirect(['create-by-client']);
        } else {
            $serial_number = TempBackSales::find()->select('serial_number')->where(['created_by' => Yii::$app->user->identity->id, 'state' => 1])->distinct()->one();
            return $this->render('create_by_client', [
                'model' => $model,
                'serial_number' => $serial_number,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    public function actionGetInfo($id)
    {
        $value = SalesDetails::find()->where(['id' => $id])->one();
        Yii::$app->response->format = Yii\web\Response::FORMAT_JSON;
        return $value;
    }
}
