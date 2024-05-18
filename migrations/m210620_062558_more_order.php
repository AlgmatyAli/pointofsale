<?php

use yii\db\Migration;

/**
 * Class m210620_062558_more_order
 */
class m210620_062558_more_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('category','moreRequest', $this->integer()->after('commCode'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210620_062558_more_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210620_062558_more_order cannot be reverted.\n";

        return false;
    }
    */
}
