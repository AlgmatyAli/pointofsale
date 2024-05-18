<?php

use yii\db\Migration;

/**
 * Class m230428_005101_sale_by_wholesale
 */
class m230428_005101_sale_by_wholesale extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('sales', 'wholesale' , $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230428_005101_sale_by_wholesale cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230428_005101_sale_by_wholesale cannot be reverted.\n";

        return false;
    }
    */
}
