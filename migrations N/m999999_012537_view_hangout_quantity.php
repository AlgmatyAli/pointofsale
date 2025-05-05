<?php

use yii\db\Migration;

/**
 * Class m201225_012537_view_hangout_quantity
 */
class m999999_012537_view_hangout_quantity extends Migration
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
        VIEW `hangouts_quantity` AS
        
        select purchasesDetails.category as id, purchasesDetails.costPrice as costPrice, 
        purchasesDetails.totalCost as totalCost, purchasesDetails.salePrice as salePrice, purchasesDetails.salePrice_ as salePrice_,
        purchasesDetails.PurchasesId as PurchasesId,
        max(category.name) as name, sum(purchasesDetails.quantity) as quantity,
        MAX(purchases.branch) as branch, max(category.box) as box,
        max(category.class) as class, max(category.company) as company, max(category.unit) as unit, max(category.serialNo) as serialNo
        from purchases, purchasesDetails, category
        where purchases.id=purchasesDetails.PurchasesId and purchasesDetails.category=category.id 
        and purchases.type=3 GROUP BY purchasesDetails.category, purchases.branch
        "
    );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201225_012537_view_hangout_quantity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201225_012537_view_hangout_quantity cannot be reverted.\n";

        return false;
    }
    */
}
