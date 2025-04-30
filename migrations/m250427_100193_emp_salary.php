<?php

use yii\db\Schema;

class m250427_100193_emp_salary extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('emp_salary', [
            'id' => $this->primaryKey(),
            'employee' => $this->integer(11)->notNull(),
            'at' => $this->date()->notNull(),
            'month' => $this->string(255),
            'year' => $this->string(255),
            'value' => $this->float()->notNull(),
            'why' => $this->string(255)->notNull(),
            'type' => $this->integer(11)->notNull(),
            'currancy' => $this->integer(11)->notNull(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[employee]]) REFERENCES employee ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[currancy]]) REFERENCES currancy ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('emp_salary');
    }
}
