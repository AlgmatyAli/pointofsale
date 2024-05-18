<?php

use yii\db\Migration;

/**
 * Class m220820_130236_outBox_expenses
 */
class m220820_130236_outBox_expenses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('expenses', 'outBox', $this->integer()->after('value')->notnull());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220820_130236_outBox_expenses cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220820_130236_outBox_expenses cannot be reverted.\n";

        return false;
    }
    */
}
