<?php

use yii\db\Migration;

/**
 * Class m210306_220138_temp_back_sales
 */
class m210306_220138_temp_back_sales extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('temp_back_sales', [
            'id' =>$this->primaryKey(),
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
        echo "m210306_220138_temp_back_sales cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210306_220138_temp_back_sales cannot be reverted.\n";

        return false;
    }
    */
}
