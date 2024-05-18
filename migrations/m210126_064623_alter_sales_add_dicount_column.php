<?php

use yii\db\Migration;

/**
 * Class m201107_233451_create_admin_user
 */
class m210126_064623_alter_sales_add_dicount_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('sales','disscount', $this->decimal(9,3)->after('total')); 
        $this->addColumn('category','place', $this->text()->after('status'));
        
      
        $this->update('sales', ['disscount' => 0]);
        $this->update('category', ['place' => '0']);
    
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
