<?php

use yii\db\Migration;

/**
 * Class m201012_210708_alter_purchases
 */
class m201012_210708_alter_purchases extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-purchases-client',
            'purchases',
            'clinet'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-purchases-client',
            'purchases',
            'clinet',
            'client',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-purchases-branches',
            'purchases',
            'branch'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-purchases-branches',
            'purchases',
            'branch',
            'branches',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-purchases-user_insert',
            'purchases',
            'user_insert'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-purchases-user_insert',
            'purchases',
            'user_insert',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-purchases-user_update',
            'purchases',
            'user_update'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-purchases-user_update',
            'purchases',
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
        echo "m201012_210708_alter_purchases cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201012_210708_alter_purchases cannot be reverted.\n";

        return false;
    }
    */
}
