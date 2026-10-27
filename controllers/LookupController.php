<?php

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Response;

/**
 * بحث Ajax لقوائم العملاء والأصناف (app\widgets\LookupSelect2).
 * يتطلب تسجيل الدخول فقط (الحماية العامة في config/web.php).
 */
class LookupController extends Controller
{
    private const PAGE_SIZE = 30;

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    /** lookup/client?q=..&types=0,2 */
    public function actionClient($q = '', $types = '', $page = 1)
    {
        $query = (new Query())->select(['id', 'text' => 'name'])->from('client');

        if ($types !== '') {
            $query->andWhere(['type' => array_map('intval', explode(',', (string) $types))]);
        }

        // المستخدم المرتبط بعملاء محددين يرى عملاءه فقط
        $identity = Yii::$app->user->identity;
        if ($identity->client != null) {
            $query->andWhere(['id' => array_map('intval', explode(',', $identity->client))]);
        }

        $q = trim(mb_substr((string) $q, 0, 100));
        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'name', '%' . str_replace(' ', '%', $q) . '%', false],
                ['like', 'phone', $q],
                ['id' => ctype_digit($q) ? (int) $q : -1],
            ]);
        }

        return $this->page($query->orderBy(['id' => SORT_ASC]), $page);
    }

    /** lookup/category?q=.. (نفس طرق البحث في شاشة البيع: الاسم بالحروف الأولى، الرقم، الباركود، الشركة، التصنيف) */
    public function actionCategory($q = '', $page = 1)
    {
        $query = (new Query())->select(['id', 'text' => 'name'])->from('category');

        $q = trim(mb_substr((string) $q, 0, 100));
        if ($q !== '') {
            $qSearch = str_replace(' ', '%', $q);
            $query->andWhere([
                'or',
                ['like', 'name', '%' . $qSearch . '%', false],
                ['like', "REPLACE(serialNo, '-', '')", str_replace('-', '', $qSearch)],
                ['like', 'commCode', $qSearch],
                ['like', 'company', $qSearch],
                ['like', 'class', $qSearch],
                ['id' => ctype_digit($q) ? (int) $q : -1],
            ]);
        }

        return $this->page($query->orderBy(['id' => SORT_ASC]), $page);
    }

    /** lookup/company?q=.. الشركات المصنّعة (itemCompany) */
    public function actionCompany($q = '', $page = 1)
    {
        $query = (new Query())->select(['id', 'text' => 'name'])->from('itemCompany');

        $q = trim(mb_substr((string) $q, 0, 100));
        if ($q !== '') {
            $query->andWhere(['like', 'name', '%' . str_replace(' ', '%', $q) . '%', false]);
        }

        return $this->page($query->orderBy(['name' => SORT_ASC]), $page);
    }

    private function page(Query $query, $page): array
    {
        $page = max(1, (int) $page);
        $rows = $query->limit(self::PAGE_SIZE + 1)->offset(($page - 1) * self::PAGE_SIZE)->all();
        $more = count($rows) > self::PAGE_SIZE;
        return [
            'results' => array_slice($rows, 0, self::PAGE_SIZE),
            'pagination' => ['more' => $more],
        ];
    }
}
