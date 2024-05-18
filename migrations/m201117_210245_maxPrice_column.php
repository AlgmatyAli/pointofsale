<?php

use yii\db\Migration;

/**
 * Class m201117_210245_maxPrice_column
 */
class m201117_210245_maxPrice_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('purchasesDetails', 'salePrice_', $this->decimal(9,3)->after('salePrice'));
        $this->addColumn('temp_invoice_purchase', 'salePrice_', $this->decimal(9,3)->after('salePrice'));
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201117_210245_maxPrice_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201117_210245_maxPrice_column cannot be reverted.\n";

        return false;
    }
    */
}
