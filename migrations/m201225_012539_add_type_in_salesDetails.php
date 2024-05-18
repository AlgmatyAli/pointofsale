<?php

use yii\db\Migration;


class m201225_012539_add_type_in_salesDetails extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('salesDetails', 'type', $this->integer()->after('category'));
        $this->addColumn('temp_invoice', 'type', $this->integer()->after('category'));
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // echo "m201117_210245_maxPrice_column cannot be reverted.\n";

        // return false;
    }

   
}
