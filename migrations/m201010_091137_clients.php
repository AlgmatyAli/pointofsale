<?php

use yii\db\Migration;

/**
 * Class m201010_091137_clients
 */
class m201010_091137_clients extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('client', [
            'id' => $this->primaryKey(),
            'name' =>$this->string()->notNull(),
            'phone' =>$this->string(),
            'email' =>$this->string(),
            'balance' =>$this->string(),
            'type'=>$this->integer()->notNull(),
            'user_insert' =>$this->integer()->notNull(),
            'user_update' =>$this->integer(),
            'created_at'=>$this->date()->notNull(),
            'update_at'=>$this->date(),
        ]);

        $this->createIndex(
            'idx-client-user_insert',
            'client',
            'user_insert'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-client-user_insert',
            'client',
            'user_insert',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-client-user_update',
            'client',
            'user_update'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-client-user_update',
            'client',
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
        $this->dropTable('client');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201010_091137_clients cannot be reverted.\n";

        return false;
    }
    */
}
