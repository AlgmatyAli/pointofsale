<?php

use yii\db\Migration;

/**
 * Class m201203_051455_costTotal
 */
class m201226_182151_add_price_group_to_sales_delete_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn( 'sales_deleted','price_group','enum(
            "زبون"
            ,"مندوب"
            ,"بائع"
            ,"بائع جمله")');
       
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
