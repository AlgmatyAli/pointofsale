<?php

use yii\db\Schema;

class m250427_090112_company_info extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('company_info', [
            'id' => $this->primaryKey(),
            'name' => $this->string(150)->notNull(),
            'work' => $this->string(150)->notNull(),
            'address' => $this->text()->notNull(),
            'phone1' => $this->string(14)->notNull(),
            'phone2' => $this->string(14)->notNull(),
            'phone3' => $this->string(14)->notNull(),
            'fax' => $this->string(14)->notNull(),
            'email' => $this->string(20)->notNull(),
            'terms' => $this->text(),
            'path' => $this->string(255),
            'currancy' => $this->integer(11),
            'skin' => $this->string(100),
            'searchById' => $this->integer(11)->notNull(),
            'repeatCategory' => $this->integer(11)->notNull(),
            'payWayCash' => $this->integer(11)->notNull(),
            'invoiceState' => $this->integer(11),
            'waitQnty' => $this->integer(11)->defaultValue(0),
            'rate' => $this->decimal(10,3)->notNull()->defaultValue('0.000'),
            'criteriaـvalue' => $this->integer(11)->notNull()->defaultValue(0),
            'zeroQnty' => $this->integer(11)->notNull()->defaultValue(0),
            'FOREIGN KEY ([[currancy]]) REFERENCES currancy ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);

                          
        $this->insert('company_info', [
            'name' => 'Jupiter',
            'work' => 'Software Development',
            'address' => 'Tripoli - Libya',
            'phone1' => '092-5908515',
            'phone2' => '0',
            'phone3' => '0',
            'fax' => '0',
            'email' => 'info@jupiter.ly',
            'terms' => 'Terms and Conditions',
            'path' => '-',
            'currancy' => '1',
            'skin' => 'skin-green',
            'searchById' => '1',
            'repeatCategory' => '1',
            'payWayCash' => '1',
            'invoiceState' => '1',
            'waitQnty' => '0',
            'rate' => '0.000',
            'criteriaـvalue' => '0',
            'zeroQnty' => '0'
        ]);   
    }

    public function down()
    {
        $this->dropTable('company_info');
    }
}
