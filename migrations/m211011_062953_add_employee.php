<?php

use yii\db\Migration;

/**
 * Class m211011_062953_add_employee
 */
class m211011_062953_add_employee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('employee', [
            'id' =>$this->primaryKey(),
            'name' =>$this->string(),
            'salary'=>$this->decimal(15,3),
            'dayOfWork' =>$this->integer(),
            'salaryByDay'=>$this->decimal(15,3),
            'startWork' =>$this->date(),
            'notes' =>$this->string(),
            'state' =>$this->integer(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211011_062953_add_employee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211011_062953_add_employee cannot be reverted.\n";

        return false;
    }
    */
}
