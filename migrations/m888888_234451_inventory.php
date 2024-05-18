<?php

use yii\db\Migration;

/**
 * Class m221111_234451_inventory
 */
class m888888_234451_inventory extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(" 

        CREATE OR REPLACE 

        ALGORITHM = UNDEFINED 

      
        SQL SECURITY DEFINER                        

        VIEW `inventory` AS
        select 1 a, category.id, max(category.name) as name, sum(category.quantity) as quantity, 1 branch, category.box, category.class,
         category.company, category.unit, category.serialNo, 1 type, place, commCode from category group by id, branch
        UNION
        select 2, purchasesDetails.category, max(category.name), sum(purchasesDetails.quantity), MAX(purchases.branch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), max(purchases.type), max(category.place), max(category.commCode)
        from purchases, purchasesDetails, category
        where purchases.id=purchasesDetails.PurchasesId and purchasesDetails.category=category.id and purchases.type in(1,3)
        GROUP BY purchasesDetails.category, purchases.branch, purchases.type
        UNION
        select 3, purchasesDetails.category, max(category.name), sum(purchasesDetails.quantity)*-1, MAX(purchases.branch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), max(purchases.type), max(category.place), max(category.commCode)
        from purchases, purchasesDetails, category
        where purchases.id=purchasesDetails.PurchasesId and purchasesDetails.category=category.id and purchases.type=2 
        GROUP BY purchasesDetails.category, purchases.branch, purchases.type
        UNION
        SELECT 4, salesDetails.category, max(category.name), SUM(salesDetails.quantity)*-1, max(sales.branch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), max(sales.type), max(category.place), max(category.commCode)
        from sales, salesDetails, category
        where sales.id=salesDetails.salesId and salesDetails.category=category.id and sales.type in (1,3)
        GROUP by salesDetails.category, sales.branch, sales.type
        UNION
        SELECT 5, salesDetails.category, max(category.name), SUM(salesDetails.quantity), max(sales.branch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), max(sales.type), max(category.place), max(category.commCode)
        from sales, salesDetails, category
        where sales.id=salesDetails.salesId and salesDetails.category=category.id and sales.type = 2 
        GROUP by salesDetails.category, sales.branch, sales.type
        UNION
        SELECT 6, transferItemsDetails.category, max(category.name), SUM(transferItemsDetails.quantity), max(transferItems.toBranch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), 1, max(category.place), max(category.commCode)
        from transferItems, transferItemsDetails, category
        where transferItems.id=transferItemsDetails.transfer and transferItemsDetails.category=category.id
        GROUP by transferItemsDetails.category, transferItems.toBranch
        UNION
        SELECT 7, transferItemsDetails.category, max(category.name), SUM(transferItemsDetails.quantity)*-1, max(transferItems.fromBranch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), 1, max(category.place), max(category.commCode)
        from transferItems, transferItemsDetails, category
        where transferItems.id=transferItemsDetails.transfer and transferItemsDetails.category=category.id
        GROUP by transferItemsDetails.category, transferItems.fromBranch
        UNION
        SELECT 8, arrangementDetails.category, max(category.name), SUM(arrangementDetails.quantity * arrangementDetails.type), max(arrangement.branch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), 1, max(category.place), max(category.commCode)
        from arrangement, arrangementDetails, category
        where arrangement.id=arrangementDetails.arrangement and arrangementDetails.category=category.id
        GROUP by arrangementDetails.category, arrangement.branch
    "
    );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201111_234451_inventory cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201111_234451_inventory cannot be reverted.\n";

        return false;
    }
    */
}
