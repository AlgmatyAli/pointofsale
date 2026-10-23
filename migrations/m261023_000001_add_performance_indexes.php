<?php

use yii\db\Migration;

/**
 * فهارس للأعمدة المستخدمة في البحث والتقارير والأرصدة (sales, purchases, receipt, stocks...).
 * إذا وُجد فهرس يبدأ بنفس الأعمدة لا يُضاف فهرس مكرر.
 * على جداول كبيرة قد تأخذ دقيقة أو أكثر؛ نفّذها في وقت لا يعمل فيه أحد على النظام.
 */
class m261023_000001_add_performance_indexes extends Migration
{
    private const INDEXES = [
        ['idx_sales_at', 'sales', ['at']],
        ['idx_sales_type_payway_client', 'sales', ['type', 'payWay', 'clinet', 'currancy']],
        ['idx_sales_deserving', 'sales', ['deserving']],
        ['idx_sales_deleviried', 'sales', ['deleviried']],
        ['idx_salesdetails_category_sales', 'salesDetails', ['category', 'salesId']],
        ['idx_purchases_at', 'purchases', ['at']],
        ['idx_purchases_type_payway_client', 'purchases', ['type', 'payWay', 'clinet', 'currancy']],
        ['idx_purchases_type_arrival', 'purchases', ['type', 'dateOfArrival']],
        ['idx_purchasesdetails_category_purchase', 'purchasesDetails', ['category', 'PurchasesId']],
        ['idx_receipt_client_type_currancy', 'receipt', ['clinet', 'type', 'currancy']],
        ['idx_receipt_at', 'receipt', ['at']],
        ['idx_stocks_category_branch_type', 'stocks', ['category', 'branch', 'type']],
        ['idx_stocks_branch_quantity', 'stocks', ['branch', 'quantity']],
        ['idx_disscount_clients_client_type', 'disscount_clients', ['client', 'type']],
    ];

    public function safeUp()
    {
        foreach (self::INDEXES as [$name, $table, $columns]) {
            if ($this->db->getTableSchema($table, true) === null) {
                echo "    > الجدول $table غير موجود، تم تخطيه\n";
                continue;
            }
            if ($this->hasIndexStartingWith($table, $columns)) {
                echo "    > يوجد فهرس على $table(" . implode(', ', $columns) . ")، تم تخطيه\n";
                continue;
            }
            $this->createIndex($name, $table, $columns);
        }
    }

    public function safeDown()
    {
        foreach (array_reverse(self::INDEXES) as [$name, $table]) {
            $exists = (new \yii\db\Query())->from('information_schema.STATISTICS')
                ->where(['TABLE_SCHEMA' => new \yii\db\Expression('DATABASE()'), 'TABLE_NAME' => $table, 'INDEX_NAME' => $name])
                ->exists($this->db);
            if ($exists) {
                $this->dropIndex($name, $table);
            }
        }
    }

    /** هل يوجد فهرس أعمدته الأولى هي نفس $columns وبنفس الترتيب؟ */
    private function hasIndexStartingWith(string $table, array $columns): bool
    {
        $rows = (new \yii\db\Query())->select(['INDEX_NAME', 'COLUMN_NAME', 'SEQ_IN_INDEX'])
            ->from('information_schema.STATISTICS')
            ->where(['TABLE_SCHEMA' => new \yii\db\Expression('DATABASE()'), 'TABLE_NAME' => $table])
            ->orderBy(['INDEX_NAME' => SORT_ASC, 'SEQ_IN_INDEX' => SORT_ASC])
            ->all($this->db);

        $indexes = [];
        foreach ($rows as $row) {
            $indexes[$row['INDEX_NAME']][] = strtolower($row['COLUMN_NAME']);
        }
        $want = array_map('strtolower', $columns);
        foreach ($indexes as $cols) {
            if (array_slice($cols, 0, count($want)) === $want) {
                return true;
            }
        }
        return false;
    }
}
