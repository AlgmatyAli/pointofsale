<?php

use yii\db\Migration;

/**
 * Class m250427_111133_transfer
 */
class m250427_111133_transfer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('transfer', [
            'id' =>$this->primaryKey(),
            'fromBr' =>$this->integer()->notNull(),
            'toBr' =>$this->integer()->notNull(),
            'value' =>$this->decimal(9,3)->notNull(),
            'at' =>$this->date()->notNull(),
            'type' =>$this->integer()->notNull(),
            'currancy' =>$this->integer()->notNull(),
            'why' =>$this->string(255)->notNull(),
            'user_insert' =>$this->integer()->notNull(),
            'created_at'=>$this->date()->notNull(),
            'user_update' =>$this->integer(),
            'update_at'=>$this->date(),
            ]);

            $this->createIndex(
                'idx-transfer-fromBr',
                'transfer',
                'fromBr'
            );
    
            // add foreign key for table `transfer ` 
            $this->addForeignKey(
                'fk-transfer-fromBr',
                'transfer',
                'fromBr',
                'branches',
                'id',
                'CASCADE'
            );

            $this->createIndex(
                'idx-transfer-toBr',
                'transfer',
                'toBr'
            );
    
            // add foreign key for table `transfer ` 
            $this->addForeignKey(
                'fk-transfer-toBr',
                'transfer',
                'toBr',
                'branches',
                'id',
                'CASCADE'
            );

            $this->createIndex(
                'idx-transfer-user_insert',
                'transfer',
                'user_insert'
            );
    
            // add foreign key for table `transfer ` 
            $this->addForeignKey(
                'fk-transfer-user_insert',
                'transfer',
                'user_insert',
                'user',
                'id',
                'CASCADE'
            );
    
            $this->createIndex(
                'idx-transfer-user_update',
                'transfer',
                'user_update'
            );
    
            // add foreign key for table `transfer ` 
            $this->addForeignKey(
                'fk-transfer-user_update',
                'transfer',
                'user_update',
                'user',
                'id',
                'CASCADE'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('transfer');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250427_111133_transfer cannot be reverted.\n";

        return false;
    }
    */
}
