<?php

use yii\db\Schema;

class m999999_210101_balance_history_v2 extends \yii\db\Migration
{
    public function up()
    {
        $this->execute(" 
        create or replace view balance_history as
        select sum(p.`value`)*-1 value, p.`clinet`, p.`currancy` ,p.at  ,p.why  , 'ايصال قبض' type from receipt p , client c , currancy  WHERE  c.id = p.clinet  and p.type=1  
        and p.currancy = currancy.id
        GROUP by  p.`clinet`, p.`currancy` , p.at , p.why

        UNION 
        select sum(p.`value`) value, p.`clinet`, p.`currancy` ,p.at  ,p.why  , 'ايصال دفع' type from receipt p , client c ,currancy WHERE  c.id = p.clinet  and p.type=2 
        and p.currancy = currancy.id
        GROUP by  p.`clinet`, p.`currancy` , p.at , p.why

        UNION 

        select sum(p.total_currancy ) value, p.`clinet`, p.`currancy` ,p.at  ,p.notes   , 'فاتورة مشتريات' type from purchases p , client c ,currancy WHERE  c.id = p.clinet  and p.type=1
        and p.currancy = currancy.id
        GROUP by  p.`clinet`, p.`currancy` , p.at , p.notes
    ");
       
    }

    public function down()
    {
       //
    }
}
