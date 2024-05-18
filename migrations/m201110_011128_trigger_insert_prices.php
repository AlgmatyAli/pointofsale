<?php

use yii\db\Migration;

/**
 * Class m201110_011128_trigger_insert_prices
 */
class m201110_011128_trigger_insert_prices extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(" 

        CREATE TRIGGER `insertPrices` AFTER INSERT ON `category`
        FOR EACH ROW INSERT INTO prices(category, costPrice, minPrice, maxPrice, minPrice2, minPrice3)
        VALUES(new.id, new.cost, new.price, new.price, 0,0)"
    );

    // $this->execute(" 

    // CREATE TRIGGER `updatePrice` AFTER UPDATE ON `category`
    // FOR EACH ROW UPDATE prices SET prices.costPrice = new.cost,
    //        prices.minPrice = new.price,
    //        prices.maxPrice = new.price
    //        WHERE prices.category = new.id"
    // );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201110_011128_trigger_insert_prices cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201110_011128_trigger_insert_prices cannot be reverted.\n";

        return false;
    }
    */
}
