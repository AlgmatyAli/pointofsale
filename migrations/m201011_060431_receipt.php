<?php

use yii\db\Migration;

/**
 * Class m201011_060431_receipt
 */
class m201011_060431_receipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('receipt', [
            'id' =>$this->primaryKey(),
            'rId'=>$this->integer()->notNull(),
            'clinet' =>$this->integer()->notNull(),
            'at' =>$this->date()->notNull(),
            'value' =>$this->decimal(9,3)->notNull(),
            'why' =>$this->string(255)->notNull(),
            'payWay'=>'enum("نقدا","صك","بطاقة") NOT NULL',
            'type' =>$this->integer()->notNull(),
            'user_insert' =>$this->integer()->notNull(),
            'created_at'=>$this->date()->notNull(),
            'user_update' =>$this->integer(),
            'update_at'=>$this->date(),
            ]);

            $this->createIndex(
                'idx-receipt-user_insert',
                'receipt',
                'user_insert'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-receipt-user_insert',
                'receipt',
                'user_insert',
                'user',
                'id',
                'CASCADE'
            );
    
            $this->createIndex(
                'idx-receipt-user_update',
                'receipt',
                'user_update'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-receipt-user_update',
                'receipt',
                'user_update',
                'user',
                'id',
                'CASCADE'
            );

            $this->createIndex(
                'idx-receipt-client',
                'receipt',
                'clinet'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-receipt-client',
                'receipt',
                'clinet',
                'client',
                'id',
                'CASCADE'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('receipt');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201011_060431_receipt1 cannot be reverted.\n";

        return false;
    }
    */
}
