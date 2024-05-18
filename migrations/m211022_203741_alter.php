<?php

use yii\db\Migration;

/**
 * Class m211022_203741_alter
 */
class m211022_203741_alter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('sales', 'created_at', $this->timestamp()->notNull());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211022_203741_alter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211022_203741_alter cannot be reverted.\n";

        return false;
    }
    */
}
