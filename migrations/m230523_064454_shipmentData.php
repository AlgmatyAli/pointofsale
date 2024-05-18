<?php

use yii\db\Migration;

/**
 * Class m230523_064454_shipmentData
 */
class m230523_064454_shipmentData extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('shipmentData',[
            'id' => $this->primaryKey(),
            'shipmentId' => $this->integer(11)->notNull(),
            'at' => $this->date(),
            'value' => $this->decimal(10,3),
            'size' => $this->integer(11)->notNull(),
            'country' => $this->string(255)->notNull(),
            'type' => $this->string(255)->notNull(),
            'notes' => $this->string(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('shipmentData');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230523_064454_shipmentData cannot be reverted.\n";

        return false;
    }
    */
}
