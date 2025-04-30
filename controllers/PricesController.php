<?php

namespace app\controllers;

use Yii;
use app\models\Prices;
use app\models\PricesSearch;
use app\models\PricesWithCategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\filters\AccessControl;

/**
 * PricesController implements the CRUD actions for Prices model.
 */
class PricesController extends Controller
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
                        'actions' => ['index', 'catalogue', 'index_', 'index-with-qnty', 'compare', 'update', 'create1'],
                        'roles' => ['prices'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Prices models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PricesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result = Prices::findOne($id);
            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['Prices']);

            $post['Prices'] = $posted;
            if ($result->load($post)) {

                $result->save(false);
                if (isset($posted['maxPrice'])) {
                    $outMessage = $result->maxPrice;
                } else {
                    $outMessage = $result->minPrice;
                }
                $output = $outMessage;
                $out = Json::encode(['output' => $output]);
                return $out;
            }
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndex_($category)
    {
        $searchModel = new PricesSearch();
        $searchModel->category = $category;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result = Prices::findOne($id);
            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['Prices']);

            $post['Prices'] = $posted;
            if ($result->load($post)) {

                $result->save(false);
                if (isset($posted['maxPrice'])) {
                    $outMessage = $result->maxPrice;
                } else {
                    $outMessage = $result->minPrice;
                }
                $output = $outMessage;
                $out = Json::encode(['output' => $output]);
                return $out;
            }
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCatalogue()
    {
        $searchModel = new PricesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('catalogue', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single Prices model.
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
     * Creates a new Prices model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Prices();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Prices model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->actionCompare();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Prices model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Prices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Prices the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Prices::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionIndexWithQnty()
    {
        $searchModel = new PricesWithCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result = Prices::findOne($id);
            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['Prices']);

            $post['Prices'] = $posted;
            if ($result->load($post)) {

                $result->save(false);
                if (isset($posted['maxPrice'])) {
                    $outMessage = $result->maxPrice;
                } else {
                    $outMessage = $result->minPrice;
                }
                $output = $outMessage;
                $out = Json::encode(['output' => $output]);
                return $out;
            }
        }
        return $this->render('indexWithQnty', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCompare()
    {
        $info = Prices::find()
            ->select(['prices.id', 'prices.category', 'prices.maxPrice', 'prices.costPrice', 'category.name', 'stocks.quantity'])
            ->leftJoin('category', 'prices.category = category.id')
            ->leftJoin('stocks', 'prices.category = stocks.category')
            ->where('maxPrice <= costPrice')
            ->andWhere('stocks.quantity > 0')
            ->orderBy('category')
            ->asArray()->all();
        return $this->render('compare', [
            'models' => $info,
            'count' => 0,
        ]);
    }

    public function actionCreate1()
    {
        $model = new Prices();
        $searchModel = new PricesWithCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($model->load(Yii::$app->request->post())) {
            $diff = $len = $maxPrice = $minPrice = 0;
            $items = Prices::find()->where(['between', 'maxPrice', $model->minPrice, $model->maxPrice])->all();
            foreach ($items as $value) {
                $maxPrice = round($value->maxPrice + ($value->maxPrice * ($model->percentage / 100)));
                $len = strlen($maxPrice);
                $val = substr($maxPrice, $len-1, $len);
                if ((int) $val < 5 && (int) $val != 0) {
                    $diff = 5 - (int) $val;
                    $maxPrice += $diff;
                }
                if ((int) $val > 5) {
                    $diff = 10 - (int) $val;
                    $maxPrice += $diff;
                }
                $minPrice = round($value->minPrice + ($value->minPrice * ($model->percentage / 100)));
                $len = strlen($minPrice);
                $val = substr($minPrice, $len-1, $len);
                if ((int) $val < 5 && (int) $val != 0) {
                    $diff = 5 - (int) $val;
                    $minPrice += $diff;
                }
                if ((int) $val > 5) {
                    $diff = 10 - (int) $val;
                    $minPrice += $diff;
                }

                Yii::$app->db->createCommand("UPDATE prices SET maxPrice = round(".$maxPrice."), minPrice = round(".$minPrice.") WHERE category = $value->category")->execute();
            }

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

        return $this->render('create1', [
            'model' => $model,
        ]);
    }
}
