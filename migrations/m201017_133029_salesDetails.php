<?php

use yii\db\Migration;

/**
 * Class m201017_133029_salesDetails
 */
class m201017_133029_salesDetails extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('salesDetails', [
            'id' =>$this->primaryKey(),
            'salesId'=>$this->integer()->notNull(),
            'category' =>$this->integer()->notNull(),
            'quantity'=>$this->float()->notNull(),
            'costPrice' =>$this->decimal(9,3)->notNull(),
            'salePrice' =>$this->decimal(9,3),
            'box' =>$this->integer()->notNull(),
            'expire' =>$this->date(),
            ]);

            $this->createIndex(
                'idx-sales-sales',
                'salesDetails',
                'salesId'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-sales-sales',
                'salesDetails',
                'salesId',
                'sales',
                'id',
                'CASCADE'
            );
    
            $this->createIndex(
                'idx-sales-category',
                'salesDetails',
                'category'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-sales-category',
                'salesDetails',
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
        echo "m201017_133029_salesDetails cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201017_133029_salesDetails cannot be reverted.\n";

        return false;
    }
    */
}
