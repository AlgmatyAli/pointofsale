<?php

use yii\db\Migration;

/**
 * Class m201107_233451_create_admin_user
 */
class m201222_060104_Drop_forigenKey extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      //  $this->dropIndex('salesdetails_deleted_ibfk_1','salesDetails_deleted');
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
