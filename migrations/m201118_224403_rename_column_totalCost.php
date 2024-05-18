<?php

use yii\db\Migration;

/**
 * Class m201118_224403_rename_column_totalCost
 */
class m201118_224403_rename_column_totalCost extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('temp_invoice_purchase', 'totalPrice', 'state');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201118_224403_rename_column_totalCost cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201118_224403_rename_column_totalCost cannot be reverted.\n";

        return false;
    }
    */
}
