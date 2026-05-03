<?php

use yii\db\Migration;

class m260502_213326_add_payWay_table_and_feilds extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%payment_types}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);

        $this->insert('payment_types', array(
            'id' => 1,
            'name' => 'كـاش',
        ));
        $this->insert('payment_types', array(
            'id' => 2,
            'name' => 'بطاقة',
        ));
        $this->insert('payment_types', array(
            'id' => 3,
            'name' => 'صـك',
        ));
        $this->insert('payment_types', array(
            'id' => 4,
            'name' => 'حوالة',
        ));

        $this->dropForeignKey(
            'fk-sales-carpenter',
            '{{%sales}}'
        );
        $this->dropForeignKey(
            'fk-sales-upholstered',
            '{{%sales}}'
        );
        $this->dropForeignKey(
            'fk-sales-deleviryId',
            '{{%sales}}'
        );
        $this->dropForeignKey(
            'fk-sales-paint',
            '{{%sales}}'
        );
        $this->dropIndex(
            'idx-sales-carpenter',
            '{{%sales}}'
        );
        $this->dropIndex(
            'idx-sales-upholstered',
            '{{%sales}}'
        );
        $this->dropIndex(
            'idx-sales-deleviryId',
            '{{%sales}}'
        );
        $this->dropIndex(
            'idx-sales-user_paint',
            '{{%sales}}'
        );
        $this->dropColumn('{{%sales}}', 'carpenter');
        $this->dropColumn('{{%sales}}', 'upholstered');
        $this->dropColumn('{{%sales}}', 'paintId');
        $this->dropColumn('{{%sales}}', 'deleviryId');
        $this->dropColumn('{{%sales}}', 'price_group');
        $this->dropColumn('{{%receipt}}', 'payWay');

        $this->addColumn('{{%sales}}', 'payment_type', $this->integer()->notNull()->after('notes')->defaultValue(1));
        $this->addColumn('{{%purchases}}', 'payment_type', $this->integer()->notNull()->after('notes')->defaultValue(1));
        $this->addColumn('{{%receipt}}', 'payment_type', $this->integer()->notNull()->after('why')->defaultValue(1));
        $this->addColumn('{{%expenses}}', 'payment_type', $this->integer()->notNull()->after('why')->defaultValue(1));

        $this->execute(
            '
        DROP TRIGGER IF EXISTS `before_delete`;
        CREATE TRIGGER `before_delete` BEFORE DELETE ON `receipt`
        FOR EACH ROW INSERT INTO receipt_arch(rId, clinet, at, value, why, payWay, type)
        VALUES(old.rId, old.clinet, old.at, old.value, old.why, old.payment_type, old.type)'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260502_213326_add_payWay_table_and_feilds cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260502_213326_add_payWay_table_and_feilds cannot be reverted.\n";

        return false;
    }
    */
}
