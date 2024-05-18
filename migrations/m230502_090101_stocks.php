<?php

use yii\db\Migration;

class m230502_090101_stocks extends Migration
{
    public function safeUp()
    {
        $this->createTable('stocks', [
            'id' => $this->primaryKey(),
            'category' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11)->notNull(),
            'branch' => $this->integer(11)->notNull(),
            'type' => $this->integer(11)->notNull(),
            'FOREIGN KEY ([[branch]]) REFERENCES branches ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('stocks');
    }
}
