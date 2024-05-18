<?php

use yii\db\Schema;

class m201122_060102_salesDetails_deleted extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('salesDetails_deleted', [
            'id' => $this->integer(11)->notNull()->defaultValue(0),
            'salesId' => $this->integer(11)->notNull(),
            'category' => $this->integer(11)->notNull(),
            'quantity' => $this->float()->notNull(),
            'costPrice' => $this->decimal(9,3)->notNull(),
            'salePrice' => $this->decimal(9,3),
            'box' => $this->integer(11)->notNull(),
            'expire' => $this->date(),
            'FOREIGN KEY ([[salesId]]) REFERENCES sales_deleted ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('salesDetails_deleted');
    }
}
