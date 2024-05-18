<?php

use yii\db\Schema;

class m201226_182149_create_currancy_table  extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('currancy', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'code' => $this->string(3)->notNull(),
            'user_insert' => $this->integer(11),
            'created_at' => $this->date()->notNull(),
            'user_update' => $this->integer(11),
            'update_at' => $this->date(),
            'FOREIGN KEY ([[user_insert]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_update]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
          
        ]
        // $tableOptions
    );
                
    }

    public function down()
    {
        $this->dropTable('currancy');
    }
}
