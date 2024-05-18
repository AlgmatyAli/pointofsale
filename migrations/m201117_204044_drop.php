<?php

use yii\db\Migration;

/**
 * Class m201117_204044_drop
 */
class m201117_204044_drop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(" DROP TRIGGER IF EXISTS updatePrices");        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201117_204044_drop cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201117_204044_drop cannot be reverted.\n";

        return false;
    }
    */
}
