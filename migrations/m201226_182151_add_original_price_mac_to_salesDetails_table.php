<?php

use yii\db\Migration;

/**
 * Class m201203_051455_costTotal
 */
class m201226_182151_add_original_price_mac_to_salesDetails_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn( 'salesDetails','original_price', $this->decimal(9,3)->after('salePrice'));
        $this->addColumn('salesDetails','mac_address', $this->text()->after('box'));
         
       
        
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // echo "m201203_051455_costTotal cannot be reverted.\n";

        // return false;
    }

    
}
