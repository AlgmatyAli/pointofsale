<?php

use yii\db\Schema;

class m250428_080101_shippingtype extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('shippingType', [
            'id' => $this->primaryKey(),
            'name' => $this->string(125)->notNull(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->insert('shippingType', array(
            'id' => 2,
            'name' => 'بري ',
            'created_by' => '1',
            'created_at' => date('Y-m-d')
        ));
        $this->insert('shippingType', array(
            'id' => 3,
            'name' => 'بحري ',
            'created_by' => '1',
            'created_at' => date('Y-m-d')
        ));
        $this->insert('shippingType', array(
            'id' => 1,
            'name' => 'جوي ',
            'created_by' => '1',
            'created_at' => date('Y-m-d')
        ));
    }

    public function down()
    {
        $this->dropTable('shippingType');
    }
}
