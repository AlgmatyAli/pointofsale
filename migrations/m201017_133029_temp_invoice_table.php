<?php

use yii\db\Migration;

/**
 * Class m201017_133029_salesDetails
 */
class m201017_133029_temp_invoice_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('temp_invoice', [
            'id' =>$this->primaryKey(),
            'invoice_number' =>$this->integer(),
            'category' =>$this->integer()->notNull(),
            'serial_number' =>$this->string(255),
            'quantity'=>$this->float()->notNull(),
            'costPrice' =>$this->decimal(11,3),
            'salePrice' =>$this->decimal(11,3)->notNull(),
            'box' =>$this->integer(),
            'state' =>$this->integer(),
            'expire' =>$this->date(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE'
        
            ]);

            
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201017_133029_salesDetails cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201017_133029_salesDetails cannot be reverted.\n";

        return false;
    }
    */
}
