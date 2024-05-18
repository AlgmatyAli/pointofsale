<?php

use yii\db\Migration;

/**
 */
class m201123_200713_trigger_before_delete_sales_detals extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(" 

        CREATE TRIGGER `before_delete_sales_detals` BEFORE DELETE ON `salesDetails`
        FOR EACH ROW INSERT into salesDetails_deleted
       (id	 , 
       salesId	 , 
       category	 , 
       quantity	 , 
       costPrice	 , 
       salePrice	 , 
       box	 , 
       expire	  )
       VALUES
       (old.id	 , 
       old.salesId	 , 
       old.category	 , 
       old.quantity	 , 
       old.costPrice	 , 
       old.salePrice	 , 
       old.box	 , 
       old.expire	  )
        
        "
    );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        return false;
    }

    
}
