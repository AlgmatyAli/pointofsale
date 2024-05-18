<?php

use yii\db\Migration;

/**
 * Class m201107_233451_create_admin_user
 */
class m201107_233451_create_admin_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    //     $this->insert('user',array(
    //         'id'=>0,
    //         'username' => 'naeem.exe@gmail.com',
    //         'email' => 'naeem.exe@gmail.com',
    //         'password' => Yii::$app->security->generatePasswordHash('naeem.exe@gmail.com'),
    //         'branch'=>'1',
    //         'isActive'=>'active',
    //  ));
    //  $this->insert('company_info',array(
    //     'id'=>0,
    //     'name' => 'منظومة المبيعات',
    //     'work' => 'منظومة المبيعات',
    //     'address' => 'منظومة المبيعات',
    //     'phone1' => '1',
    //     'phone2' =>'2',
    //     'phone3'=>'3',
    //     'fax'=>'0',
    //     'email' => 'naeem.exe@gmail.com',
    //     'terms'=>'-',
    //     ));
    //     $this->insert('branches',array(
    //         'id'=>0,
    //         'name' => 'الرئيسي ',
    //         'user_insert'=>'1',
    //         'created_at'=>date('Y-m-d')
    //         ));

    //     $this->insert('client',array(
    //         'id'=>0,
    //         'name' => 'زبون نقدي',
    //         'type' => '2',
    //         'user_insert'=>'1',
    //         'created_at'=>date('Y-m-d'),
    //         'branch'=>'1',

    //         ));   
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201107_233451_create_admin_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201107_233451_create_admin_user cannot be reverted.\n";

        return false;
    }
    */
}
