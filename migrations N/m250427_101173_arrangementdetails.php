<?php

use yii\db\Schema;

class m250427_101173_arrangementdetails extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('arrangementDetails', [
            'id' => $this->primaryKey(),
            'arrangement' => $this->integer(11)->notNull(),
            'category' => $this->integer(11)->notNull(),
            'quantity' => $this->float()->notNull(),
            'box' => $this->integer(11),
            'type' => $this->integer(11)->notNull(),
            'expire' => $this->date(),
            'stockTaking' => $this->integer(11),
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[arrangement]]) REFERENCES arrangement ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('arrangementDetails');
    }
}
