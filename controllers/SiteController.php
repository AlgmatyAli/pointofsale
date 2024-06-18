<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Sales;
use app\models\base\TempInvoicePurchase;
use app\models\CompanyInfo;
use app\models\TempInvoice;
use app\models\InventorySearch;
use app\models\Prices;
use app\models\Purchases;
use app\models\Stocks;
use yii\data\ActiveDataProvider;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'q-balance'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['q-balance'],
                        'roles' => ['inventory'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(Yii::$app->urlManager->createUrl(["site/login"]));
        } else {


            $count = 0;
            $counter = 0;
            $tempPurchase = 0;
            $tempSales = 0;
            $moreRequest = 0;
            $deleviried = 0;
            $amount = 0;
            $dateOfArrival = 0;
            $zeroQ = 0;

            $sql = " SELECT `category`.`id`, max(category.name) AS `name`,
        max(category.serialNo) AS `serialNo`, max(category.minimum) AS `minimum`,
        sum(category.quantity) AS `quantity` FROM `stocks`
        LEFT JOIN `category`
        ON stocks.category = category.id
        GROUP BY `category`.`id`, `category`.`minimum`
        HAVING sum(stocks.quantity) <= category.minimum";

            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $reorder = $data->queryAll();

            foreach ($reorder as $value) {
                $count += 1;
            }

            $sql = " SELECT `category`.`id`, max(category.name) AS `name`,
        max(category.serialNo) AS `serialNo`, max(category.minimum) AS `minimum`,
        sum(category.quantity) AS `quantity` FROM `stocks` LEFT JOIN `category`
        ON stocks.category = category.id
        Where category.moreRequest  = 1
        GROUP BY `category`.`id`, `category`.`minimum`
        HAVING sum(stocks.quantity) <= category.minimum ";

            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $moreRequest = $data->queryAll();

            foreach ($moreRequest as $request) {
                $counter += 1;
            }

            $sql = " SELECT sales.id, client.name , sales.deserving from sales, client
        where sales.clinet = client.id and sales.deserving - CURRENT_DATE() <= 0";

            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $soon = $data->queryAll();

            foreach ($soon as $recent) {
                $amount += 1;
            }

            $tempPurchase = TempInvoicePurchase::find()->select('created_by')
                ->where(['state' => '0', 'created_by' => Yii::$app->user->identity->id])->distinct()->count();

            $tempSales = TempInvoice::find()->select('created_by')
                ->where(['created_by' => Yii::$app->user->identity->id])->distinct()->count();

            $deleviried = Sales::find()->select('id')->where(['deleviried' => 0])->distinct()->count();

            // $sql = " SELECT max(client.name) cleintName, sum(total) countt from sales, client
            // where sales.clinet = client.id and sales.branch = ".'1'." group by sales.clinet
            // HAVING sum(total)>=50 LIMIT 10";

            // $connection = Yii::$app->db;
            // $data = $connection->createCommand($sql);
            // $infos = $data->queryAll();

            // $sql = "select salesDetails.category, sum(salesDetails.quantity) counts, max(category.name) categoryName
            //         from salesDetails, category
            //         where salesDetails.category=category.id
            //         group by salesDetails.category
            //         HAVING sum(salesDetails.quantity) >=2
            //         Order By counts desc LIMIT 10";

            // $connection = Yii::$app->db;
            // $data = $connection->createCommand($sql);
            // $details = $data->queryAll();

            $dateOfArrival = Purchases::find()->select(['count(*) as dateOfArrival'])
                ->where('DATEDIFF(dateOfArrival, sysdate()) <=0 ')
                ->andWhere(['=', 'type', '3'])
                ->asArray()
                ->one();

            $compare = Prices::find()
                ->leftJoin('stocks', 'prices.category = stocks.category')
                ->select('id')
                ->where('maxPrice <= costPrice')
                ->andWhere('stocks.quantity > 0')
                ->count();

            $zeroQ = Stocks::find()->select('id')->where(['<', 'quantity', 0])->count();

            $model = new TempInvoice();
            $company = CompanyInfo::find()->one();
            return $this->render('index', [
                'reorder' => $count,
                'tempPurchase' => $tempPurchase,
                'tempSales' => $tempSales,
                'infos' => null,
                'details' => null,
                'request' => $counter,
                'deleviried' => $deleviried,
                'count' => null,
                'bestCustomer' => null,
                'counts' => null,
                'bestCategory' => null,
                'amount' => $amount,
                'events' => null,
                'arabic_date' => $this::ArabicDate(),
                'dateOfArrival' => $dateOfArrival,
                'compare' => $compare,
                'zeroQ' => $zeroQ,
                'model' => $model,
                'company' => $company
            ]);
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {

        if (!Yii::$app->user->isGuest) {

            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {

        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionReport()
    {
        return $this->render('report');
    }

    public function actionDashboard()
    {
        return $this->render('Dashboard');
    }

    public function actionItems()
    {
        $this->layout = 'saleLayout';
        $searchModel = new InventorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('items', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    private static function ArabicDate()
    {
        $months = array(
            "Jan" => "يناير", "Feb" => "فبراير", "Mar" => "مارس", "Apr" => "أبريل",
            "May" => "مايو", "Jun" => "يونيو", "Jul" => "يوليو", "Aug" => "أغسطس", "Sep" => "سبتمبر",
            "Oct" => "أكتوبر", "Nov" => "نوفمبر", "Dec" => "ديسمبر"
        );
        $yourDate = date('y-m-d'); // The Current Date
        $enMonth = date("M", strtotime($yourDate));
        foreach ($months as $en => $ar) {
            if ($en == $enMonth) {
                $arMonth = $ar;
            }
        }

        $find = array("Sat", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri");
        $replace = array("السبت", "الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة");
        $arDayFormat = date('D'); // The Current Day
        $arDay = str_replace($find, $replace, $arDayFormat);

        header('Content-Type: text/html; charset=utf-8');
        $standard = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $easternArabicSymbols = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $currentDate = $arDay . ' ' . date('d') . ' / ' . $arMonth . ' / ' . date('Y');
        $arabicDate = str_replace($standard, $easternArabicSymbols, $currentDate);

        return $arabicDate;
    }

    public function actionQBalance()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Stocks::find()
                ->select(['stocks.category', 'stocks.quantity quantity', 'Totalinventory.quantity as tq'])
                ->leftJoin('Totalinventory', 'stocks.category = Totalinventory.id')
                ->where('stocks.quantity<>Totalinventory.quantity')
                ->andWhere('stocks.branch = Totalinventory.branch')
                ->andWhere('stocks.type = Totalinventory.type'),

            'pagination' => [
                'pageSize' => 100
            ],
        ]);
        return $this->render('qBalance', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
