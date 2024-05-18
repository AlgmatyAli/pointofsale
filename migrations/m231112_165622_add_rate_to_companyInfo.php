<?php

use yii\db\Migration;

/**
 * Class m231112_165622_add_rate_to_companyInfo
 */
class m231112_165622_add_rate_to_companyInfo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company_info', 'rate', $this->decimal(10,3)->after('waitQnty')->notnull()->defaultValue(0));
        $this->addColumn('company_info', 'criteriaـvalue', $this->integer()->after('rate')->notnull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231112_165622_add_rate_to_companyInfo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231112_165622_add_rate_to_companyInfo cannot be reverted.\n";

        return false;
    }
    */
}
