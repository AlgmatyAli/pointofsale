<?php

use yii\db\Migration;

/**
 * Class m220130_134109_alter_arrangement_by_add_last_stockTaking
 */
class m220130_134109_alter_arrangement_by_add_last_stockTaking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('arrangementDetails','stockTaking', $this->integer(1)->after('expire')); 
        $this->addColumn('temp_arrangement', 'stockTaking', $this->integer(1)->after('expire'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220130_134109_alter_arrangement_by_add_last_stockTaking cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220130_134109_alter_arrangement_by_add_last_stockTaking cannot be reverted.\n";

        return false;
    }
    */
}
