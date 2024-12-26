<?php

use yii\db\Migration;

/**
 * Class m240216_203433_triggersFortransferItems
 */
class m240216_203433_triggersFortransferItems extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(" CREATE TRIGGER `afterUpdatetransferItems_3` AFTER UPDATE ON `transferItems`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type = 3), 0) where stocks.type =3 ");

        $this->execute(" CREATE TRIGGER `afterUpdatetransferItems_` AFTER UPDATE ON `transferItems`
        FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        $this->execute(" CREATE TRIGGER `afterUpdatetransferItemsDetails` AFTER UPDATE ON `transferItemsDetails`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3), 0)");

        $this->execute(" CREATE TRIGGER `afterUpdatetransferItemsDetails_3` AFTER UPDATE ON `transferItemsDetails`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type = 3), 0) where stocks.type = 3");

        $this->execute(" CREATE TRIGGER `afterUpdatetransferItemsDetails_` AFTER UPDATE ON `transferItemsDetails`
        FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        // ================

        $this->execute("CREATE TRIGGER `beforeDeletetransferItems` AFTER DELETE ON `transferItems`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3), 0)");

        $this->execute(" CREATE TRIGGER `beforeDeletetransferItems_` AFTER DELETE ON `transferItems`
        FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        $this->execute("CREATE TRIGGER `beforeDeletetransferItemsDetails` AFTER DELETE ON `transferItemsDetails`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3), 0)");

        $this->execute(" CREATE TRIGGER `beforeDeletetransferItemsDetails_` AFTER DELETE ON `transferItemsDetails`
        FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240216_203433_triggersFortransferItems cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240216_203433_triggersFortransferItems cannot be reverted.\n";

        return false;
    }
    */
}
