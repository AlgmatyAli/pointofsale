<?php

use yii\db\Schema;

class m250427_090133_temp_invoice_purchase extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('temp_invoice_purchase', [
            'id' => $this->primaryKey(),
            'category' => $this->integer(11)->notNull(),
            'quantity' => $this->float()->notNull(),
            'costPrice' => $this->decimal(11,3),
            'costTotal' => $this->decimal(9,3),
            'state' => $this->decimal(11,3),
            'salePrice' => $this->decimal(11,3)->notNull(),
            'salePrice_' => $this->decimal(9,3),
            'salePrice_2' => $this->decimal(9,3),
            'salePrice_3' => $this->decimal(9,3),
            'box' => $this->integer(11),
            'expire' => $this->date(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('temp_invoice_purchase');
    }
}
