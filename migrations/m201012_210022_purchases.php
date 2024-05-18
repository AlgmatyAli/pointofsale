<?php

use yii\db\Migration;

/**
 * Class m201012_210022_purchases
 */
class m201012_210022_purchases extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('purchases', [
            'id' =>$this->primaryKey(),
            'billId'=>$this->integer()->notNull(),
            'clinet' =>$this->integer()->notNull(),
            'at' =>$this->date()->notNull(),
            'payWay'=>$this->integer()->notNull(),
            'clientBill' =>$this->string(255)->notNull(),
            'BuyFor'=>$this->integer()->notNull(),
            'branch'=>$this->integer()->notNull(),
            'total' =>$this->decimal(9,3)->notNull(),
            'paid' =>$this->decimal(9,3),
            'notes' =>$this->string(255),
            'path' =>$this->string(255),
            'type' =>$this->integer()->notNull(),
            'user_insert' =>$this->integer()->notNull(),
            'created_at'=>$this->date()->notNull(),
            'user_update' =>$this->integer(),
            'update_at'=>$this->date(),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('purchases');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201012_210022_purchases cannot be reverted.\n";

        return false;
    }
    */
}
