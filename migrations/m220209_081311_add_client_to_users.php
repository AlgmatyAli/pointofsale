<?php

use yii\db\Migration;

/**
 * Class m220209_081311_add_client_to_users
 */
class m220209_081311_add_client_to_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'client', $this->string(255));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220209_081311_add_client_to_users cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220209_081311_add_client_to_users cannot be reverted.\n";

        return false;
    }
    */
}
