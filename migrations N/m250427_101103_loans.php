<?php

use yii\db\Schema;

class m250427_101103_loans extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('loans', [
            'id' => $this->primaryKey(),
            'employee' => $this->integer(11)->notNull(),
            'loanValue' => $this->decimal(11,3),
            'kestValue' => $this->decimal(11,3),
            'at' => $this->datetime(),
            'parts' => $this->integer(11)->notNull(),
            'paid' => $this->integer(11),
            'notes' => $this->string(255),
            'status' => $this->integer(11)->notNull(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[employee]]) REFERENCES employee ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('loans');
    }
}
