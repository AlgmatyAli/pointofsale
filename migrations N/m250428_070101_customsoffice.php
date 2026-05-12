<?php

use yii\db\Schema;

class m250428_070101_customsoffice extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('customsOffice', [
            'id' => $this->primaryKey(),
            'customId' => $this->integer(11)->notNull(),
            'at' => $this->date()->notNull(),
            'value' => $this->float()->notNull(),
            'why' => $this->string(255)->notNull(),
            'currancy' => $this->integer(11),
            'user_insert' => $this->integer(11)->notNull(),
            'created_at' => $this->date()->notNull(),
            'user_update' => $this->integer(11),
            'update_at' => $this->date(),
            'FOREIGN KEY ([[customId]]) REFERENCES customsdeclaration ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_update]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('customsOffice');
    }
}
