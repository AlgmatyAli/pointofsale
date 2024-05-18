<?php

use yii\db\Migration;

/**
 * Class m210803_103442_add_packing_to_salesDetails
 */
class m210803_103442_add_packing_to_salesDetails extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('salesDetails', 'packing', $this->text()->after('expire'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210803_103442_add_packing_to_salesDetails cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210803_103442_add_packing_to_salesDetails cannot be reverted.\n";

        return false;
    }
    */
}
