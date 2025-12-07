<?php

use yii\db\Schema;

class m250427_090102_client extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('client', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'phone' => $this->string(255),
            'mobile' => $this->string(255),
            'address' => $this->string(255),
            'email' => $this->string(255),
            'balance' => $this->string(255),
            'type' => $this->integer(11)->notNull(),
            'user_insert' => $this->integer(11)->notNull(),
            'user_update' => $this->integer(11),
            'created_at' => $this->date()->notNull(),
            'update_at' => $this->date(),
            'branch' => $this->integer(11)->notNull(),
            'price_group' => $this->string(),
            'debt' => $this->decimal(10,3)->notNull(),
            'post_paid' => $this->integer(11)->notNull()->defaultValue(0),
            'FOREIGN KEY ([[user_insert]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_update]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('client');
    }
}
