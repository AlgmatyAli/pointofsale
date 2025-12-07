<?php

use yii\db\Schema;

class m250427_090113_sales extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('sales', [
            'id' => $this->primaryKey(),
            'billId' => $this->integer(11)->notNull(),
            'at' => $this->date()->notNull(),
            'clinet' => $this->integer(11)->notNull(),
            'payWay' => $this->integer(11)->notNull(),
            'branch' => $this->integer(11)->notNull(),
            'total' => $this->decimal(9, 3)->notNull(),
            'disscount' => $this->decimal(9, 3),
            'paid' => $this->decimal(9, 3),
            'notes' => $this->string(255),
            'path' => $this->string(255),
            'type' => $this->integer(11)->notNull(),
            'deleviryAt' => $this->date(),
            'deserving' => $this->date(),
            'deleviried' => $this->integer(11),
            'currancy' => $this->integer(11)->notNull()->defaultValue(1),
            'user_insert' => $this->integer(11)->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'user_update' => $this->integer(11),
            'update_at' => $this->date(),
            'wholesale' => $this->integer(11)->defaultValue(0),
            'FOREIGN KEY ([[branch]]) REFERENCES branches ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_insert]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_update]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[clinet]]) REFERENCES client ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[currancy]]) REFERENCES currancy ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('sales');
    }
}
