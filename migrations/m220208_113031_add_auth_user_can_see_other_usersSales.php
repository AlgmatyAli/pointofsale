<?php

use yii\db\Migration;

/**
 * Class m220208_113031_add_auth_user_can_see_other_usersSales
 */
class m220208_113031_add_auth_user_can_see_other_usersSales extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // add "" permission 
        $userCanSeeOtherUsersSales = $auth->createPermission('userCanSeeOtherUsersSales');
        $userCanSeeOtherUsersSales->description = 'امكانية مشاهدة مبيعات المستخدمين';
        $auth->add($userCanSeeOtherUsersSales);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220208_113031_add_auth_user_can_see_other_usersSales cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220208_113031_add_auth_user_can_see_other_usersSales cannot be reverted.\n";

        return false;
    }
    */
}
