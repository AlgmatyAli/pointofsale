<?php

use yii\db\Migration;

/**
 */
class m201123_200714_trigger_before_delete_sales extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(" 

        CREATE TRIGGER `before_delete_sales` BEFORE DELETE ON `sales`
 FOR EACH ROW INSERT into sales_deleted 
(  id
 , billId
 , at
 , clinet
 , payWay  
 , 	branch
 , 	total
 , 	paid
 , 	notes
 , 	path
 , 	type
 , 	deleviryAt
 , 	carpenter
 , 	upholstered
 , 	paintId
 , 	deleviryId
 , 	user_insert
 , 	created_at
 , 	user_update
 , 	update_at )
VALUES 
( old.id
 ,old.billId
,old.at
,old.clinet
,old.payWay
,old.branch
,old.total
,old.paid
,old.notes
,old.path
,old.type
,old.deleviryAt
,old.carpenter
,old.upholstered
,old.paintId
,old.deleviryId
,old.user_insert
,old.created_at
,old.user_update
,old.update_at)
        
        "
    );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201105_200712_trigger_update_prices cannot be reverted.\n";

        return false;
    }

    /*
    
    */
}
