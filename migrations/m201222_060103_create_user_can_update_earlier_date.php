<?php

use yii\db\Migration;

/**
 * Class m201107_233451_create_admin_user
 */
class m201222_060103_create_user_can_update_earlier_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('auth_item',array(
            'name' => 'update_earlier_date',
            'description' => 'مشاهدة سعر التكلفة في شاشة المبيعات',
            'type'=>'2',
           
     ));
        $this->insert('auth_item_child',array(
            'parent' => 'مدير',
            'child' => 'update_earlier_date',
             
        
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
