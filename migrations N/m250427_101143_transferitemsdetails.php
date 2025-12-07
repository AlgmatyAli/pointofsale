<?php

use yii\db\Schema;

class m250427_101143_transferitemsdetails extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('transferitemsdetails', [
            'id' => $this->primaryKey(),
            'transfer' => $this->integer(11)->notNull(),
            'category' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11)->notNull(),
            'FOREIGN KEY ([[transfer]]) REFERENCES transferitems ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('transferitemsdetails');
    }
}
