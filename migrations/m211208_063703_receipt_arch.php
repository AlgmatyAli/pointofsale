<?php

use yii\db\Migration;

/**
 * Class m211208_063703_receipt_arch
 */
class m211208_063703_receipt_arch extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('receipt_arch', [
            'id' =>$this->primaryKey(),
            'rId'=>$this->integer()->notNull(),
            'clinet' =>$this->integer()->notNull(),
            'at' =>$this->date()->notNull(),
            'value' =>$this->decimal(9,3)->notNull(),
            'why' =>$this->string(255)->notNull(),
            'payWay'=>'enum("نقدا","صك","بطاقة") NOT NULL',
            'type' =>$this->integer()->notNull(),
            'delete_by' =>$this->integer(),
            'delete_at'=>$this->integer(),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211208_063703_receipt_arch cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211208_063703_receipt_arch cannot be reverted.\n";

        return false;
    }
    */
}
