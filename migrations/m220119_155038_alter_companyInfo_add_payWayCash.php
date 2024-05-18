<?php

use yii\db\Migration;

/**
 * Class m220119_155038_alter_companyInfo_add_payWayCash
 */
class m220119_155038_alter_companyInfo_add_payWayCash extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {       
         $this->addColumn('company_info', 'payWayCash', $this->integer()->after('repeatCategory')->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220119_155038_alter_companyInfo_add_payWayCash cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220119_155038_alter_companyInfo_add_payWayCash cannot be reverted.\n";

        return false;
    }
    */
}
