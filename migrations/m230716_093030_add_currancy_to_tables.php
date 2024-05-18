<?php

use yii\db\Migration;

/**
 * Class m230716_093030_add_currancy_to_tables
 */
class m230716_093030_add_currancy_to_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
     {
         $this->addColumn('sales', 'currancy', $this->integer()->after('deleviried')->notnull()->defaultValue(1));
         $this->addColumn('expenses', 'currancy', $this->integer()->after('why')->notnull()->defaultValue(1));
         $this->addColumn('emp_salary', 'currancy', $this->integer()->after('type')->notnull()->defaultValue(1));
         $this->addColumn('transfer', 'currancy', $this->integer()->after('why')->notnull()->defaultValue(1));
     }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230716_093030_add_currancy_to_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230716_093030_add_currancy_to_tables cannot be reverted.\n";

        return false;
    }
    */
}
