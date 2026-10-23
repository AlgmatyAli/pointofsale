<?php

namespace app\controllers;

use Yii;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\CompanyInfo;
use app\models\SalesDetails;
use app\models\Stocks;
use yii\db\Query;

class ExceptController extends Controller
{
    public function behaviors()
    {
        return [
            // كل نقاط البحث هذه تتطلب تسجيل الدخول (كانت مفتوحة بلا قيد)
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * أعمدة نتائج البحث عن الأصناف: التكلفة لمن يملك seeCostPrice فقط، والسعر الأدنى للموظف دون العميل.
     */
    private function itemColumns(array $extra = [])
    {
        $identity = Yii::$app->user->identity;
        $cols = [
            'category.id',
            'category.name AS text',
            'company AS company',
            'stocks.quantity as quantity',
            'maxPrice as maxPrice',
            'serialNo AS serialNo',
            'place',
            'commCode',
        ];
        if ($identity->seeCostPrice == 1) {
            $cols[] = 'costPrice as costPrice';
        }
        if ($identity->client == null) {
            $cols[] = 'minPrice AS minPrice';
        }
        return array_merge($cols, $extra);
    }

    public function actionItemlist($q = null, $id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $q = str_replace(' ', '%', mb_substr((string) $q, 0, 100));
            $query = new Query;
            $query->select($this->itemColumns([
                'branches.name AS BRNAME',
                'branches.id AS branchId',
            ]))
                ->from('category')
                ->leftJoin('prices', 'prices.category = category.id')
                ->leftJoin('stocks', 'stocks.category = category.id')
                ->leftJoin('branches', 'branches.id = stocks.branch')
                // معاملات مربوطة بدل دمج النص في الاستعلام (كانت ثغرة حقن SQL)
                ->where(['like', 'category.name', '%' . $q . '%', false])   // false: يبقى % (من المسافات) علامة بحث مفتوح كما في الأصل
                ->orWhere(['like', "REPLACE(category.serialNo, '-', '')", str_replace('-', '', $q)])
                ->orWhere(['like', 'category.commCode', $q])
                ->orWhere(['like', 'category.id', $q])
                ->orWhere(['like', 'category.place', $q])
                ->orWhere(['like', 'category.company', $q])
                ->andWhere(['=', 'category.status', 0])
                ->andWhere(['!=', 'stocks.type', 3])
                ->limit(60);
            $out['results'] = array_values($query->all());
        }
        return $out;
    }

    public function actionGetInv($category)
    {
        $identity = Yii::$app->user->identity;
        if ($identity === null) {
            throw new \yii\web\ForbiddenHttpException();
        }

        $company = CompanyInfo::find()->one();
        if ($company->criteriaـvalue != 0) {
            $criteriaـvalue = (float) $company->criteriaـvalue; // رقم فقط لأنه يدخل في نص SQL
        } else {
            $criteriaـvalue = 1;
        }

        if ($company->rate != 0) {
            $rate = ((float) $company->rate / 100);
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

        // الأعمدة الأساسية المتاحة للجميع
        $select = [
            'stocks.category AS id',
            'stocks.quantity as quantity',
            $maxPrice,
        ];

        // الموظف فقط (وليس العميل) يرى الأسعار الدنيا
        if ($identity->client == null) {
            $select[] = $minPrice;
            $select[] = 'prices.minPrice2';
            $select[] = 'prices.minPrice3';

            // سعر التكلفة لمن يملك الصلاحية فقط
            if ($identity->seeCostPrice == 1) {
                $select[] = 'costPrice';
            }
        }

        $value = Stocks::find()
            ->leftJoin('prices', 'stocks.category = prices.category')
            ->select($select)
            // ->andWhere(['stocks.branch' => $identity->branch])
            ->andWhere(['stocks.category' => $category])
            ->andWhere(['in', 'type', [1, 2]])
            ->asArray()->one();

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $value;
    }

    public function actionItemlistid($q = null, $id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $q = str_replace(' ', '%', mb_substr((string) $q, 0, 100));
            $query = new Query;
            $query->select($this->itemColumns([
                'CASE
            WHEN `type` =1
            THEN "متوفر"
            WHEN `type` =2
            THEN "متوفر"
            ELSE "قريبا" END as type'
            ]))
                ->from('category')
                ->leftJoin('prices', 'prices.category = category.id')
                ->leftJoin('stocks', 'stocks.category = category.id')
                ->where(['=', 'category.id', $q])
                ->andWhere(['=', 'category.status', 0])
                ->andWhere(['!=', 'stocks.type', 3])
                ->limit(60);
            $out['results'] = array_values($query->all());
        }
        return $out;
    }

    /**
     * حركات بيع سابقة لعميل (لتعبئة المرتجع). مستخدم العميل يرى عملاءه فقط، والتكلفة لمن يملك seeCostPrice.
     */
    public function actionItemListByClient($q = null,  $client = null, $id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $identity = Yii::$app->user->identity;
        $out = ['results' => ['id' => '', 'text' => '']];

        if ($identity->client !== null && $identity->client !== '') {
            $allowed = array_map('trim', explode(',', (string) $identity->client));
            if (!in_array((string) $client, $allowed, true)) {
                return $out;
            }
        }

        if (!is_null($q)) {
            $q = str_replace(' ', '%', mb_substr((string) $q, 0, 100));
            $cols = [
                'salesDetails.id as id',
                'sales.at',
                'sales.clinet',
                'salesDetails.category as category',
                'category.name AS text',
                'category.serialNo',
                'category.company',
                'category.commCode',
                'salesDetails.quantity',
                'salesDetails.salePrice',
            ];
            if ($identity->seeCostPrice == 1) {
                $cols[] = 'salesDetails.costPrice';
            }
            $query = new Query;
            $query->select($cols)
                ->from('salesDetails')
                ->leftJoin('sales', 'salesDetails.salesId = sales.id')
                ->leftJoin('client', 'sales.clinet = client.id')
                ->leftJoin('category', 'category.id = salesDetails.category ')
                ->where(['sales.clinet' => $client])
                ->andWhere(['like', 'category.name', '%' . $q . '%', false])
                ->andWhere(['=', 'category.status', 0])
                ->andWhere(['=', 'sales.type', '1'])
                ->limit(60)
                ->orderBy(['sales.at' => SORT_ASC]);
            $out['results'] = array_values($query->all());
        }
        return $out;
    }
}
