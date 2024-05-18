
<?php

use yii\db\Migration;

/**
 * Class m201019_132755_alter_table_add_branches
 */
class m201019_132755_alter_table_add_branches extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'branch', $this->integer()->notNull());
        $this->addColumn('receipt', 'branch', $this->integer()->notNull());
        $this->addColumn('expenses', 'branch', $this->integer()->notNull());
        $this->addColumn('client', 'branch', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'branch');
        $this->dropColumn('receipt', 'branch');
        $this->dropColumn('expenses', 'branch');
        $this->dropColumn('client', 'branch');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_132755_alter_user cannot be reverted.\n";

        return false;
    }
    */
}
