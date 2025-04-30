<?php

use yii\db\Schema;

class m250427_090102_customsdeclaration extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('customsdeclaration', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'phone' => $this->string(255),
            'balance' => $this->string(255),
            'user_insert' => $this->integer(11)->notNull(),
            'user_update' => $this->integer(11),
            'created_at' => $this->date()->notNull(),
            'update_at' => $this->date(),
            'FOREIGN KEY ([[user_insert]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_update]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('customsdeclaration');
    }
}
