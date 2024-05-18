<?php

use yii\db\Migration;

/**
 * Class m210411_175939_reorder_details
 */
class m210411_175939_reorder_details extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('reorderDetails', [
            'id' =>$this->primaryKey(),
            'reorder' =>$this->integer()->notNull(),
            'category' =>$this->integer()->notNull(),
            'quantity'=>$this->float()->notNull(),
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[reorder]]) REFERENCES reorder ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210411_175939_reorder_details cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210411_175939_reorder_details cannot be reverted.\n";

        return false;
    }
    */
}
