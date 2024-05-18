<?php

use yii\db\Schema;

class m999999_210101_balance_history_v3 extends \yii\db\Migration
{
    public function up()
    { 
        $this->execute(" 
        create or replace view  balance_history AS
        select sum(client.balance) AS value, client.id as clinet, 1 as currancy, '2020-01-01' AS AT, null as why, 'اول المدة' AS type, client.type as client_type 
        from client 
            where client.type in(0,2) 
            group by client.id
        union 
        select (sum(`p`.`value`) * -(1)) AS `value`,`p`.`clinet` AS `clinet`,`p`.`currancy` AS `currancy`,`p`.`at` AS `AT`,`p`.`why` AS `why`,'ايصال قبض' AS `type`,`c`.`type` AS `client_type` 
        from (( `receipt` `p` join  `client` `c`) join  `currancy`) 
            where ((`c`.`id` = `p`.`clinet`) and (`p`.`type` = 1) and (`p`.`currancy` =  `currancy`.`id`)) 
            group by `p`.`clinet`,`p`.`currancy`,`p`.`at`,`p`.`why` 
        union 
        select sum(`p`.`total`-`p`.`disscount`-`p`.`paid`) AS `value`,`p`.`clinet` AS `clinet`,1 AS `1`,`p`.`at` AS `at`,`p`.`notes` AS `notes`
        ,'فاتورة مبيعات' AS `type`,`c`.`type` AS `client_type` from (( `sales` `p` join  `client` `c`) )
         where ((`c`.`id` = `p`.`clinet`) and (`p`.`type` = 1) and (`p`.`payWay` in (1,2))) group by `p`.`clinet`,`p`.`at`,`p`.`notes` 
        union 
        select sum(`p`.`value`) AS `value`,`p`.`clinet` AS `clinet`,`p`.`currancy` AS `currancy`,`p`.`at` AS `at`,
        `p`.`why` AS `why`,'ايصال دفع' AS `type`,`c`.`type` AS `client_type` from (( `receipt` `p` join  `client` `c`) join  `currancy`) 
        where ((`c`.`id` = `p`.`clinet`) and (`p`.`type` = 2)  and (`p`.`currancy` =  `currancy`.`id`)) group by `p`.`clinet`,`p`.`currancy`,`p`.`at`,`p`.`why` 
        union 
        select sum((`p`.`total_currancy` * -(1))) AS `value`,`p`.`clinet` AS `clinet`,`p`.`currancy` AS `currancy`,`p`.`at` AS `at`,
        `p`.`notes` AS `notes`,'فاتورة مشتريات' AS `type`,`c`.`type` AS `client_type` 
        from (( `purchases` `p` join  `client` `c`) join  `currancy`) 
        where ((`c`.`id` = `p`.`clinet`) and (`p`.`type` = 1) and (`p`.`payWay` in (1,2)) 
        and (`p`.`currancy` =  `currancy`.`id`)) group by `p`.`clinet`,`p`.`currancy`,`p`.`at`,`p`.`notes` 
        union 
        select (sum(`p`.`total`) * -(1)) AS `value`,`p`.`clinet` AS `clinet`,1 AS `1`,`p`.`at` AS `at`,`p`.`notes` AS `notes`,'مسترجع مبيعات	' AS `type`
        ,`c`.`type` AS `client_type` from (( `sales` `p` join  `client` `c`) ) where ((`c`.`id` = `p`.`clinet`)
         and (`p`.`type` = 2) and (`p`.`payWay` = 2)) group by `p`.`clinet`,`p`.`at`,`p`.`notes`






         ");
       
/*
VIEW `dept` AS

        select id, max(name) as name, sum(balance) as credt, max(phone) as phone, max(type) as type from client where client.type in(0,2) group by id
        UNION
        SELECT sales.clinet, max(client.name), sum(sales.total - sales.paid - disscount), max(client.phone), max(client.type) as type from sales, client
        where sales.clinet=client.id and sales.type=1 and sales.payWay in(1,2)
        group by sales.clinet
        UNION
        SELECT sales.clinet as id, max(client.name) as name, sum(sales.total)*-1 as credt, max(client.phone) AS phone, max(client.type) as type from sales, client
        where sales.clinet=client.id and sales.type=2 and sales.payWay = 2
        group by sales.clinet
        UNION
        select receipt.clinet as id, max(client.name) as name, sum(receipt.value)*-1 as credt, max(client.phone) as phone, max(client.type) as type
        FROM receipt, client where receipt.clinet = client.id and receipt.type = 1
        group by receipt.clinet
        UNION
        SELECT purchases.clinet, max(client.name), sum(purchases.total - purchases.paid)*-1, max(client.phone), max(client.type) as type from purchases, client
        where purchases.clinet=client.id and purchases.type=1 and purchases.payWay in(1,2)
        group by purchases.clinet
        UNION
        select receipt.clinet as id, max(client.name) as name, sum(receipt.value) as credt, max(client.phone) as phone, max(client.type) as type
        FROM receipt, client where receipt.clinet = client.id and receipt.type = 2
        group by receipt.clinet

*/


    }

    public function down()
    {
       //
    }
}
