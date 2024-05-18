<?php

use yii\db\Migration;

/**
 * Class m201010_130709_expenses
 */
class m201010_130709_expenses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('expenses', [
            'id' =>$this->primaryKey(),
            'expenseTo' =>$this->string(255)->notNull(),
            'at' =>$this->date()->notNull(),
            'itemId' =>$this->integer()->notNull(),
            'value' =>$this->decimal(9,3)->notNull(),
            'why' =>$this->string(255)->notNull(),
            'user_insert' =>$this->integer()->notNull(),
            'created_at'=>$this->date()->notNull(),
            'user_update' =>$this->integer(),
            'update_at'=>$this->date(),
            ]);

            $this->createIndex(
                'idx-expenses-user_insert',
                'expenses',
                'user_insert'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-expenses-user_insert',
                'expenses',
                'user_insert',
                'user',
                'id',
                'CASCADE'
            );
    
            $this->createIndex(
                'idx-expenses-user_update',
                'expenses',
                'user_update'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-expenses-user_update',
                'expenses',
                'user_update',
                'user',
                'id',
                'CASCADE'
            );

            $this->createIndex(
                'idx-expenses-items',
                'expenses',
                'itemId'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-expenses-items',
                'expenses',
                'itemId',
                'items',
                'id',
                'CASCADE'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('expenses');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201010_130709_expenses cannot be reverted.\n";

        return false;
    }
    */
}
