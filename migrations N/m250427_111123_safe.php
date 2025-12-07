<?php

use yii\db\Schema;

class m250427_111123_safe extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('safe', [
            'id' => $this->primaryKey(),
            'branch' => $this->integer(11)->notNull(),
            'safeNo' => $this->integer(11)->notNull(),
            'value' => $this->decimal(9,3)->notNull(),
            'at' => $this->date()->notNull(),
            'type' => $this->integer(11)->notNull(),
            'why' => $this->string(255)->notNull(),
            'user_insert' => $this->integer(11)->notNull(),
            'created_at' => $this->date()->notNull(),
            'user_update' => $this->integer(11),
            'update_at' => $this->date(),
            'FOREIGN KEY ([[branch]]) REFERENCES branches ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_insert]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_update]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[branch]]) REFERENCES branches ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('safe');
    }
}
