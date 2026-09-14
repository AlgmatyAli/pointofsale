<?php

use yii\db\Migration;

class m260914_094817_add_disscount_for_clients extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%disscount_clients}}', [
            'id' => $this->primaryKey(),
            'client' => $this->integer(11)->notNull(),
            'at' => $this->date(),
            'value' => $this->decimal(13, 3)->notNull()->defaultValue(0),
            'type' => $this->integer(1)->notNull(),
            'currancy' => $this->integer(2)->notNull(),
            'branch' => $this->integer(2)->notNull(),
            'notes' => $this->text(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[client]]) REFERENCES client ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[currancy]]) REFERENCES currancy ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[branch]]) REFERENCES branches ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%disscount_clients}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260914_094817_add_disscount_for_clients cannot be reverted.\n";

        return false;
    }
    */
}
