<?php

use yii\db\Schema;

class m250427_100173_receipt_arch extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('receipt_arch', [
            'id' => $this->primaryKey(),
            'rId' => $this->integer(11)->notNull(),
            'clinet' => $this->integer(11)->notNull(),
            'at' => $this->date()->notNull(),
            'value' => $this->decimal(9,3)->notNull(),
            'why' => $this->string(255)->notNull(),
            'payWay' => $this->string()->notNull(),
            'type' => $this->integer(11)->notNull(),
            'delete_by' => $this->integer(11),
            'delete_at' => $this->integer(11),
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('receipt_arch');
    }
}
