<?php

use yii\db\Migration;

/**
 * Class m210709_153618_alter_sales_add
 */
class m210709_153618_alter_sales_add extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('sales', 'deleviried', $this->integer()->after('deleviryId'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210709_153618_alter_sales_add cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210709_153618_alter_sales_add cannot be reverted.\n";

        return false;
    }
    */
}
