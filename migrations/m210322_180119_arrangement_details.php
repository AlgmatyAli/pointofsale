<?php

use yii\db\Migration;

/**
 * Class m210322_180119_arrangement_details
 */
class m210322_180119_arrangement_details extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('arrangementDetails', [
            'id' =>$this->primaryKey(),
            'arrangement' =>$this->integer()->notNull(),
            'category' =>$this->integer()->notNull(),
            'quantity'=>$this->float()->notNull(),
            'box' =>$this->integer(),
            'type' =>$this->integer()->notNull(),
            'expire' =>$this->date(),
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[arrangement]]) REFERENCES arrangement ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210322_180119_arrangement_details cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210322_180119_arrangement_details cannot be reverted.\n";

        return false;
    }
    */
}
