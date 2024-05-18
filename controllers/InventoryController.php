<?php

namespace app\controllers;

use Yii;
use app\models\Inventory;
use app\models\InventorySearch;
use app\models\InventorySearch_;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * InventoryController implements the CRUD actions for Inventory model.
 */
class InventoryController extends Controller
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
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'list', 'price-list', 'stock-taking'],
                        'roles' => ['inventory']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all Inventory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InventorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $sql = " 
        SELECT sum(quantity) quantity, sum(inventory.quantity*prices.costPrice) TotalCost, sum(inventory.quantity*prices.maxPrice) Price 
        FROM inventory, prices WHERE inventory.id=prices.category
        ";    

        $connection = \Yii::$app->db;
        $data = $connection->createCommand($sql);
        $info = $data->queryAll();

        $sql = " 
        SELECT sum(quantity) quantity, sum(inventory.quantity*prices.costPrice) TotalCost, sum(inventory.quantity*prices.maxPrice) Price 
        FROM inventory, prices WHERE inventory.id=prices.category and inventory.type = 3
        ";    

        $connection = \Yii::$app->db;
        $data = $connection->createCommand($sql);
        $inventory = $data->queryAll();
      
        // var_dump($info);
        // die();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'infos'=>$info,
            'inventory' =>$inventory,
            'count' => 0,
            'TotalCost'=> 0,
            'totalCostWOHangOut' =>0,
            'price' =>0,
        ]);
    }

    /**
     * Lists all Inventory models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new InventorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPriceList()
    {
        $searchModel = new InventorySearch_();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $sql = " 
        SELECT sum(quantity) quantity, sum(inventory.quantity*prices.costPrice) TotalCost, sum(inventory.quantity*prices.maxPrice) Price 
        FROM inventory, prices WHERE inventory.id=prices.category
        ";    

        $connection = \Yii::$app->db;
        $data = $connection->createCommand($sql);
        $info = $data->queryAll();
        
        return $this->render('priceList', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'infos'=>$info,
        ]);
    }

    public function actionStockTaking()
    {
        $model = new Inventory();
        if ($model->load(Yii::$app->request->post())) {
            
        $sql = " select inventory.id, sum(inventory.quantity)as quantity, max(inventory.name)as name, 
        max(inventory.serialNo) as serialNo, max(inventory.company) as company FROM inventory 
        where inventory.id not in (select category from arrangementDetails, arrangement 
        where arrangement.id = arrangementDetails.arrangement 
        and arrangementDetails.stockTaking = 1
        and DATE_FORMAT(arrangement.at, '%Y') = ".$model->at." ) 
        GROUP by id HAVING sum(inventory.quantity) <> 0" ;    
    
        $connection = \Yii::$app->db;
        $data = $connection->createCommand($sql);
        $info = $data->queryAll();
         if ($info == null){
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
