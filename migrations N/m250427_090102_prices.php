<?php

use yii\db\Schema;

class m250427_090102_prices extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('prices', [
            'id' => $this->primaryKey(),
            'category' => $this->integer(11)->notNull(),
            'costPrice' => $this->decimal(9, 3)->notNull(),
            'minPrice' => $this->decimal(9, 3),
            'minPrice2' => $this->decimal(9, 3),
            'minPrice3' => $this->decimal(9, 3),
            'maxPrice' => $this->decimal(9, 3),
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('prices');
    }
}
