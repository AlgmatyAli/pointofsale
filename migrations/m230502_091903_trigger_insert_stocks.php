<?php

use yii\db\Migration;

/**
 * Class m230502_091903_trigger_insert_stocks
 */
class m230502_091903_trigger_insert_stocks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(" 
        CREATE TRIGGER `insertStocks` AFTER INSERT ON `category`
        FOR EACH ROW INSERT INTO stocks(category, quantity, branch, type)
        VALUES(new.id, new.quantity, 1, 1)"
    );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230502_091903_trigger_insert_stocks cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230502_091903_trigger_insert_stocks cannot be reverted.\n";

        return false;
    }
    */
}
