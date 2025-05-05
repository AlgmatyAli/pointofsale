<?php

use yii\db\Migration;

/**
 * Class m999999_210105_trigger
 */
class m999999_210105_trigger extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(" CREATE TRIGGER `afterUpdateSales` AFTER UPDATE ON `sales`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3), 0)");

        $this->execute(" CREATE TRIGGER `afterUpdateSales_3` AFTER UPDATE ON `sales`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type = 3), 0) where stocks.type = 3");
        
        $this->execute(" CREATE TRIGGER `afterUpdateSales_` AFTER UPDATE ON `sales`
        FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        $this->execute(" CREATE TRIGGER `afterUpdateSalesDetails` AFTER UPDATE ON `salesDetails`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3), 0)");

        $this->execute(" CREATE TRIGGER `afterUpdateSalesDetails_3` AFTER UPDATE ON `salesDetails`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type = 3), 0) where stocks.type = 3");
        
        $this->execute("  CREATE TRIGGER `afterUpdateSalesDetails_` AFTER UPDATE ON `salesDetails`
        FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");
        
        // ================

        $this->execute("CREATE TRIGGER `beforeDeleteSales` AFTER DELETE ON `sales`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3), 0)");
        
        $this->execute(" CREATE TRIGGER `beforeDeleteSales_` AFTER DELETE ON `sales`
        FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        $this->execute("CREATE TRIGGER `beforeDeleteSalesDetails` AFTER DELETE ON `salesDetails`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3), 0)");

        $this->execute(" CREATE TRIGGER `beforeDeleteSalesDetails_` AFTER DELETE ON `salesDetails`
        FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        // ================

        $this->execute(" CREATE TRIGGER `afterUpdatePurchases` AFTER UPDATE ON `purchases`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3), 0)");
        

        $this->execute(" CREATE TRIGGER `afterUpdatePurchases_3` AFTER UPDATE ON `purchases`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type = 3), 0) where stocks.type =3 ");

        $this->execute(" CREATE TRIGGER `afterUpdatePurchases_` AFTER UPDATE ON `purchases`
        FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        $this->execute(" CREATE TRIGGER `afterUpdatePurchasesDetails` AFTER UPDATE ON `purchasesDetails`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3), 0)");

        $this->execute(" CREATE TRIGGER `afterUpdatePurchasesDetails_3` AFTER UPDATE ON `purchasesDetails`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type = 3), 0) where stocks.type = 3");

        $this->execute(" CREATE TRIGGER `afterUpdatePurchasesDetails_` AFTER UPDATE ON `purchasesDetails`
        FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");
        
        // ================

        $this->execute("CREATE TRIGGER `beforeDeletePurchases` AFTER DELETE ON `purchases`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3), 0)");

        $this->execute(" CREATE TRIGGER `beforeDeletePurchases_` AFTER DELETE ON `purchases`
        FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        $this->execute("CREATE TRIGGER `beforeDeletePurchasesDetails` AFTER DELETE ON `purchasesDetails`
        FOR EACH ROW UPDATE stocks set quantity = IFNULL((select quantity from Totalinventory where stocks.category = Totalinventory.id
        and stocks.branch = Totalinventory.branch and Totalinventory.type <> 3), 0)");

        $this->execute(" CREATE TRIGGER `beforeDeletePurchasesDetails_` AFTER DELETE ON `purchasesDetails`
        FOR EACH ROW UPDATE stocks a set a.quantity = 0 where a.quantity is null");

        // ================
        $this->execute(
            " CREATE TRIGGER `insertPrices` AFTER INSERT ON `category`
                    FOR EACH ROW INSERT INTO prices(category, costPrice, minPrice, maxPrice, minPrice2, minPrice3)
                    VALUES(new.id, new.cost, new.price, new.price, 0,0)"
        );
        
        $this->execute(
            " CREATE TRIGGER `insertStocks` AFTER INSERT ON `category`
                    FOR EACH ROW 
                    BEGIN
                        DECLARE branch_id INT;
                        DECLARE done INT DEFAULT 0;
                        DECLARE branch_cursor CURSOR FOR SELECT id FROM branches;
                        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

                        OPEN branch_cursor;

                        read_loop: LOOP
                            FETCH branch_cursor INTO branch_id;
                            IF done THEN
                                LEAVE read_loop;
                            END IF;
                            INSERT INTO stocks(category, quantity, branch, type)
                            VALUES (NEW.id, NEW.quantity, branch_id, 1);
                        END LOOP;

                        CLOSE branch_cursor;
                    END"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m999999_210105_trigger cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m999999_210105_trigger cannot be reverted.\n";

        return false;
    }
    */
}
