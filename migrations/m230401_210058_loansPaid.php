<?php

use yii\db\Migration;

/**
 * Class m230401_210058_loansPaid
 */
class m230401_210058_loansPaid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('loanPaid', [
            'id' => $this->primaryKey(),
            'employee' => $this->integer()->notNull(),
            'loanId' => $this->integer()->notNull(),
            'kestValue' => $this->decimal(11,3),
            'at' => $this->datetime(),
            'month' => $this->integer(),
            'year' => $this->integer(),
            'notes' => $this->string(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[employee]]) REFERENCES employee ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[loanId]]) REFERENCES loans ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ]); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230401_210058_loansPaid cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230401_210058_loansPaid cannot be reverted.\n";

        return false;
    }
    */
}
