<?php

namespace app\controllers;

use Yii;
use app\models\TransferItems;
use app\models\TransferItemsSearch;
use app\models\TransferItemsDetails;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\base\TempTransferItems;
use app\models\Stocks;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;

/**
 * TransferItemsController implements the CRUD actions for TransferItems model.
 */
class TransferItemsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
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
            $model->created_at     = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->identity->id;
            $model->save();
            // ==============
            $items =  TempTransferItems::find()->where('created_by = ' . Yii::$app->user->identity->id)
                ->all();
            //============== add to TransferItemsDetails
            foreach ($items  as $value) {
                $TransferItemsDetails = new TransferItemsDetails();
                $TransferItemsDetails->transfer = $model->id;
                $TransferItemsDetails->category = $value->category;
                $TransferItemsDetails->quantity = $value->quantity;
                $TransferItemsDetails->save(false);
                //  ===============================
                $exsit = Stocks::find()->where(['=', 'branch', $model->toBranch])
                    ->andWhere(['=', 'category', $value->category])
                    ->andWhere(['=', 'type', 1])
                    ->one();
                if ($exsit == null){
                    Yii::$app->db->createCommand('INSERT INTO stocks(category, quantity, branch, type) VALUES
            (:category, :quantity, :branch, :type)')
                        ->bindValues([':category' => $value->category])
                        ->bindValues([':quantity' => $value->quantity])
                        ->bindValues([':branch' => $model->toBranch])
                        ->bindValues([':type' => 1])
                        ->execute();

                        Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $value->quantity
                        WHERE category=:category
                        and branch = :branch
                        and type = :type")
                        ->bindValue(':category', $value->category)
                        ->bindValue(':branch', $model->fromBranch)
                        ->bindValue(':type', 1)
                        ->execute();
                }else{
                    Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + $value->quantity
                        WHERE category=:category
                        and branch = :branch
                        and type = :type")
                        ->bindValue(':category', $value->category)
                        ->bindValue(':branch', $model->toBranch)
                        ->bindValue(':type', 1)
                        ->execute();

                        Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $value->quantity
                        WHERE category=:category
                        and branch = :branch
                        and type = :type")
                        ->bindValue(':category', $value->category)
                        ->bindValue(':branch', $model->fromBranch)
                        ->bindValue(':type', 1)
                        ->execute();
                }
            }
           TempTransferItems::deleteAll(['created_by' => Yii::$app->user->identity->id]);
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

        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result = TransferItemsDetails::findOne($id);
            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['TransferItemsDetails']);
            $post['TransferItemsDetails'] = $posted;
            if ($result->load($post)) {
                $result->save(false);
                if (isset($posted['quantity'])) {
                    $outMessage = $result->quantity;
                }

                $output = $outMessage;

                $out = Json::encode(['output' => $output]);

                return $out;
            }
        }
        if ($model->load(Yii::$app->request->post())) {

            if (Yii::$app->user->identity->seeOtherBranchQ == 0) {
                $branch = Yii::$app->user->identity->branch;
            } else {
                $branch = [1, 2, 3];
            }

            $item = Stocks::find()
                ->select([
                    'stocks.category as id', 'stocks.quantity as quantity',
                    'prices.costPrice', 'prices.minPrice', 'prices.maxPrice'
                ])
                ->leftJoin('prices', 'stocks.category = prices.category')
                ->where(['stocks.category' => $model->category])
                ->andWhere(['<>', 'stocks.quantity', 0])
                ->andwhere(['in', 'stocks.type', 1])
                ->andWhere(['in', 'stocks.branch', $branch])
                ->one();

            if ($item != null) {
                if ($model->quantity > $item->quantity) {
                    $model->quantity = abs($item->quantity);
                }
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
        $transferItems = TransferItems::find()->where(['id'=> $id])->one();
        $TransferItemsDetails= TransferItemsDetails::find()->where(['transfer'=> $id])->all();

        foreach ($TransferItemsDetails as $data) {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + $data->quantity 
                 WHERE category=:category
                 and branch = :branch
                 and type = :type")
                    ->bindValue(':category', $data->category)
                    ->bindValue(':branch', $transferItems->fromBranch)
                    ->bindValue(':type', 1)
                    ->execute();

                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $data->quantity
                WHERE category=:category
                and branch = :branch
                and type = :type")
                    ->bindValue(':category', $data->category)
                    ->bindValue(':branch', $transferItems->toBranch)
                    ->bindValue(':type', 1)
                    ->execute();
        }
    
        Yii::$app
        ->db
        ->createCommand()
        ->delete('transferItems', ['id' => $id])
        ->execute();

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

    public function actionAdd($categoryid, $transfer)
    {
        $model = new TransferItemsDetails();
        $transferData = TransferItems::find()->where(['=','id', $transfer])->one();
        
        $temp = TransferItemsDetails::find()->where([
            'category' => $categoryid,
            'transfer' => $transfer,
        ])->one();

        if (!isset($temp)) {
            $model->transfer = $transfer;
            $model->quantity = 1;
            $model->category = $categoryid;
            $model->save(false);
            // ================
            $exsit = Stocks::find()->where(['=', 'branch', $transferData->toBranch])
            ->andWhere(['=', 'category', $categoryid])
            ->andWhere(['=', 'type', 1])
            ->one();
        if ($exsit == null){
            Yii::$app->db->createCommand('INSERT INTO stocks(category, quantity, branch, type) VALUES
                 (:category, :quantity, :branch, :type)')
                ->bindValues([':category' => $categoryid])
                ->bindValues([':quantity' => 1])
                ->bindValues([':branch' => $transferData->toBranch])
                ->bindValues([':type' => 1])
                ->execute();
        }else{
            Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + 1
                 WHERE category=:category
                 and branch = :branch
                 and type = :type")
                ->bindValue(':category', $categoryid)
                ->bindValue(':branch', $transferData->toBranch)
                ->bindValue(':type', 1)
                ->execute();
        }
        } else {
            $temp->quantity = $temp->quantity + 1;
            $temp->save(false);
            // ===============
             $exsit = Stocks::find()->where(['=', 'branch', $transferData->toBranch])
             ->andWhere(['=', 'category', $categoryid])
             ->andWhere(['=', 'type', 1])
             ->one();
         if ($exsit == null){
             Yii::$app->db->createCommand('INSERT INTO stocks(category, quantity, branch, type) VALUES
                  (:category, :quantity, :branch, :type)')
                 ->bindValues([':category' => $categoryid])
                 ->bindValues([':quantity' => 1])
                 ->bindValues([':branch' => $transferData->toBranch])
                 ->bindValues([':type' => 1])
                 ->execute();
         }else{
             Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + 1
                  WHERE category=:category
                  and branch = :branch
                  and type = :type")
                 ->bindValue(':category', $categoryid)
                 ->bindValue(':branch', $transferData->toBranch)
                 ->bindValue(':type', 1)
                 ->execute();
         }
        }
        // ========
            return $this->redirect(['transfer-items/update', 'id' => $transfer]);
    }

    public function actionRemove($id)
    {
        $details =  TransferItemsDetails::find()->where(['id' => $id])->one();
        $transferId = $details["transfer"];
        
        TransferItemsDetails::deleteAll(['id' => $id]);
        return $this->redirect(['update', 'id' => $transferId]);
    }
}
