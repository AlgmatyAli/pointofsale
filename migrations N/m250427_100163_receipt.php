<?php

use yii\db\Schema;

class m250427_100163_receipt extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('receipt', [
            'id' => $this->primaryKey(),
            'rId' => $this->integer(11)->notNull(),
            'clinet' => $this->integer(11)->notNull(),
            'branch' => $this->integer(11)->notNull(),
            'currancy' => $this->integer(11),
            'at' => $this->date()->notNull(),
            'value' => $this->decimal(9, 3)->notNull(),
            'tafqet' => $this->string(255),
            'why' => $this->string(255)->notNull(),
            'payWay' => $this->string()->notNull(),
            'type' => $this->integer(11)->notNull(),
            'user_insert' => $this->integer(11)->notNull(),
            'created_at' => $this->date()->notNull(),
            'user_update' => $this->integer(11),
            'update_at' => $this->date(),
            'FOREIGN KEY ([[currancy]]) REFERENCES currancy ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[clinet]]) REFERENCES client ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_insert]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_update]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->execute(" 
                CREATE TRIGGER `before_delete` BEFORE DELETE ON `receipt`
                FOR EACH ROW INSERT INTO receipt_arch(rId, clinet, at, value, why, payWay, type)
                VALUES(old.rId, old.clinet, old.at, old.value, old.why, old.payWay, old.type)
       ");
    }

    public function down()
    {
        $this->dropTable('receipt');
    }
}
