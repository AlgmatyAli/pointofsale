<?php

use yii\db\Migration;

/**
 * Class m201010_123705_items
 */
class m201010_123705_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('items', [
            'id' => $this->primaryKey(),
            'name' =>$this->string()->notNull(),
            'user_insert' =>$this->integer()->notNull(),
            'created_at'=>$this->date()->notNull(),
            'user_update' =>$this->integer(),
            'update_at'=>$this->date(),
        ]);

        $this->createIndex(
            'idx-items-user_insert',
            'items',
            'user_insert'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-items-user_insert',
            'items',
            'user_insert',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-items-user_update',
            'items',
            'user_update'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-items-user_update',
            'items',
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
        $this->dropTable('items');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201010_123705_items cannot be reverted.\n";

        return false;
    }
    */
}
