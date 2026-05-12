<?php

use yii\db\Schema;

class m250427_111113_reorderdetails extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('reorderDetails', [
            'id' => $this->primaryKey(),
            'reorder' => $this->integer(11)->notNull(),
            'category' => $this->integer(11)->notNull(),
            'quantity' => $this->float()->notNull(),
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[reorder]]) REFERENCES reorder ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('reorderDetails');
    }
}
