<?php

use yii\db\Migration;

/**
 * Class m201107_215438_alter_purchase
 */
class m201107_215438_alter_purchase extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('purchases', 'totalCost', $this->decimal(11,3)->after('type'));
        $this->addColumn('purchases', 'standBy', $this->string()->after('totalCost'));
        $this->addColumn('purchasesDetails', 'totalCost', $this->decimal(11,3)->after('costPrice'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('category', 'totalCost');
        $this->dropColumn('category', 'standBy');
        $this->dropColumn('purchasesDetails', 'totalCost');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201107_215438_alter_purchase cannot be reverted.\n";

        return false;
    }
    */
}
