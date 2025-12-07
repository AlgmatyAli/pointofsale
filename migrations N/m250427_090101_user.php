<?php

use yii\db\Schema;

class m250427_090101_user extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255),
            'password' => $this->string(255),
            'isActive' => $this->string()->notNull(),
            'createedDate' => $this->date(),
            'phone' => $this->string(255),
            'email' => $this->string(255),
            'path' => $this->string(255),
            'branch' => $this->integer(11)->notNull(),
            'permission' => $this->integer(11),
            'seeCostPrice' => $this->integer(11),
            'editSalePrice' => $this->integer(11),
            'makeDiscount' => $this->integer(11),
            'maxDiscount' => $this->decimal(10, 3),
            'maxExpenses' => $this->decimal(10, 3),
            'maxReceipt' => $this->decimal(10, 3),
            'printPurtchaseInvoice' => $this->integer(11),
            'client' => $this->string(255),
            'seeOtherBranchQ' => $this->integer(11)->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->insert('user', [
            'username' => 'admin',
            'password' => '200ceb26807d6bf99fd6f4f0d1ca54d4',
            'isActive' => 'active',
            'createedDate' => date('Y-m-d'),
            'phone' => '0',
            'email' => '0',
            'path' => '-',
            'branch' => 1,
            'permission' => 1,
            'seeCostPrice' => 1,
            'editSalePrice' => 1,
            'makeDiscount' => 1,
            'maxDiscount' => 0.000,
            'maxExpenses' => 0.000,
            'maxReceipt' => 0.000,
            'printPurtchaseInvoice' => 1,
        ]);

        $this->insert('auth_assignment', [
            'item_name' => 'مدير',
            'user_id' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->dropTable('user');
    }
}
