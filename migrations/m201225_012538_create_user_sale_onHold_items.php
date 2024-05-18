<?php

use yii\db\Migration;

/**
 * Class m201107_233451_create_admin_user
 */
class m201225_012538_create_user_sale_onHold_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('auth_item',array(
            'name' => 'saleOnHoldItems',
            'description' => 'امكانية البيع من البضاعة المعلفة',
            'type'=>'2',
           
     ));
        $this->insert('auth_item_child',array(
            'parent' => 'مدير',
            'child' => 'saleOnHoldItems',
             
        
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
