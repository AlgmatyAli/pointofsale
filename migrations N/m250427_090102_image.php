<?php

use yii\db\Schema;

class m250427_090102_image extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('image', [
            'id' => $this->primaryKey(),
            'claimId' => $this->integer(11)->notNull(),
            'path' => $this->string(1024)->notNull(),
            'name' => $this->string(255)->notNull(),
            'user_insert' => $this->integer(11)->notNull(),
            'user_update' => $this->integer(11)->notNull(),
            'created_at' => $this->date()->notNull(),
            'update_at' => $this->date()->notNull(),
            'FOREIGN KEY ([[user_insert]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_update]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('image');
    }
}
