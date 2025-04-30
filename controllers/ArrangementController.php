<?php

namespace app\controllers;

use Yii;
use app\models\Arrangement;
use app\models\ArrangementSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TempArrangement;
use app\models\ArrangementDetails;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use app\models\ArrangementDetailsSearch;
use app\models\Stocks;
use yii\helpers\Json;

/**
 * ArrangementController implements the CRUD actions for Arrangement model.
 */
class ArrangementController extends Controller
{
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
            $model->branch = Yii::$app->user->identity->branch;
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = Yii::$app->user->identity->id;
            $model->save();
            $id = $model->id;

            $temp_arrangement = new TempArrangement();
            $temp_arrangement = TempArrangement::find()->where(['created_by' => Yii::$app->user->identity->id, 'state' => 0])->all();

            foreach ($temp_arrangement as $data) {
                $modelDetails = new ArrangementDetails();
                $modelDetails->arrangement = $id;
                $modelDetails->category =  $data->category;
                $modelDetails->quantity = $data->quantity;
                $modelDetails->box = 1;
                $modelDetails->type = $data->type;
                $modelDetails->stockTaking = $data->stockTaking;
                $modelDetails->save(false);

                $getStock = Stocks::find()->where(['category' => $data->category])
                    ->andWhere(['branch' => Yii::$app->user->identity->branch])
                    ->andWhere(['=', 'type', 1])->one();

                if ($getStock == null) {
                    $stocks = new Stocks();
                    $stocks->category = $data->category;
                    $stocks->branch = Yii::$app->user->identity->branch;
                    $stocks->quantity = $data->quantity;
                    $stocks->type = 1;
                    $stocks->save(false);
                } else {
                    if ($data->type == 1) {
                        Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + ($data->quantity*$data->type)
                 WHERE category=:category
                 and branch = :branch
                 and type = :type")
                            ->bindValue(':category', $data->category)
                            ->bindValue(':branch', Yii::$app->user->identity->branch)
                            ->bindValue(':type', 1)
                            ->execute();
                    } else {
                        Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - ($data->quantity*$data->type)
                WHERE category=:category
                and branch = :branch
                and type = :type")
                            ->bindValue(':category', $data->category)
                            ->bindValue(':branch', Yii::$app->user->identity->branch)
                            ->bindValue(':type', 1)
                            ->execute();
                    }
                }
            }
            TempArrangement::deleteAll(['created_by' => Yii::$app->user->identity->id, 'state' => 0]);

            return $this->redirect(['view', 'id' => $model->id]);
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

        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result = ArrangementDetails::findOne($id);
            $old = ArrangementDetails::find()->where(['id' => $id])->one();

            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['ArrangementDetails']);
            $quantity = $posted['quantity'];
            $post['ArrangementDetails'] = $posted;
            if ($result->load($post)) {
                $result->save(false);
                if (isset($posted['quantity'])) {
                    $outMessage = $result->quantity;
                } else {
                    $outMessage = $result->type;
                }

                $stocks = Stocks::find()->where(['category' => $result->category])
                    ->andWhere(['branch' => Yii::$app->user->identity->branch])
                    ->andWhere(['type' => 1])->one();
                if ($old->type == 1) {
                    $stocks->quantity = ($stocks->quantity - $old->quantity);
                    $stocks->save(true);
                    $stocks->quantity = ($stocks->quantity + $quantity);
                    $stocks->save(true);
                } else {
                    $stocks->quantity = ($stocks->quantity + $old->quantity);
                    $stocks->save(true);
                    $stocks->quantity = ($stocks->quantity - $quantity);
                    $stocks->save(true);
                }

                $output = $outMessage;

                $out = Json::encode(['output' => $output]);

                return $out;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
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
        $arrangementDetails = ArrangementDetails::find()->where(['=', 'arrangement', $id])->all();

        foreach ($arrangementDetails as $key => $value):

            if ($value->type == 1) {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - ($value->quantity*$value->type)
         WHERE category=:category
         and branch = :branch
         and type = :type")
                    ->bindValue(':category', $value->category)
                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                    ->bindValue(':type', 1)
                    ->execute();
            } else {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + ($value->quantity*$value->type)
        WHERE category=:category
        and branch = :branch
        and type = :type")
                    ->bindValue(':category', $value->category)
                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                    ->bindValue(':type', 1)
                    ->execute();
            }

        endforeach;

        ArrangementDetails::deleteAll(['arrangement' => $id]);
        Arrangement::deleteAll(['id' => $id]);
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
     * Creates a new HistransClient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionStockTaking()
    {
        $model = new Arrangement();
        if ($model->load(Yii::$app->request->post())) {

            $sql = " select at, category.name, arrangementDetails.quantity, DATE_FORMAT(arrangement.at, '%Y')year,
        category.serialNo, category.company, arrangementDetails.type
        FROM arrangementDetails, arrangement, category
        where arrangement.id=arrangementDetails.arrangement 
        and DATE_FORMAT(arrangement.at, '%Y')  =  " . $model->at . " and stockTaking =1 
        and arrangementDetails.category = category.id
        order by  arrangement.id";


            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $info = $data->queryAll();
            if ($info == null) {
                echo '<script type="text/javascript"> 
                alert("عفوا لايوجد بيانات للعرض");
                window.location.href="?r=arrangement/stock-taking"
                </script>';
            }
            return $this->render('stockTakingRep', [
                'models' => $info,
                'year' => $model->at,
            ]);
        }

        return $this->render('stockTaking', [
            'model' => $model,
        ]);
    }
}
