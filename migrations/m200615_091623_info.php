<?php

use yii\db\Migration;

/**
 * Class m200615_091623_info
 */
class m200615_091623_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('company_info', [
            'id' =>$this->primaryKey(),
            'name' =>$this->string(150)->notNull(),
            'work' =>$this->string(150)->notNull(),
            'address' =>$this->text(150)->notNull(),
            'phone1' =>$this->string(14)->notNull(),
            'phone2' =>$this->string(14)->notNull(),
            'phone3' =>$this->string(14)->notNull(),
            'fax' =>$this->string(14)->notNull(),
            'email' =>$this->string(20)->notNull(),
            'path'=>$this->string(),
            'searchById'=>$this->integer(),
            'repeatCategory'=>$this->integer(),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('company_info');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200615_091623_info cannot be reverted.\n";

        return false;
    }
    */
}
