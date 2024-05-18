<?php

use yii\db\Migration;

/**
 * Class m201017_133020_sales
 */
class m201017_133020_sales extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    { 
        $this->createTable('sales', [
        'id' =>$this->primaryKey(),
        'billId'=>$this->integer()->notNull(),
        'at' =>$this->date()->notNull(),
        'clinet' =>$this->integer()->notNull(),
        'payWay'=>$this->integer()->notNull(),
        'branch'=>$this->integer()->notNull(),
        'total' =>$this->decimal(9,3)->notNull(),
        'paid' =>$this->decimal(9,3),
        'notes' =>$this->string(255),
        'path' =>$this->string(255),
        'type' =>$this->integer()->notNull(),
        'deleviryAt' =>$this->date(),
        'carpenter' =>$this->integer(),
        'upholstered' =>$this->integer(),
        'paintId' =>$this->integer(),
        'deleviryId' =>$this->integer(),
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
        $this->dropTable('sales');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201017_133020_sales cannot be reverted.\n";

        return false;
    }
    */
}
