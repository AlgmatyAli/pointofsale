<?php

use yii\db\Migration;

/**
 * يمنع تكرار رقم السند (rId) لنفس النوع.
 * إن وُجدت أرقام مكررة حالياً لا يضيف الفهرس ويطبع تحذيراً، فيجب تصحيحها يدوياً ثم إعادة التشغيل.
 */
class m261013_000002_unique_receipt_rid extends Migration
{
    public function safeUp()
    {
        $dups = (new \yii\db\Query())->select(['type', 'rId', 'c' => 'COUNT(*)'])->from('receipt')
            ->groupBy(['type', 'rId'])->having('COUNT(*) > 1')->all($this->db);
        if ($dups) {
            echo "    ! توجد أرقام سندات مكررة (" . count($dups) . " حالة) - لم يُضف الفهرس. صحّحها أولاً.\n";
            return true;
        }
        $this->createIndex('ux_receipt_type_rid', 'receipt', ['type', 'rId'], true);
    }

    public function safeDown()
    {
        $this->dropIndex('ux_receipt_type_rid', 'receipt');
    }
}
