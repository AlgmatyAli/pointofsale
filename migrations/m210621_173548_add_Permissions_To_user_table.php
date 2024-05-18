<?php

use yii\db\Migration;

/**
 * Class m210621_173548_add_Permissions_To_user_table
 */
class m210621_173548_add_Permissions_To_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user','seeCostPrice', $this->integer()->after('permission'));
        $this->addColumn('user','editSalePrice', $this->integer()->after('seeCostPrice'));
        $this->addColumn('user','makeDiscount', $this->integer()->after('editSalePrice'));
        $this->addColumn('user','maxDiscount', $this->decimal(10,3)->after('makeDiscount'));
        $this->addColumn('user','maxExpenses', $this->decimal(10,3)->after('maxDiscount'));
        $this->addColumn('user','maxReceipt', $this->decimal(10,3)->after('maxExpenses'));
        $this->addColumn('user','printPurtchaseInvoice', $this->integer()->after('maxReceipt'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210621_173548_add_Permissions_To_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210621_173548_add_Permissions_To_user_table cannot be reverted.\n";

        return false;
    }
    */
}
