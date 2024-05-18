<?php

use yii\db\Migration;

/**
 * Class m201012_211505_Purchases_Details
 */
class m201012_211505_Purchases_Details extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('purchasesDetails', [
            'id' =>$this->primaryKey(),
            'PurchasesId'=>$this->integer()->notNull(),
            'category' =>$this->integer()->notNull(),
            'quantity'=>$this->integer()->notNull(),
            'costPrice' =>$this->decimal(9,3)->notNull(),
            'salePrice' =>$this->decimal(9,3),
            'box' =>$this->integer()->notNull(),
            'expire' =>$this->date(),
            ]);

            $this->createIndex(
                'idx-purchases-purchases',
                'purchasesDetails',
                'purchasesId'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-purchases-purchases',
                'purchasesDetails',
                'purchasesId',
                'purchases',
                'id',
                'CASCADE'
            );
    
            $this->createIndex(
                'idx-purchases-category',
                'purchasesDetails',
                'category'
            );
    
            // add foreign key for table `basicInfo ` 
            $this->addForeignKey(
                'fk-purchases-category',
                'purchasesDetails',
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
        $this->dropTable('purchasesDetails');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201012_211505_Purchases_Details cannot be reverted.\n";

        return false;
    }
    */
}
