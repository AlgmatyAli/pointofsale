```php
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

            SELECT
                purchasesDetails.category AS id,

                MAX(purchasesDetails.costPrice) AS costPrice,
                MAX(purchasesDetails.totalCost) AS totalCost,
                MAX(purchasesDetails.salePrice) AS salePrice,
                MAX(purchasesDetails.salePrice_) AS salePrice_,
                MAX(purchasesDetails.PurchasesId) AS PurchasesId,

                MAX(category.name) AS name,

                SUM(purchasesDetails.quantity) AS quantity,

                MAX(purchases.branch) AS branch,

                MAX(category.box) AS box,
                MAX(category.class) AS class,
                MAX(category.company) AS company,
                MAX(category.unit) AS unit,
                MAX(category.serialNo) AS serialNo

            FROM purchases
            INNER JOIN purchasesDetails
                ON purchases.id = purchasesDetails.PurchasesId

            INNER JOIN category
                ON purchasesDetails.category = category.id

            WHERE purchases.type = 3

            GROUP BY
                purchasesDetails.category,
                purchases.branch
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201225_012537_view_hangout_quantity cannot be reverted.\n";

        return false;
    }
}
