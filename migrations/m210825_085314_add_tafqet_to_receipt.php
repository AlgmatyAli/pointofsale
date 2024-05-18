<?php

use yii\db\Migration;

/**
 * Class m210825_085314_add_tafqet_to_receipt
 */
class m210825_085314_add_tafqet_to_receipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('receipt','tafqet', $this->string()->after('value'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210825_085314_add_tafqet_to_receipt cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210825_085314_add_tafqet_to_receipt cannot be reverted.\n";

        return false;
    }
    */
}
