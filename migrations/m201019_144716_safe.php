<?php

use yii\db\Migration;

/**
 * Class m201019_144716_safe
 */
class m201019_144716_safe extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('safe', [
            'id' =>$this->primaryKey(),
            'branch' =>$this->integer()->notNull(),
            'safeNo' =>$this->integer()->notNull(),
            'value' =>$this->decimal(9,3)->notNull(),
            'at' =>$this->date()->notNull(),
            'type' =>$this->integer()->notNull(),
            'why' =>$this->string(255)->notNull(),
            'user_insert' =>$this->integer()->notNull(),
            'created_at'=>$this->date()->notNull(),
            'user_update' =>$this->integer(),
            'update_at'=>$this->date(),
            ]);

            $this->createIndex(
                'idx-safe-safe',
                'safe',
                'branch'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-safe-safe',
                'safe',
                'branch',
                'branches',
                'id',
                'CASCADE'
            );

            $this->createIndex(
                'idx-safe-user_insert',
                'safe',
                'user_insert'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-safe-user_insert',
                'safe',
                'user_insert',
                'user',
                'id',
                'CASCADE'
            );
    
            $this->createIndex(
                'idx-safe-user_update',
                'safe',
                'user_update'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-safe-user_update',
                'safe',
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
        $this->dropTable('safe');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_144716_safe cannot be reverted.\n";

        return false;
    }
    */
}
