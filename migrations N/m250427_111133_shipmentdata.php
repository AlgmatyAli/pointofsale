<?php

use yii\db\Schema;

class m250427_111133_shipmentdata extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('shipmentdata', [
            'id' => $this->primaryKey(),
            'shipmentId' => $this->integer(11)->notNull(),
            'at' => $this->date(),
            'value' => $this->decimal(10,3),
            'size' => $this->string(125),
            'country' => $this->string(255)->notNull(),
            'type' => $this->string(255)->notNull(),
            'currancy' => $this->integer(11),
            'customsOffice' => $this->integer(11),
            'notes' => $this->string(255),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[currancy]]) REFERENCES currancy ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[customsOffice]]) REFERENCES customsdeclaration ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('shipmentdata');
    }
}
