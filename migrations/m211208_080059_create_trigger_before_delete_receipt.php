<?php

use yii\db\Migration;

/**
 * Class m211208_080059_create_trigger_before_delete_receipt
 */
class m211208_080059_create_trigger_before_delete_receipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(" 
        CREATE TRIGGER `before_delete` BEFORE DELETE ON `receipt`
        FOR EACH ROW INSERT INTO receipt_arch(rId, clinet, at, value, why, payWay, type)
       VALUES(old.rId, old.clinet, old.at, old.value, old.why, old.payWay, old.type)
       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211208_080059_create_trigger_before_delete_receipt cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211208_080059_create_trigger_before_delete_receipt cannot be reverted.\n";

        return false;
    }
    */
}
