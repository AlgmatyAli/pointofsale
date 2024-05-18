<?php

use yii\db\Migration;


class m201107_212927_alter_company_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company_info', 'terms', $this->text()->after('email'));
         }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('company_info', 'terms');
       
    }

}
