<?php

use yii\db\Migration;

/**
 * Handles adding user_type to table `user`.
 */
class m201019_132756_add_user_type_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->addColumn('user', 'permission', $this->integer(4)->after('branch'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->dropColumn('user', 'user_type');
    }
}
