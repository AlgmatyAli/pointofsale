<?php

use yii\db\Migration;

/**
 * Class m220828_102004_customs_declaration
 */
class m220828_102004_customs_declaration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('customsDeclaration', [
            'id' => $this->primaryKey(),
            'name' =>$this->string()->notNull(),
            'phone' =>$this->string(),
            'balance' =>$this->string(),
            'user_insert' =>$this->integer()->notNull(),
            'user_update' =>$this->integer(),
            'created_at'=>$this->date()->notNull(),
            'update_at'=>$this->date(),
        ]);

        $this->createIndex(
            'idx-customsDeclaration-user_insert',
            'customsDeclaration',
            'user_insert'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-customsDeclaration-user_insert',
            'customsDeclaration',
            'user_insert',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-customsDeclaration-user_update',
            'customsDeclaration',
            'user_update'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-customsDeclaration-user_update',
            'customsDeclaration',
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
        echo "m220828_102004_customs_declaration cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220828_102004_customs_declaration cannot be reverted.\n";

        return false;
    }
    */
}
