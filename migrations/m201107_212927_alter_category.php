<?php

use yii\db\Migration;

/**
 * Class m201107_212927_alter_category
 */
class m201107_212927_alter_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('category', 'company', $this->string()->after('status'));
        $this->addColumn('category', 'country', $this->string()->after('status'));
        $this->addColumn('category', 'serialNo', $this->string()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('category', 'company');
        $this->dropColumn('category', 'country');
        $this->dropColumn('category', 'serialNo');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201107_212927_alter_category cannot be reverted.\n";

        return false;
    }
    */
}
