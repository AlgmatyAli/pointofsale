<?php

namespace app\controllers;

use Yii;
use app\models\TempArrangement;
use app\models\TempArrangementSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Category;
use app\models\Inventory;
use yii\helpers\Json;
use yii\filters\AccessControl;
use app\models\ArrangementDetails;
use app\models\CompanyInfo;
use app\models\Stocks;
use yii\db\Query;

/**
 * TempArrangementController implements the CRUD actions for TempArrangement model.
 */
class TempArrangementController extends Controller
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
                        'actions' => ['create', 'view', 'delete-all', 'delete', 'itemlist', 'get-inv'],
                        'roles' => ['createArrangment'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all TempArrangement models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TempArrangementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TempArrangement model.
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
     * Creates a new TempArrangement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TempArrangement();

        $searchModel = new TempArrangementSearch();
        $searchModel->created_by = Yii::$app->user->identity->id;
        $searchModel->state = 0;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result = TempArrangement::findOne($id);

            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['TempArrangement']);
            $post['TempArrangement'] = $posted;
            if ($result->load($post)) {
                $result->save(false);
                if (isset($posted['quantity'])) {
                    $outMessage = $result->quantity;
                } else {
                    $outMessage = $result->type;
                }

                $output = $outMessage;

                $out = Json::encode(['output' => $output]);

                return $out;
            }
        }

        if ($model->loadAll(Yii::$app->request->post())) {

            $temp = TempArrangement::find()->where([
                'category' => $model->category,
                'stockTaking' => 1,
            ])->one();

            if ($model->stockTaking == 1 && $temp <> NULL) {
                if ($temp->category == $model->category) {

                    Yii::$app->session->setFlash('error', Yii::t('app', "This Item Alrady stockTaking") . ' : ' . $model->category0->name);
                    return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
                }
            }

            $tempD = ArrangementDetails::find()
                ->leftJoin('arrangement', 'arrangement.id = arrangementDetails.arrangement')
                ->where([
                    'category' => $model->category,
                    'stockTaking' => 1,
                ])
                ->andWhere('year(arrangement.at) = ' . date("Y") . '')
                ->one();

            if ($model->stockTaking == 1 && $tempD <> NULL) {
                if ($tempD->category == $model->category) {
                    Yii::$app->session->setFlash('error', Yii::t('app', "This Item Alrady stockTaking") . ' : ' . $model->category0->name);
                    return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
                }
            }

            $deferent = $model->quantity - $model->realQuantity;
            if ($deferent >= 0) {
                $model->type  = 1;
                $model->quantity = $deferent;
            }
            if ($deferent < 0) {
                $model->type  = -1;
                $model->quantity = ($deferent * -1);
            }

            $model->state = 0;
            $model->box = 1;
            $model->branch = Yii::$app->user->identity->branch;
            $model->save(false);

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
     * Updates an existing TempArrangement model.
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
     * Deletes an existing TempArrangement model.
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
     * Finds the TempArrangement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TempArrangement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TempArrangement::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    public function actionDeleteAll()
    {
        TempArrangement::deleteAll(['state' => 0, 'created_by' => Yii::$app->user->identity->id]);
        return $this->redirect(['create']);
    }

    public function actionItemlist($q = null, $id = null)
    {
        // if(Yii::$app->user->Identity->seeOtherBranchQ == 0){
        //     $whereBranch = 'branch='.Yii::$app->user->Identity->branch;
        // }else{
        //     $whereBranch = 'branch in (1,2,3,4,5,6,7,8,9)';
        // }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $q = str_replace(' ', '%', $q);
            $query = new Query;
            $secript = [
                'category.id',
                'category.name AS text',
                'company AS company',
                'stocks.quantity as quantity',
                'maxPrice as maxPrice',
                'costPrice as costPrice',
                'serialNo AS serialNo',
                'minPrice AS minPrice',
                'place',
                'commCode',
                'branches.name AS BRNAME'
            ];
            $query->select(
                $secript
            )
                ->from('category')
                ->leftJoin('prices', 'prices.category = category.id')
                ->leftJoin('stocks', 'stocks.category = category.id')
                ->leftJoin('branches', 'branches.id = stocks.branch')
                ->where('category.name like' . "'%" . $q . "%'")
                //->andWhere($whereBranch)
                // ->orWhere(['like', 'category.serialNo', $q])
                ->orWhere(['like', "REPLACE(category.serialNo, '-', '')", str_replace('-', '', $q)])
                ->orWhere(['like', 'category.commCode', $q])
                ->orWhere((['like', 'category.id', $q]))
                ->orWhere((['like', 'category.place', $q]))
                ->orWhere((['like', 'category.company', $q]))
                ->andWhere(['=', 'stocks.branch', Yii::$app->user->identity->branch])
                ->andWhere(['=', 'category.status', 0])
                ->andWhere(['=', 'stocks.type', 1])
                ->limit(60);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    public function actionGetInv($category)
    {
        $company = CompanyInfo::find()->one();
        if ($company->criteriaـvalue != 0) {
            $criteriaـvalue = $company->criteriaـvalue;
        } else {
            $criteriaـvalue = 1;
        }

        if ($company->rate != 0) {
            $rate = ($company->rate / 100);
            $maxPrice = 'CASE
        WHEN maxPrice >= ' . $criteriaـvalue . ' THEN round(maxPrice * "' . $rate . '" + maxPrice)
        ELSE maxPrice
        END  as maxPrice';

            $minPrice = 'CASE
        WHEN maxPrice >= ' . $criteriaـvalue . ' THEN round(minPrice * "' . $rate . '" + minPrice)
        ELSE minPrice
        END  as minPrice';
        } else {
            $maxPrice = 'maxPrice';
            $minPrice = 'minPrice';
        }

        $value = Stocks::find()
            ->leftJoin('prices', 'stocks.category = prices.category')
            ->select([
                'stocks.category AS id',
                'stocks.quantity as quantity',
                'costPrice',
                $maxPrice,
                $minPrice,
                'prices.minPrice2',
                'prices.minPrice3'
            ])
            ->Where(['stocks.branch' => Yii::$app->user->identity->branch])
            ->andwhere(['stocks.category' => $category])
            ->andwhere(['in', 'type',  [1]])
            ->asArray()->one();
        //echo json::encode($value);
        Yii::$app->response->format = Yii\web\Response::FORMAT_JSON;
        return $value;
    }
}
