<?php

use yii\db\Migration;

/**
 * Class m210914_064027_add_deservingDate_to_sales
 */
class m210914_064027_add_deservingDate_to_sales extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('sales', 'deserving', $this->date()->after('deleviryId'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210914_064027_add_deservingDate_to_sales cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210914_064027_add_deservingDate_to_sales cannot be reverted.\n";

        return false;
    }
    */
}
