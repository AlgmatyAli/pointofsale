<?php

use yii\db\Schema;

class m250427_090102_currancy extends \yii\db\Migration
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
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
            'FOREIGN KEY ([[user_insert]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
            
            $this->insert('currancy', [
                'name' => 'دينار ليبي',
                'code' => 'LYD',
                'user_insert' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $this->insert('currancy', [
                'name' => 'دولار امريكي',
                'code' => 'USD',
                'user_insert' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $this->insert('currancy', [
                'name' => 'درهم اماراتي',
                'code' => 'AED',
                'user_insert' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

                
    }

    public function down()
    {
        $this->dropTable('currancy');
    }
}
