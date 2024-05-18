<?php

use yii\db\Migration;

/**
 * Class m201109_202500_temp_purchases
 */
class m201109_202500_temp_purchases extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('temp_invoice_purchase', [
            'id' =>$this->integer()->notNull(),
            //$this->primaryKey(),
            'category' =>$this->integer()->notNull(),
            'quantity'=>$this->float()->notNull(),
            'costPrice' =>$this->decimal(11,3),
            'totalPrice' =>$this->decimal(11,3),
            'salePrice' =>$this->decimal(11,3)->notNull(),
            'box' =>$this->integer(),
            'expire' =>$this->date(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE'
        
            ]);
            $this->addPrimaryKey('temp_invoice_purchase_PK', 'temp_invoice_purchase', ['id']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('temp_invoice_purchase');
        $this->dropPrimaryKey('temp_invoice_purchase_PK');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201109_202500_temp_purchases cannot be reverted.\n";

        return false;
    }
    */
}
