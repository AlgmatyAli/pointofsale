<?php

use yii\db\Migration;

/**
 * Class m200615_095929_user
 */
class m200615_095929_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' =>$this->string(),
            'password' =>$this->string(),
            'isActive'=>'enum("disabled","active") NOT NULL',
            'createedDate'=>$this->date(),
            'phone' =>$this->string(),
            'email' =>$this->string(),
            'path'=>$this->string()
        ]);

        // $this->insert('shippingType',array(
        //     'id'=>1,
        //     'username' => 'admin',
        //     'password' => 'admin@pointofsale.ly',
        //     'isActive' => 'active',
        //     'createedDate' => date('Y-m-d'),
        //     'phone' => '0920000000',
        //     'email' => 'admin@pointofsale.ly',
        //     'created_by'=>'1',
        //     'created_at'=> date('Y-m-d')
        //     ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200615_095929_user cannot be reverted.\n";

        return false;
    }
    */
}
