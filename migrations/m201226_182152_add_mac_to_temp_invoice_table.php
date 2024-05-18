<?php

use yii\db\Migration;

/**
 * Class m201203_051455_costTotal
 */
class m201226_182152_add_mac_to_temp_invoice_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('temp_invoice','mac_address', $this->text()->after('box'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // echo "m201203_051455_costTotal cannot be reverted.\n";

        // return false;
    }

    
}
