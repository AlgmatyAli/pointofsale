<?php

use yii\db\Migration;

/**
 * Class m201017_140138_alter_sales
 */
class m201017_140138_alter_sales extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-sales-client',
            'sales',
            'clinet'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-sales-client',
            'sales',
            'clinet',
            'client',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-sales-branches',
            'sales',
            'branch'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-sales-branches',
            'sales',
            'branch',
            'branches',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-sales-user_insert',
            'sales',
            'user_insert'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-sales-user_insert',
            'sales',
            'user_insert',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-sales-user_update',
            'sales',
            'user_update'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-sales-user_update',
            'sales',
            'user_update',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-sales-user_paint',
            'sales',
            'paintId'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-sales-paint',
            'sales',
            'paintId',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-sales-deleviryId',
            'sales',
            'deleviryId'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-sales-deleviryId',
            'sales',
            'deleviryId',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-sales-upholstered',
            'sales',
            'upholstered'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-sales-upholstered',
            'sales',
            'upholstered',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-sales-carpenter',
            'sales',
            'carpenter'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-sales-carpenter',
            'sales',
            'carpenter',
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
        echo "m201017_140138_alter_sales cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201017_140138_alter_sales cannot be reverted.\n";

        return false;
    }
    */
}
