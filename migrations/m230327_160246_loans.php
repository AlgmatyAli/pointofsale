<?php

use yii\db\Migration;

/**
 * Class m230327_160246_loans
 */
class m230327_160246_loans extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('loans', [
            'id' => $this->primaryKey(),
            'employee' => $this->integer()->notNull(),
            'loanValue' => $this->decimal(11,3),
            'kestValue' => $this->decimal(11,3),
            'at' => $this->datetime(),
            'parts' => $this->integer()->notNull(),
            'paid' => $this->integer(),
            'notes' => $this->string(),
            'status' => $this->integer()->notNull(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[employee]]) REFERENCES employee ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ]); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230327_160246_loans cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230327_160246_loans cannot be reverted.\n";

        return false;
    }
    */
}
