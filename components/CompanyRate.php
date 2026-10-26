<?php

namespace app\components;

use app\models\CompanyInfo;

/**
 * نسبة الزيادة العامة (company_info.rate) على أسعار البيع عند عرض الأصناف في شاشات البيع.
 * إذا كانت النسبة لا تساوي 0 تُطبَّق على الصنف عندما تتحقق كل الشروط المحددة معاً:
 *  - قيمة البحث (criteriaـvalue) لا تساوي 0: سعر البيع >= هذه القيمة.
 *  - الشركات (rateCompanies) غير فارغة: شركة الصنف من الشركات المختارة.
 * الشرط غير المحدد لا يُقيِّد، وإذا لم يُحدد أي منهما تُطبَّق على كل الأصناف. لا تغيّر الأسعار المحفوظة.
 *
 * الاستعلام يجب أن يحتوي جدولي category و prices.
 */
class CompanyRate
{
    /** @return string[] [maxPrice, minPrice] كتعابير SQL مع الأسماء المستعارة */
    public static function priceColumns(): array
    {
        $company = CompanyInfo::current();
        if ($company === null || (float) $company->rate == 0) {
            return ['prices.maxPrice AS maxPrice', 'prices.minPrice AS minPrice'];
        }

        // أي قيمة تدخل في نص SQL تُحوَّل لرقم أولاً
        $rate = sprintf('%.6F', (float) $company->rate / 100);
        $conditions = [];

        $criteria = (float) $company->{"criteria\u{0640}value"};
        if ($criteria != 0) {
            $conditions[] = 'prices.maxPrice >= ' . sprintf('%.6F', $criteria);
        }

        $ids = array_filter(array_map('intval', explode(',', (string) $company->rateCompanies)));
        if ($ids) {
            $conditions[] = 'category.companyId IN (' . SqlSafe::intList($ids) . ')';
        }

        if (!$conditions) {
            return [
                "ROUND(prices.maxPrice * $rate + prices.maxPrice) AS maxPrice",
                "ROUND(prices.minPrice * $rate + prices.minPrice) AS minPrice",
            ];
        }

        $when = implode(' AND ', $conditions);
        return [
            "CASE WHEN $when THEN ROUND(prices.maxPrice * $rate + prices.maxPrice) ELSE prices.maxPrice END AS maxPrice",
            "CASE WHEN $when THEN ROUND(prices.minPrice * $rate + prices.minPrice) ELSE prices.minPrice END AS minPrice",
        ];
    }
}
