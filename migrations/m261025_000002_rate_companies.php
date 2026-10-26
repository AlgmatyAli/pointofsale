<?php

use yii\db\Migration;

/**
 * نسبة الزيادة العامة (company_info.rate) تُطبَّق على أصناف شركات مختارة، مع شرط القيمة (criteriaـvalue) إن وُجد:
 *  - company_info.rateCompanies: أرقام itemCompany مفصولة بفاصلة، وفارغ = كل الأصناف.
 *  - حذف itemCompany.rate إن وُجد (أُضيف مؤقتاً ثم أُلغي).
 * آمن لإعادة التشغيل.
 */
class m261025_000002_rate_companies extends Migration
{
    private function companyInfoTable()
    {
        return \app\models\CompanyInfo::tableName();
    }

    public function safeUp()
    {
        $info = $this->db->getTableSchema($this->companyInfoTable(), true);
        if (!isset($info->columns['rateCompanies'])) {
            $this->addColumn($this->companyInfoTable(), 'rateCompanies', $this->text()->null()->comment('itemCompany.id مفصولة بفاصلة، فارغ = كل الأصناف'));
        }

        $itemCompany = $this->db->getTableSchema('itemCompany', true);
        if ($itemCompany !== null && isset($itemCompany->columns['rate'])) {
            $this->dropColumn('itemCompany', 'rate');
        }
    }

    public function safeDown()
    {
        $info = $this->db->getTableSchema($this->companyInfoTable(), true);
        if (isset($info->columns['rateCompanies'])) {
            $this->dropColumn($this->companyInfoTable(), 'rateCompanies');
        }
    }
}
