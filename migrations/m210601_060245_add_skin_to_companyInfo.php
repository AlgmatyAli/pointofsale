<?php

use yii\db\Migration;

/**
 * Class m210601_060245_add_skin_to_companyInfo
 */
class m210601_060245_add_skin_to_companyInfo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company_info','skin', $this->string(100)->after('currancy'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210601_060245_add_skin_to_companyInfo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210601_060245_add_skin_to_companyInfo cannot be reverted.\n";

        return false;
    }
    */
}
