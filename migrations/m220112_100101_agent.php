<?php

use yii\db\Schema;

class m220112_100101_agent extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('agent', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'phone' => $this->string(10)->notNull(),
            'email' => $this->string(20)->notNull(),
            'branch' => $this->integer(11)->notNull(),
            'branch' =>$this->integer()->notNull(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            // 'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            // 'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            // 'FOREIGN KEY ([[branch]]) REFERENCES branches ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
      
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('agent');
    }
}
