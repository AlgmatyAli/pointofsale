<?php

use yii\db\Migration;

/**
 * Class m201105_200712_trigger_update_prices
 */
class m201106_200712_trigger_update_prices extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    //     $this->execute(" 

    //     CREATE  TRIGGER `updatePrices` AFTER INSERT ON `purchasesdetails`
    //     FOR EACH ROW UPDATE prices SET prices.costPrice = new.costPrice,
    //     prices.minPrice = new.salePrice,
    //     prices.maxPrice = new.salePrice
    //     WHERE prices.category = new.category"
    // );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201105_200712_trigger_update_prices cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201105_200712_trigger_update_prices cannot be reverted.\n";

        return false;
    }
    */
}
