<?php

use yii\db\Migration;

/**
 * Class m231203_051455_costTotal
 */
class m231226_182151_add_price_group_to_sales_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->addColumn( 'sales','price_group','enum(
        //     "زبون"
        //     ,"مندوب"
        //     ,"بائــع"
        //     ,"بائع جمله")');
       
    
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
