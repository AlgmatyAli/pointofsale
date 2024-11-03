<?php

use yii\db\Migration;

/**
 * Class m241028_064231_add_ShowZeroQnty_to_CompanyInfo
 */
class m241028_064231_add_ShowZeroQnty_to_CompanyInfo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company_info', 'zeroQnty', $this->integer(1)->after('criteriaـvalue')->notnull()->defaultValue(0));  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241028_064231_add_ShowZeroQnty_to_CompanyInfo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241028_064231_add_ShowZeroQnty_to_CompanyInfo cannot be reverted.\n";

        return false;
    }
    */
}
