<?php

namespace app\controllers;

use Yii;
use app\models\TempTransferItemsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\base\TempTransferItems;
use app\models\Stocks;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Response;

/**
 * TempTransferItemsController implements the CRUD actions for TempTransferItems model.
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
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TempTransferItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->post('hasEditable')) {
            $model = new TempTransferItems();
            $bookId = Yii::$app->request->post('editableKey');
            $model = TempTransferItems::findOne($bookId);

            $post = [];
            $posted = current($_POST['TempTransferItems']);
            $post['TempTransferItems'] = $posted;
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
     * Displays a single TempTransferItems model.
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
     * Creates a new TempTransferItems model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TempTransferItems();
        $searchModel = new TempTransferItemsSearch();

        if (Yii::$app->user->can('saleOnHoldItems')) {
            $type = [1, 2, 3];
        } else {
            $type = [1, 2, 3];
        }

        $searchModel->created_by = Yii::$app->user->identity->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result = TempTransferItems::findOne($id);
            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['TempTransferItems']);
            $post['TempTransferItems'] = $posted;
            if ($result->load($post)) {

                // if (Yii::$app->user->identity->seeOtherBranchQ == '0') {
                //     $branch = Yii::$app->user->identity->branch;
                // } else {
                //     $branch = [1, 2, 3];
                // }

                $data = Stocks::find()
                    ->select([
                        'max(stocks.category) as category',
                        'sum(stocks.quantity) as quantity',
                        'max(prices.costPrice) as costPrice',
                        'max(prices.minPrice) as minPrice',
                        'max(prices.maxPrice) as maxPrice'
                    ])
                    ->leftJoin('prices', 'stocks.category = prices.category')
                    ->Where(['stocks.branch' => $model->branch])
                    ->andwhere(['stocks.category' => $result->category])
                    ->andwhere(['in', 'stocks.type', $type])
                    ->one();

                $sumQnty = TempTransferItems::find()
                    ->where(['category' => $result->category, 'created_by' => Yii::$app->user->identity->id,])
                    ->andWhere(['!=', 'id', $result->id])
                    ->sum('quantity');

                if ($sumQnty != null) {
                    $balance = abs($data->quantity) - abs($sumQnty);
                } else {
                    if ($data != null) {
                        $balance = abs($data->quantity);
                    } else {
                        $balance = 1;
                    }
                }

                if ($posted['quantity'] >= $balance) {
                    $remaining = $balance;
                } else {
                    $remaining = $posted['quantity'];
                }

                if (isset($posted['quantity'])) {
                    $result->quantity = $remaining;
                    $outMessage = $result->quantity;
                    $result->save(false);
                }

                $output = $outMessage;

                $out = Json::encode(['output' => $output]);

                return $out;
            }
        }

        if ($model->loadAll(Yii::$app->request->post())) {
            if ($model->category == null) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry You Can not Add Empty Model"));
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }

            // if (Yii::$app->user->identity->seeOtherBranchQ == 0) {
            //     $branch = Yii::$app->user->identity->branch;
            // } else {
            //     $branch = [1, 2, 3];
            // }

            $item = Stocks::find()
                ->select([
                    'max(stocks.category) as category',
                    'sum(stocks.quantity) as quantity',
                    'max(prices.costPrice) as costPrice',
                    'max(prices.minPrice) as minPrice',
                    'max(prices.maxPrice) as maxPrice'
                ])
                ->leftJoin('prices', 'stocks.category = prices.category')
                ->Where(['stocks.branch' => $model->branch])
                ->andwhere(['stocks.category' => $model->category])
                ->andwhere(['in', 'stocks.type', 1])
                ->one();

            $sumQnty = TempTransferItems::find()
                ->where(['category' => $model->category, 'created_by' => Yii::$app->user->identity->id,])
                ->sum('quantity');

            if ($sumQnty != null) {
                $balance = abs($item->quantity) - abs($sumQnty);
            } else {
                if ($item != null) {
                    $balance = abs($item->quantity);
                } else {
                    $balance = 1;
                }
            }
            if (abs($model->quantity) > abs($balance)) {
                $remaining = abs($balance);
            } else {
                $remaining = abs($model->quantity);
            }
            $model->quantity = $remaining;
            if ($item != null) {
                if ($sumQnty >= $item->quantity) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    // return ['error' => true, 'message' => Yii::t('app', "عفوا لقد تجاوزت الكمية الموجودة لايمكنك الاستمرار")];
                    return "عفوا لقد تجاوزت الكمية الموجودة لايمكنك الاستمرار";
                }
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['error' => true, 'message' => Yii::t('app', "عفوا كمية هذا الصنف صفر لايمكنك الاستمرار")];
            }
            $id = TempTransferItems::find()->max('id') + 1;
            $model->id = $id;
            $model->created_by = Yii::$app->user->identity->id;
            $model->created_at = date('Y-m-d');
            $model->save(false);

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
     * Deletes an existing TempTransferItems model.
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
     * Finds the TempTransferItems model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TempTransferItems the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TempTransferItems::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    public function actionDeleteAll()
    {
        TempTransferItems::deleteAll(['created_by' => Yii::$app->user->identity->id]);
        return $this->redirect(['create']);
    }
}
