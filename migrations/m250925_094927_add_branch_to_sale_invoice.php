<?php

use yii\db\Migration;

/**
 * Class m250925_094927_add_branch_to_sale_invoice
 */
class m250925_094927_add_branch_to_sale_invoice extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%temp_invoice}}', 'branch', $this->integer()->after('expire')->notNull()->defaultValue(default: 1));
        $this->addForeignKey(
            'fk-temp_invoice-branch',
            '{{%temp_invoice}}',
            'branch',
            '{{%branches}}',
            'id',
            'CASCADE'
        );

        $this->addColumn('{{%salesDetails}}', 'branch', $this->integer()->after('waitQnty')->notNull()->defaultValue(default: 1));
        $this->addForeignKey(
            'fk-salesDetails-branch',
            '{{%salesDetails}}',
            'branch',
            '{{%branches}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250925_094927_add_branch_to_sale_invoice cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250925_094927_add_branch_to_sale_invoice cannot be reverted.\n";

        return false;
    }
    */
}
