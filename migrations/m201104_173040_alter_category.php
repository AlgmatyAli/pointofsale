<?php

use yii\db\Migration;

/**
 * Class m201104_173040_alter_category
 */
class m201104_173040_alter_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('category', 'path', $this->string()->after('status'));
        $this->addColumn('client', 'mobile', $this->string()->after('phone'));
        $this->addColumn('client', 'address', $this->string()->after('mobile'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('category', 'path');
        $this->dropColumn('client', 'mobile');
        $this->dropColumn('client', 'address');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201104_173040_alter_category cannot be reverted.\n";

        return false;
    }
    */
}
