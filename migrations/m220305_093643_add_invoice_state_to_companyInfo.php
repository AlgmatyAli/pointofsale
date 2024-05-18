<?php

use yii\db\Migration;

/**
 * Class m220305_093643_add_invoice_state_to_companyInfo
 */
class m220305_093643_add_invoice_state_to_companyInfo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company_info', 'invoiceState', $this->integer()->after('payWayCash'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220305_093643_add_invoice_state_to_companyInfo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220305_093643_add_invoice_state_to_companyInfo cannot be reverted.\n";

        return false;
    }
    */
}
