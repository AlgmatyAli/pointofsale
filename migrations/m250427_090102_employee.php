<?php

use yii\db\Schema;

class m250427_090102_employee extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('employee', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'salary' => $this->decimal(15,3),
            'dayOfWork' => $this->integer(11),
            'salaryByDay' => $this->decimal(15,3),
            'startWork' => $this->date(),
            'notes' => $this->string(255),
            'state' => $this->integer(11),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('employee');
    }
}
