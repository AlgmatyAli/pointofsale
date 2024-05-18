<?php

use yii\db\Migration;

/**
 * Class m201203_051455_costTotal
 */
class m201203_051455_costTotal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('temp_invoice_purchase', 'costTotal', $this->decimal(9,3)->after('costPrice'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // echo "m201203_051455_costTotal cannot be reverted.\n";

        // return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201203_051455_costTotal cannot be reverted.\n";

        return false;
    }
    */
}
