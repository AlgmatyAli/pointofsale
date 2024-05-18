<?php

use yii\db\Migration;

/**
 * Class m240218_191708_dropSomeTriggers
 */
class m240218_191708_dropSomeTriggers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP TRIGGER IF EXISTS beforeDeletetransferItems");
        $this->execute("DROP TRIGGER IF EXISTS beforeDeletetransferItems_");
        $this->execute("DROP TRIGGER IF EXISTS beforeDeletetransferItemsDetails");
        $this->execute("DROP TRIGGER IF EXISTS beforeDeletetransferItemsDetails_");
        $this->execute("DROP TRIGGER IF EXISTS beforeDeletePurchases");
        $this->execute("DROP TRIGGER IF EXISTS beforeDeletePurchases_");
        $this->execute("DROP TRIGGER IF EXISTS beforeDeletePurchasesDetails");
        $this->execute("DROP TRIGGER IF EXISTS beforeDeletePurchasesDetails_");
        $this->execute("DROP TRIGGER IF EXISTS beforeDeleteSales");
        $this->execute("DROP TRIGGER IF EXISTS beforeDeleteSales_");
        $this->execute("DROP TRIGGER IF EXISTS beforeDeleteSalesDetails");
        $this->execute("DROP TRIGGER IF EXISTS beforeDeleteSalesDetails_");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240218_191708_dropSomeTriggers cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240218_191708_dropSomeTriggers cannot be reverted.\n";

        return false;
    }
    */
}
