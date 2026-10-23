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
                'class' => AccessControl::class,
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
                'class' => VerbFilter::class,
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


            // أرقام عامة للوحة الرئيسية: تُحسب بـ COUNT في قاعدة البيانات (بدل جلب كل الصفوف وعدّها)
            // وتُحفظ في الكاش 5 دقائق لأن هذه الصفحة تُفتح كثيراً
            $stats = Yii::$app->cache->getOrSet('site-index-stats', function () {
                $db = Yii::$app->db;
                $belowMinimum = "SELECT COUNT(*) FROM (
                    SELECT category.id FROM stocks
                    LEFT JOIN category ON stocks.category = category.id
                    %s
                    GROUP BY category.id, category.minimum
                    HAVING SUM(stocks.quantity) <= category.minimum) t";

                return [
                    'reorder' => (int) $db->createCommand(sprintf($belowMinimum, ''))->queryScalar(),
                    'request' => (int) $db->createCommand(sprintf($belowMinimum, 'WHERE category.moreRequest = 1'))->queryScalar(),
                    // نفس شرط deserving - CURRENT_DATE() <= 0 لكن بصيغة تستفيد من الفهرس
                    'amount' => (int) Sales::find()->where(['<=', 'deserving', new \yii\db\Expression('CURDATE()')])->count(),
                    'deleviried' => (int) Sales::find()->where(['deleviried' => 0])->count(),
                    'dateOfArrival' => (int) Purchases::find()
                        ->where(['type' => 3])
                        ->andWhere(['<=', 'dateOfArrival', new \yii\db\Expression('CURDATE()')])
                        ->count(),
                    'compare' => (int) Prices::find()
                        ->leftJoin('stocks', 'prices.category = stocks.category')
                        ->where('maxPrice <= costPrice')
                        ->andWhere('stocks.quantity > 0')
                        ->count(),
                    'zeroQ' => (int) Stocks::find()->where(['<', 'quantity', 0])->count(),
                ];
            }, 300);

            $userId = Yii::$app->user->identity->id;
            $tempPurchase = TempInvoicePurchase::find()->select('created_by')
                ->where(['state' => '0', 'created_by' => $userId])->distinct()->count();

            $tempSales = TempInvoice::find()->select('created_by')
                ->where(['created_by' => $userId])->distinct()->count();

            $count = $stats['reorder'];
            $counter = $stats['request'];
            $amount = $stats['amount'];
            $deleviried = $stats['deleviried'];
            $dateOfArrival = ['dateOfArrival' => $stats['dateOfArrival']];
            $compare = $stats['compare'];
            $zeroQ = $stats['zeroQ'];

            $model = new TempInvoice();
            $company = CompanyInfo::current();
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
