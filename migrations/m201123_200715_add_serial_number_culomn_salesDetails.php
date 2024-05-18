<?php

use yii\db\Migration;


class m201123_200715_add_serial_number_culomn_salesDetails extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('salesDetails', 'serial_number', $this->text()->after('category'));
        
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
