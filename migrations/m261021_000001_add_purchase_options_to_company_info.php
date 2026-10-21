<?php

use yii\db\Migration;

/**
 * خياران في بيانات المؤسسة (قسم خيارات التشغيل) يتعلقان بفاتورة المشتريات:
 *  - avgCost: 1 = احتساب متوسط سعر التكلفة (الافتراضي، السلوك السابق)، 0 = تسجيل آخر سعر شراء.
 *  - editSalePriceOnPurchaseUpdate: الحالة الابتدائية لمفتاح «تعديل سعر البيع» في شاشة تعديل فاتورة مشتريات (1 مفعّل).
 * القيمة الافتراضية 1 فلا يتغير شيء حتى يغيّرها صاحب المتجر. آمن لإعادة التشغيل، واسم الجدول يؤخذ من الموديل.
 */
class m261021_000001_add_purchase_options_to_company_info extends Migration
{
    private function tableName()
    {
        return \app\models\CompanyInfo::tableName();
    }

    public function safeUp()
    {
        $table = $this->db->getTableSchema($this->tableName(), true);

        if (!isset($table->columns['avgCost'])) {
            $this->addColumn(
                $this->tableName(),
                'avgCost',
                $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('1 = متوسط سعر التكلفة، 0 = آخر سعر شراء')
            );
        }
        if (!isset($table->columns['editSalePriceOnPurchaseUpdate'])) {
            $this->addColumn(
                $this->tableName(),
                'editSalePriceOnPurchaseUpdate',
                $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('الحالة الابتدائية لمفتاح تعديل سعر البيع عند تعديل فاتورة مشتريات')
            );
        }
    }

    public function safeDown()
    {
        $table = $this->db->getTableSchema($this->tableName(), true);

        if (isset($table->columns['editSalePriceOnPurchaseUpdate'])) {
            $this->dropColumn($this->tableName(), 'editSalePriceOnPurchaseUpdate');
        }
        if (isset($table->columns['avgCost'])) {
            $this->dropColumn($this->tableName(), 'avgCost');
        }
    }
}
