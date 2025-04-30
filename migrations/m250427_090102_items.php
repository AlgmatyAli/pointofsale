<?php

use yii\db\Schema;

class m250427_090102_items extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('items', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'user_insert' => $this->integer(11)->notNull(),
            'created_at' => $this->date()->notNull(),
            'user_update' => $this->integer(11),
            'update_at' => $this->date(),
            'FOREIGN KEY ([[user_insert]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_update]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('items');
    }
}
