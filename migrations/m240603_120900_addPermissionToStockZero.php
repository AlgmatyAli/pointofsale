<?php

use yii\db\Migration;

/**
 * Class m240603_120900_addPermissionToStockZero
 */
class m240603_120900_addPermissionToStockZero extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // add "" permission 
        $userCanSeeStockLessThanZero = $auth->createPermission('userCanSeeStockLessThanZero');
        $userCanSeeStockLessThanZero->description = 'عرض الكميات الاقل من الصفر';
        $auth->add($userCanSeeStockLessThanZero);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240603_120900_addPermissionToStockZero cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240603_120900_addPermissionToStockZero cannot be reverted.\n";

        return false;
    }
    */
}
