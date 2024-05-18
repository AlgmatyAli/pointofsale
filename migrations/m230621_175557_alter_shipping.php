<?php

use yii\db\Migration;

/**
 * Class m230621_175557_alter_shipping
 */
class m230621_175557_alter_shipping extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('shipmentData', 'currancy' , $this->integer()); 
        $this->addColumn('shipmentData', 'customsOffice' , $this->integer()); 
        $this->addColumn('customsOffice', 'currancy' , $this->integer()); 
        $this->alterColumn('shipmentData', 'size', $this->string(125));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230621_175557_alter_shipping cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230621_175557_alter_shipping cannot be reverted.\n";

        return false;
    }
    */
}
