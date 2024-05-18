<?php

use yii\db\Migration;

/**
 * Class m201203_051455_costTotal
 */
class m201226_182150_add_currancy_to_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company_info', 'currancy', $this->integer(11));
        $this->addColumn('receipt', 'currancy', $this->integer(11));
       
        $this->addForeignKey('FK_company_info_currany', 'company_info', 'currancy', 'currancy', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK_receipt_currancy', 'receipt', 'currancy', 'currancy', 'id', 'CASCADE', 'CASCADE');

    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // echo "m201203_051455_costTotal cannot be reverted.\n";

        // return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201203_051455_costTotal cannot be reverted.\n";

        return false;
    }
    */
}
