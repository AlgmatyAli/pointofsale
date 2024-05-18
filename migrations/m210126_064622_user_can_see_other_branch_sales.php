<?php

use yii\db\Migration;

/**
 * Class m201107_233451_create_admin_user
 */
class m210126_064622_user_can_see_other_branch_sales extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('auth_item',array(
            'name' => 'can_see_other_branch_sales',
            'description' => 'امكانية مشاهدة مبيعات الفروع الاخري',
            'type'=>'2',
           
     ));
        $this->insert('auth_item_child',array(
            'parent' => 'مدير',
            'child' => 'can_see_other_branch_sales',
             
        
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
