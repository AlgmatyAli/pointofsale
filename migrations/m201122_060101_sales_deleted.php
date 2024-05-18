<?php

use yii\db\Schema;

class m201122_060101_sales_deleted extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('sales_deleted', [
            'id' => $this->integer(11)->notNull(),
            'billId' => $this->integer(11)->notNull(),
            'at' => $this->date()->notNull(),
            'clinet' => $this->integer(11)->notNull(),
            'payWay' => $this->integer(11)->notNull(),
            'branch' => $this->integer(11)->notNull(),
            'total' => $this->decimal(9,3)->notNull(),
            'paid' => $this->decimal(9,3),
            'notes' => $this->string(255),
            'path' => $this->string(255),
            'type' => $this->integer(11)->notNull(),
            'deleviryAt' => $this->date(),
            'carpenter' => $this->integer(11),
            'upholstered' => $this->integer(11),
            'paintId' => $this->integer(11),
            'deleviryId' => $this->integer(11),
            'user_insert' => $this->integer(11)->notNull(),
            'created_at' => $this->date()->notNull(),
            'user_update' => $this->integer(11),
            'update_at' => $this->date(),
            ], $tableOptions);

            $this->createIndex(
                'idx-sales-deleted',
                'sales_deleted',
                'id'
                
            );
                
    }

    public function down()
    {
        $this->dropTable('sales_deleted');
    }
}
