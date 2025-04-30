<?php

namespace app\controllers;

use Yii;
use app\models\BalanceHistory;
use app\models\BalanceHistorySearch;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * BalanceHistoryController implements the CRUD actions for BalanceHistory model.
 */
class BalanceHistoryController extends Controller
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
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'histrans'],
                        'roles' => ['clientDepts'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Lists all BalanceHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BalanceHistorySearch();
        $searchModel->currancy = 0;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionHistrans()
    {
        $model = new BalanceHistory();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->allData != 0) {
                $model->min_date = '2020-01-30';
                $model->max_date = date('Y-m-d');
            }

            $sqlSum = " SELECT
            abs(sum(balance_history.dept)) as balance FROM balance_history
            where balance_history.client_type in(" . $model->client_type . ")
            and balance_history.clinet = " . $model->clinet . "
            and currancy !=1 and balance_history.AT < '" . $model->min_date . "'  ";
            $connection = Yii::$app->db;
            $data = $connection->createCommand($sqlSum);
            $lastBalance = $data->queryAll();

            $sql = " SELECT balance_history.clinet, balance_history.AT as trandate, balance_history.client_type,
                        balance_history.dept as sader, balance_history.credt as wared, client.name as name, balance_history.kind, balance_history.BillId, balance_history.printId, balance_history.deleviried, balance_history.currancy
        FROM balance_history, client
        where balance_history.client_type in(" . $model->client_type . ") and balance_history.clinet = " . $model->clinet . "
        and balance_history.AT between '" . $model->min_date . "' and '" . $model->max_date . "'
        and balance_history.clinet = client.id
        and currancy !=1 
        order by  balance_history.AT, balance_history.clinet";
            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $info = $data->queryAll();

            if (count($info) === 0) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Pardon There is no data to view"));
                return $this->redirect(['histrans', 'model' => $model]);
            }

            return $this->render('histransrep', [
                'models' => $info,
                'min_date' => $model->min_date,
                'max_date' => $model->max_date,
                'sumwared' => 0,
                'sumsader' => 0,
                'sum' => 0,
                'coun' => 1,
                'count' => 0,
                'lastBalance' => $lastBalance,
                'balance' => 0,
                'id' => null,
                'deleviried' => 0,
            ]);
        }

        return $this->render('histrans', [
            'model' => $model,
        ]);
    }
}
