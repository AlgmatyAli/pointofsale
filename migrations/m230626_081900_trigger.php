<?php

use yii\db\Migration;

/**
 * Class m230626_081900_trigger
 */
class m230626_081900_trigger extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->execute(" CREATE TRIGGER `afterUpdateSales` AFTER UPDATE ON `sales`
        // FOR EACH ROW UPDATE stocks set quantity = (select quantity from Totalinventory where stocks.category = Totalinventory.id
        // and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3)");

        // $this->execute(" CREATE TRIGGER `afterUpdateSales_3` AFTER UPDATE ON `sales`
        // FOR EACH ROW UPDATE stocks set quantity = (select quantity from Totalinventory where stocks.category = Totalinventory.id
        // and stocks.branch = Totalinventory.branch and Totalinventory.type = 3) where stocks.type = 3");
        
        // $this->execute(" CREATE TRIGGER `afterUpdateSales_` AFTER UPDATE ON `sales`
        // FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        // $this->execute(" CREATE TRIGGER `afterUpdateSalesDetails` AFTER UPDATE ON `salesDetails`
        // FOR EACH ROW UPDATE stocks set quantity = (select quantity from Totalinventory where stocks.category = Totalinventory.id
        // and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3)");

        // $this->execute(" CREATE TRIGGER `afterUpdateSalesDetails_3` AFTER UPDATE ON `salesDetails`
        // FOR EACH ROW UPDATE stocks set quantity = (select quantity from Totalinventory where stocks.category = Totalinventory.id
        // and stocks.branch = Totalinventory.branch and Totalinventory.type = 3) where stocks.type = 3");
        
        // $this->execute("  CREATE TRIGGER `afterUpdateSalesDetails_` AFTER UPDATE ON `salesDetails`
        // FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");
        
        // // ================

        // $this->execute("CREATE TRIGGER `beforeDeleteSales` AFTER DELETE ON `sales`
        // FOR EACH ROW UPDATE stocks set quantity = (select quantity from Totalinventory where stocks.category = Totalinventory.id
        // and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3)");
        
        // $this->execute(" CREATE TRIGGER `beforeDeleteSales_` AFTER DELETE ON `sales`
        // FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        // $this->execute("CREATE TRIGGER `beforeDeleteSalesDetails` AFTER DELETE ON `salesDetails`
        // FOR EACH ROW UPDATE stocks set quantity = (select quantity from Totalinventory where stocks.category = Totalinventory.id
        // and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3)");

        // $this->execute(" CREATE TRIGGER `beforeDeleteSalesDetails_` AFTER DELETE ON `salesDetails`
        // FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        // // ================

        // $this->execute(" CREATE TRIGGER `afterUpdatePurchases` AFTER UPDATE ON `purchases`
        // FOR EACH ROW UPDATE stocks set quantity = (select quantity from Totalinventory where stocks.category = Totalinventory.id
        // and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3)");
        

        // $this->execute(" CREATE TRIGGER `afterUpdatePurchases_3` AFTER UPDATE ON `purchases`
        // FOR EACH ROW UPDATE stocks set quantity = (select quantity from Totalinventory where stocks.category = Totalinventory.id
        // and stocks.branch = Totalinventory.branch and Totalinventory.type = 3) where stocks.type =3 ");

        // $this->execute(" CREATE TRIGGER `afterUpdatePurchases_` AFTER UPDATE ON `purchases`
        // FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        // $this->execute(" CREATE TRIGGER `afterUpdatePurchasesDetails` AFTER UPDATE ON `purchasesDetails`
        // FOR EACH ROW UPDATE stocks set quantity = (select quantity from Totalinventory where stocks.category = Totalinventory.id
        // and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3)");

        // $this->execute(" CREATE TRIGGER `afterUpdatePurchasesDetails_3` AFTER UPDATE ON `purchasesDetails`
        // FOR EACH ROW UPDATE stocks set quantity = (select quantity from Totalinventory where stocks.category = Totalinventory.id
        // and stocks.branch = Totalinventory.branch and Totalinventory.type = 3) where stocks.type = 3");

        // $this->execute(" CREATE TRIGGER `afterUpdatePurchasesDetails_` AFTER UPDATE ON `purchasesDetails`
        // FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");
        
        // // ================

        // $this->execute("CREATE TRIGGER `beforeDeletePurchases` AFTER DELETE ON `purchases`
        // FOR EACH ROW UPDATE stocks set quantity = (select quantity from Totalinventory where stocks.category = Totalinventory.id
        // and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3)");

        // $this->execute(" CREATE TRIGGER `beforeDeletePurchases_` AFTER DELETE ON `purchases`
        // FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        // $this->execute("CREATE TRIGGER `beforeDeletePurchasesDetails` AFTER DELETE ON `purchasesDetails`
        // FOR EACH ROW UPDATE stocks set quantity = (select quantity from Totalinventory where stocks.category = Totalinventory.id
        // and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3)");

        // $this->execute(" CREATE TRIGGER `beforeDeletePurchasesDetails_` AFTER DELETE ON `purchasesDetails`
        // FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230626_081900_trigger cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230626_081900_trigger cannot be reverted.\n";

        return false;
    }
    */
}
