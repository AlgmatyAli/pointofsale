<?php

use yii\db\Migration;

class m260908_075351_new_auth extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Permissions
        $seeVendorCredit = $auth->createPermission('seeVendorCredit');
        $seeVendorCredit->description = 'عرض ديون الموردين';
        $auth->add($seeVendorCredit);

        $canCreateVendorReceipt = $auth->createPermission('canCreateVendorReceipt');
        $canCreateVendorReceipt->description = 'إنشاء ايصال صرف';
        $auth->add($canCreateVendorReceipt);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260908_075351_new_auth cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260908_075351_new_auth cannot be reverted.\n";

        return false;
    }
    */
}
