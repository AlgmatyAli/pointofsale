<?php

use yii\db\Migration;

/**
 * Class m201107_233451_create_admin_user
 */
class m210126_064622_user_can_selling_by_costprice extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('auth_item',array(
            'name' => 'selling_by_costprice',
            'description' => 'امكانية البيع حتي بسعر التكلفة',
            'type'=>'2',
           
     ));
        $this->insert('auth_item_child',array(
            'parent' => 'مدير',
            'child' => 'selling_by_costprice',
             
        
    ));
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // echo "m201107_233451_create_admin_user cannot be reverted.\n";

        // return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201107_233451_create_admin_user cannot be reverted.\n";

        return false;
    }
    */
}
