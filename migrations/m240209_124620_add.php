<?php

use yii\db\Migration;

/**
 * Class m240209_124620_add
 */
class m240209_124620_add extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'seeOtherBranchQ', $this->integer(1)->after('client')->notnull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240209_124620_add cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240209_124620_add cannot be reverted.\n";

        return false;
    }
    */
}
