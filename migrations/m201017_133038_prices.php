<?php

use yii\db\Migration;

/**
 * Class m201017_133038_prices
 */
class m201017_133038_prices extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('prices', [
            'id' =>$this->primaryKey(),
            'category' =>$this->integer()->notNull(),
            'costPrice' =>$this->decimal(9,3)->notNull(),
            'minPrice' =>$this->decimal(9,3),
            'maxPrice' =>$this->decimal(9,3),
            ]);

            $this->createIndex(
                'idx-prices-category',
                'prices',
                'category'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-prices-category',
                'prices',
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
        $this->dropTable('prices');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201017_133038_prices cannot be reverted.\n";

        return false;
    }
    */
}
