<?php

use yii\db\Migration;

/**
 * Class m201119_054421_trigger_b4delete_purchaseDetails
 */
class m201119_054421_trigger_b4delete_purchaseDetails extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    //  $this->execute(" 
    //  CREATE TRIGGER `b4delete` BEFORE DELETE ON `purchasesdetails`
    //  FOR EACH ROW insert into temp
    // values(old.id,old.PurchasesId,old.category,old.quantity,old.costPrice,old.totalCost,old.salePrice,
    //        old.salePrice_,old.box,old.expire)
    //     "
    // );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201119_054421_trigger_b4delete_purchaseDetails cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201119_054421_trigger_b4delete_purchaseDetails cannot be reverted.\n";

        return false;
    }
    */
}
