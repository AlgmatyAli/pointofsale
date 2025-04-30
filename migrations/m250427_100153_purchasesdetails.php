<?php

use yii\db\Schema;

class m250427_100153_purchasesdetails extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('purchasesdetails', [
            'id' => $this->primaryKey(),
            'PurchasesId' => $this->integer(11)->notNull(),
            'category' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11)->notNull(),
            'costPrice' => $this->decimal(9,3)->notNull(),
            'totalCost' => $this->decimal(11,3),
            'salePrice' => $this->decimal(9,3),
            'salePrice_' => $this->decimal(9,3),
            'salePrice_2' => $this->decimal(9,3),
            'salePrice_3' => $this->decimal(9,3),
            'box' => $this->integer(11)->notNull(),
            'expire' => $this->date(),
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[PurchasesId]]) REFERENCES purchases ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('purchasesdetails');
    }
}
