<?php

use yii\db\Migration;

/**
 * Class m201011_084529_category
 */
class m201011_084529_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('category', [
            'id' =>$this->primaryKey(),
            'name'=>$this->string(255)->notNull(),
            'class' =>$this->string(255)->notNull(),
            'unit' =>$this->string(100)->notNull(),
            'box' =>$this->integer()->notNull(),
            'cost' =>$this->decimal(9,3)->notNull(),
            'price' =>$this->decimal(9,3)->notNull(),
            'quantity' =>$this->float()->notNull(),
            'minimum' =>$this->integer()->notNull(),
            'ending' =>$this->integer()->notNull(),
            'qShow' =>$this->integer()->notNull(),
            'status' =>$this->integer()->notNull(),
            'user_insert' =>$this->integer()->notNull(),
            'created_at'=>$this->date()->notNull(),
            'user_update' =>$this->integer(),
            'update_at'=>$this->date(),
            ]);

            $this->createIndex(
                'idx-category-user_insert',
                'category',
                'user_insert'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-category-user_insert',
                'category',
                'user_insert',
                'user',
                'id',
                'CASCADE'
            );
    
            $this->createIndex(
                'idx-category-user_update',
                'category',
                'user_update'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-category-user_update',
                'category',
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
        $this->dropTable('category');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201011_084529_category cannot be reverted.\n";

        return false;
    }
    */
}
