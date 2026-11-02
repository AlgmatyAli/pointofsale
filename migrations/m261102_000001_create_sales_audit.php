<?php

use yii\db\Migration;

/**
 * سجل تعديلات فواتير المبيعات بعد حفظها (salesAudit): طريقة الدفع، الزبون، الإجمالي، الخصم، المدفوع.
 * يظهر للمدير في الرئيسية حتى يراجعه. بلا مفتاح أجنبي حتى لا يُعاد بناء جدول sales. آمن لإعادة التشغيل.
 */
class m261102_000001_create_sales_audit extends Migration
{
    public function safeUp()
    {
        if ($this->db->getTableSchema('salesAudit', true) !== null) {
            return;
        }
        $this->createTable('salesAudit', [
            'id' => $this->primaryKey(),
            'salesId' => $this->integer()->notNull(),
            'billId' => $this->integer()->null(),
            'attribute' => $this->string(30)->notNull()->comment('payWay, clinet, total, disscount, paid'),
            'oldValue' => $this->string(255)->null(),
            'newValue' => $this->string(255)->null(),
            'user_id' => $this->integer()->null(),
            'created_at' => $this->dateTime()->notNull(),
            'reviewed' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'reviewed_by' => $this->integer()->null(),
            'reviewed_at' => $this->dateTime()->null(),
        ], 'ENGINE=InnoDB');
        $this->createIndex('idx-salesAudit-reviewed', 'salesAudit', ['reviewed', 'salesId']);
    }

    public function safeDown()
    {
        if ($this->db->getTableSchema('salesAudit', true) !== null) {
            $this->dropTable('salesAudit');
        }
    }
}
