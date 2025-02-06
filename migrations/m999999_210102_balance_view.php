<?php

use yii\db\Schema;

class m999999_210102_balance_view extends \yii\db\Migration
{
    public function up()
    {
        // $this->execute(" 
        // create or replace view balance as
        // SELECT sum(value) as value  ,`clinet`, `currancy` FROM `balance_history` GROUP by  `clinet`, `currancy`
        //       ");
    }

    public function down()
    {
       //
    }
}
