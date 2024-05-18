<?php

use yii\db\Migration;

/**
 * Class m221221_203810_category_histrans
 */
class m999999_203810_category_histrans extends Migration
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

        VIEW `category_histrans` AS
        select 1 kind_id, 'رصيد أول المدة' as kind, 0 billId, category.id, max(category.name) as name, sum(category.quantity) as quantity, 1 branch, category.box, 
        category.class, category.company, category.unit, category.serialNo, category.created_at AS tranDate, 0 detailsId, 0 client, 0 AS printId,
        1 AS deleviried, 0 as ClientId
        from category group by id, branch
        UNION
        select 2 kind_id, 'فاتورة مشتريات رقم' as kind, purchases.billId, purchasesDetails.category, max(category.name), sum(purchasesDetails.quantity), MAX(purchases.branch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), max(purchases.at), purchasesDetails.id,
        max(client.name), purchases.id AS printId, 1 AS deleviried, max(client.id) as ClientId
        from purchases, purchasesDetails, category, client
        where purchases.id=purchasesDetails.PurchasesId and purchasesDetails.category=category.id and purchases.type=1 and purchases.clinet=client.id
        GROUP BY purchasesDetails.category, purchases.branch, purchasesDetails.id
        UNION
        select 3 kind_id, 'فاتورة مسترجع مشتريات رقم ' as kind, purchases.billId, purchasesDetails.category, max(category.name), sum(purchasesDetails.quantity)*-1, MAX(purchases.branch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), max(purchases.at), purchasesDetails.id, max(client.name), 
        purchases.id AS printId, 1 AS deleviried, max(client.id) as ClientId
        from purchases, purchasesDetails, category, client
        where purchases.id=purchasesDetails.PurchasesId and purchasesDetails.category=category.id and purchases.type=2 and purchases.clinet=client.id
        GROUP BY purchasesDetails.category, purchases.branch, purchasesDetails.id
        UNION
        SELECT 4 kind_id, 'فاتورة مبيعات رقم ' as kind, sales.billId, salesDetails.category, max(category.name), SUM(salesDetails.quantity)*-1, max(sales.branch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), max(sales.at), salesDetails.id, max(client.name), sales.id AS printId,
        max(deleviried) AS deleviried, max(client.id) as ClientId
        from sales, salesDetails, category, client
        where sales.id=salesDetails.salesId and salesDetails.category=category.id and sales.type = 1 and sales.clinet=client.id
        GROUP by salesDetails.category, sales.branch, salesDetails.id
        UNION
        SELECT 5 kind_id, 'فاتورة مسترجع مبيعات رقم' as kind, sales.billId, salesDetails.category, max(category.name), SUM(salesDetails.quantity), max(sales.branch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), max(sales.at), salesDetails.id, max(client.name), sales.id AS printId
        , 1 AS deleviried, max(client.id) as ClientId
        from sales, salesDetails, category, client
        where sales.id=salesDetails.salesId and salesDetails.category=category.id and sales.type = 2 and sales.clinet=client.id 
        GROUP by salesDetails.category, sales.branch, salesDetails.id
        UNION
        SELECT 6 kind_id, 'نقل إلى الفرع' as kind, transferItems.id, transferItemsDetails.category, max(category.name), SUM(transferItemsDetails.quantity), max(transferItems.toBranch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), max(transferItems.at), transferItems.id, max(branches.name), 
        transferItems.id AS printId, 1 AS deleviried, 0 as ClientId
        from transferItems, transferItemsDetails, category, branches
        where transferItems.id=transferItemsDetails.transfer and transferItemsDetails.category=category.id 
        and transferItems.toBranch=branches.id 
        GROUP by transferItemsDetails.category, transferItems.toBranch, transferItemsDetails.id
        UNION
        SELECT 7 kind_id, 'نقل من الفرع' as kind, transferItems.id, transferItemsDetails.category, max(category.name), SUM(transferItemsDetails.quantity)*-1, max(transferItems.fromBranch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), max(transferItems.at), transferItems.id, max(branches.name), 
        transferItems.id AS printId, 1 AS deleviried, 0 as ClientId
        from transferItems, transferItemsDetails, category, branches
        where transferItems.id=transferItemsDetails.transfer and transferItemsDetails.category=category.id 
        and transferItems.fromBranch=branches.id 
        GROUP by transferItemsDetails.category, transferItems.fromBranch, transferItemsDetails.id
        UNION
        SELECT 8 kind_id, 'تسوية جرد' as kind, arrangement.id, arrangementDetails.category, max(category.name), SUM(arrangementDetails.quantity * arrangementDetails.type), max(arrangement.branch), max(category.box),
        max(category.class), max(category.company), max(category.unit), max(category.serialNo), max(arrangement.at), arrangement.id, max(branches.name), 
        arrangement.id AS printId, 1 AS deleviried, 0 as ClientId
        from arrangement, arrangementDetails, category, branches
        where arrangement.id = arrangementDetails.arrangement and arrangementDetails.category=category.id 
        and arrangement.branch = branches.id 
        GROUP by arrangementDetails.category, arrangement.branch, arrangementDetails.id
        "
    );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201221_203810_category_histrans cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201221_203810_category_histrans cannot be reverted.\n";

        return false;
    }
    */
}
