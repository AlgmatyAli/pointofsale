<?php

use yii\db\Migration;

/**
 * Class m201226_182147_add_minPrice_feilds
 */
class m201226_182147_add_minPrice_feilds extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('prices', 'minPrice2', $this->decimal(9,3)->after('minPrice'));
        $this->addColumn('prices', 'minPrice3', $this->decimal(9,3)->after('minPrice2'));
        $this->addColumn('temp_invoice_purchase', 'salePrice_2', $this->decimal(9,3)->after('salePrice_'));
        $this->addColumn('temp_invoice_purchase', 'salePrice_3', $this->decimal(9,3)->after('salePrice_2'));
        $this->addColumn('purchasesDetails', 'salePrice_2', $this->decimal(9,3)->after('salePrice_'));
        $this->addColumn('purchasesDetails', 'salePrice_3', $this->decimal(9,3)->after('salePrice_2'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201230_192131_add cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201230_192131_add cannot be reverted.\n";

        return false;
    }
    */
}
