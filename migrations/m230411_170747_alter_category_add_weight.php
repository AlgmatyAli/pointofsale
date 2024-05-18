<?php

use yii\db\Migration;

/**
 * Class m230411_170747_alter_category_add_weight
 */
class m230411_170747_alter_category_add_weight extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('category', 'weight', $this->decimal(10,3));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230411_170747_alter_category_add_weight cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230411_170747_alter_category_add_weight cannot be reverted.\n";

        return false;
    }
    */
}
