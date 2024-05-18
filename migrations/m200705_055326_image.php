<?php

use yii\db\Migration;

/**
 * Class m200705_055326_image
 */
class m200705_055326_image extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('image', [ 
            'id' => $this->primaryKey(), 
            'claimId' =>$this->integer()->notNull(),
            'path' => $this->string(1024)->notNull(), 
            'name' => $this->string(255)->notNull(), 
            'user_insert' =>$this->integer()->notNull(),
            'user_update' =>$this->integer()->notNull(),
            'created_at'=>$this->date()->notNull(),
            'update_at'=>$this->date()->notNull(),
            ]); 

            $this->createIndex(
                'idx-mid_items-user_insert',
                'image',
                'user_insert'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-image-user_insert',
                'image',
                'user_insert',
                'user',
                'id',
                'CASCADE'
            );
    
            $this->createIndex(
                'idx-image-user_update',
                'image',
                'user_update'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-image-user_update',
                'image',
                'user_update',
                'user',
                'id',
                'CASCADE'
            );

            $this->createIndex(
                'idx-image-claimId',
                'image',
                'claimId'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-image-claimId',
                'image',
                'claimId',
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
        $this->dropTable('image');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200705_055326_image cannot be reverted.\n";

        return false;
    }
    */
}
