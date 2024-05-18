<?php

use yii\db\Migration;

/**
 * Class 
 */
class m221226_182150_add_agent_to_sale_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('sales', 'agent', $this->integer());
        $this->addForeignKey('FK_sales_agent', 'sales', 'agent', 'agent', 'id', 'CASCADE', 'CASCADE');
      
        $this->addColumn('receipt', 'agent', $this->integer());
        $this->addForeignKey('FK_receipt_agent', 'receipt', 'agent', 'agent', 'id', 'CASCADE', 'CASCADE');
        
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       
    }

   
}
