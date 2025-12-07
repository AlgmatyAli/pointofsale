<?php

use yii\db\Schema;

class m250427_100143_purchases extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('purchases', [
            'id' => $this->primaryKey(),
            'billId' => $this->integer(11)->notNull(),
            'clinet' => $this->integer(11)->notNull(),
            'at' => $this->date()->notNull(),
            'payWay' => $this->integer(11)->notNull(),
            'clientBill' => $this->string(255)->notNull(),
            'BuyFor' => $this->integer(11)->notNull(),
            'branch' => $this->integer(11)->notNull(),
            'type' => $this->integer(11)->notNull(),
            'total' => $this->decimal(9,3)->notNull(),
            'paid' => $this->decimal(9,3),
            'notes' => $this->string(255),
            'path' => $this->string(255),
            'totalCost' => $this->decimal(11,3),
            'standBy' => $this->string(255),
            'user_insert' => $this->integer(11)->notNull(),
            'created_at' => $this->date()->notNull(),
            'user_update' => $this->integer(11),
            'update_at' => $this->date(),
            'currancy' => $this->integer(11),
            'total_currancy' => $this->integer(11),
            'shippingType' => $this->integer(11),
            'dateOfArrival' => $this->date(),
            'FOREIGN KEY ([[branch]]) REFERENCES branches ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[clinet]]) REFERENCES client ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_insert]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_update]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            // 'FOREIGN KEY ([[shippingType]]) REFERENCES shippingtype ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
             'FOREIGN KEY ([[currancy]]) REFERENCES currancy	  ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('purchases');
    }
}
