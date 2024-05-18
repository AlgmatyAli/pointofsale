<?php

use yii\db\Migration;

/**
 * Class m201012_204208_branches
 */
class m201012_204208_branches extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('branches', [
            'id' => $this->primaryKey(),
            'name' =>$this->string()->notNull(),
            'user_insert' =>$this->integer()->notNull(),
            'created_at'=>$this->date()->notNull(),
            'user_update' =>$this->integer(),
            'update_at'=>$this->date(),
        ]);

        $this->createIndex(
            'idx-branches-user_insert',
            'branches',
            'user_insert'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-branches-user_insert',
            'branches',
            'user_insert',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-branches-user_update',
            'branches',
            'user_update'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-branches-user_update',
            'branches',
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
        $this->dropTable('branches');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201012_204208_branches cannot be reverted.\n";

        return false;
    }
    */
}
