<?php

use yii\db\Migration;

/**
 * Class m211011_063037_add_emp_salary
 */
class m211011_063037_add_emp_salary extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('emp_salary', [
            'id' =>$this->primaryKey(),
            'employee' =>$this->integer(),
            'at' =>$this->date()->notNull(),
            'month' => $this->string(),
            'year' => $this->string(),
            'value' =>$this->float()->notNull(),
            'why' =>$this->string(255)->notNull(),
            'type' =>$this->integer(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ]);

            $this->createIndex(
                'idx-emp_salary-employee',
                'emp_salary',
                'employee'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-emp_salary-employee',
                'emp_salary',
                'employee',
                'employee',
                'id',
                'CASCADE'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211011_063037_add_payment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211011_063037_add_payment cannot be reverted.\n";

        return false;
    }
    */
}
