<?php

use yii\db\Migration;

/**
 * Class m220828_102005_customs_office
 */
class m220828_102005_customs_office extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('customsOffice', [
            'id' =>$this->primaryKey(),
            'customId' =>$this->integer()->notNull(),
            'at' =>$this->date()->notNull(),
            'value' =>$this->float()->notNull(),
            'why' =>$this->string(255)->notNull(),
            'user_insert' =>$this->integer()->notNull(),
            'created_at'=>$this->date()->notNull(),
            'user_update' =>$this->integer(),
            'update_at'=>$this->date(),
            ]);

            $this->createIndex(
                'idx-customsOffice-user_insert',
                'customsOffice',
                'user_insert'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-customsOffice-user_insert',
                'customsOffice',
                'user_insert',
                'user',
                'id',
                'CASCADE'
            );
    
            $this->createIndex(
                'idx-customsOffice-user_update',
                'customsOffice',
                'user_update'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-customsOffice-user_update',
                'customsOffice',
                'user_update',
                'user',
                'id',
                'CASCADE'
            );

            $this->createIndex(
                'idx-customsOffice-customId',
                'customsOffice',
                'customId'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-customsOffice-customId',
                'customsOffice',
                'customId',
                'customsDeclaration',
                'id',
                'CASCADE'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220828_102005_customs_office cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220828_102005_customs_office cannot be reverted.\n";

        return false;
    }
    */
}
