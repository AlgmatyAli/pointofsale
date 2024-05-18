<?php

use yii\db\Migration;

/**
 * Class m210126_064622_transfer_items_details
 */
class m210126_064622_transfer_items_details extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('transferItemsDetails', [
            'id' =>$this->primaryKey(),
            'transfer'=>$this->integer()->notNull(),
            'category' =>$this->integer()->notNull(),
            'quantity'=>$this->integer()->notNull(),
            ]);

            $this->createIndex(
                'idx-transferItemsDetails',
                'transferItemsDetails',
                'transfer'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-transferItemsDetails',
                'transferItemsDetails',
                'transfer',
                'transferItems',
                'id',
                'CASCADE'
            );
    
            $this->createIndex(
                'idx-transferItemsDetails-category',
                'transferItemsDetails',
                'category'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-transferItemsDetails-category',
                'transferItemsDetails',
                'category',
                'category',
                'id',
                'CASCADE'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210126_064622_transfer_items_details cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210126_064622_transfer_items_details cannot be reverted.\n";

        return false;
    }
    */
}
