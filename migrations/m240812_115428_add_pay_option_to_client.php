<?php

use yii\db\Migration;

/**
 * Class m240812_115428_add_pay_option_to_client
 */
class m240812_115428_add_pay_option_to_client extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('client', 'post_paid', $this->integer(1)->after('debt')->notnull()->defaultValue(0));  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240812_115428_add_pay_option_to_client cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240812_115428_add_pay_option_to_client cannot be reverted.\n";

        return false;
    }
    */
}
