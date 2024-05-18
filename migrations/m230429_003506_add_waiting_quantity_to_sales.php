<?php

use yii\db\Migration;

/**
 * Class m230429_003506_add_waiting_quantity_to_sales
 */
class m230429_003506_add_waiting_quantity_to_sales extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('salesDetails', 'waitQnty' , $this->integer()->defaultValue(0)); 
        $this->addColumn('temp_invoice', 'waitQnty' , $this->integer()->defaultValue(0)); 
        $this->addColumn('company_info', 'waitQnty' , $this->integer()->defaultValue(0)); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230429_003506_add_waiting_quantity_to_sales cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230429_003506_add_waiting_quantity_to_sales cannot be reverted.\n";

        return false;
    }
    */
}
