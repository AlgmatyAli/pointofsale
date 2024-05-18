<?php

use yii\db\Migration;

/**
 * Class m210126_040738_transfer_items
 */
class m210126_040738_temp_transfer_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tempTransferItems', [
            'id' =>$this->integer()->notNull(),
            'category' =>$this->integer()->notNull(),
            'quantity' =>$this->decimal(9,3)->notNull(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),

            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE'
            ]);

            $this->addPrimaryKey('tempTransferItems_PK', 'tempTransferItems', ['id']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tempTransferItems');
        $this->dropPrimaryKey('tempTransferItems_PK');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210126_040738_transfer_items cannot be reverted.\n";

        return false;
    }
    */
}
