<?php

use yii\db\Schema;

class m250427_090103_temp_invoice extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('temp_invoice', [
            'id' => $this->primaryKey(),
            'invoice_number' => $this->integer(11),
            'category' => $this->integer(11)->notNull(),
            'type' => $this->integer(11),
            'serial_number' => $this->string(255),
            'quantity' => $this->float()->notNull(),
            'costPrice' => $this->decimal(11,3),
            'salePrice' => $this->decimal(11,3)->notNull(),
            'box' => $this->integer(11),
            'mac_address' => $this->text(),
            'state' => $this->integer(11),
            'expire' => $this->date(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'waitQnty' => $this->integer(11)->defaultValue(0),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('temp_invoice');
    }
}
