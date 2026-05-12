<?php

use yii\db\Schema;

class m250427_090123_salesdetails extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('salesDetails', [
            'id' => $this->primaryKey(),
            'salesId' => $this->integer(11)->notNull(),
            'category' => $this->integer(11)->notNull(),
            'type' => $this->integer(11),
            'serial_number' => $this->text(),
            'quantity' => $this->float()->notNull(),
            'costPrice' => $this->decimal(9, 3)->notNull(),
            'salePrice' => $this->decimal(9, 3),
            'original_price' => $this->decimal(9, 3),
            'box' => $this->integer(11)->notNull(),
            'expire' => $this->date(),
            'packing' => $this->text(),
            'waitQnty' => $this->integer(11)->defaultValue(0),
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[salesId]]) REFERENCES sales ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('salesDetails');
    }
}
