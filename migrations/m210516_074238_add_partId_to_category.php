<?php

use yii\db\Migration;

/**
 * Class m210516_074238_add_partId_to_category
 */
class m210516_074238_add_partId_to_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('category','commCode', $this->text()->after('path'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210516_074238_add_partId_to_category cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210516_074238_add_partId_to_category cannot be reverted.\n";

        return false;
    }
    */
}
