<?php

use yii\db\Schema;

class m250427_090102_stocks extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('stocks', [
            'id' => $this->primaryKey(),
            'category' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11),
            'branch' => $this->integer(11)->notNull(),
            'type' => $this->integer(11)->notNull(),
            'FOREIGN KEY ([[branch]]) REFERENCES branches ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('stocks');
    }
}
