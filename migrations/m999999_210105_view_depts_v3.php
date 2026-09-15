<?php

use yii\db\Migration;

/**
 * Class m999999_210105_view_depts_v3
 */
class m999999_210105_view_depts_v3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(" 

        CREATE OR REPLACE 
        ALGORITHM = UNDEFINED 
        SQL SECURITY DEFINER                        

        VIEW `dept` AS
        select id, max(name) as name, sum(balance) as credt, max(phone) as phone, max(type) as type, null as deserving, 0 as currency, max(post_paid) as post_paid 
         from client where client.type in(0,2) 
        group by id, currency
        UNION
        SELECT sales.clinet, max(client.name), sum(sales.total - sales.paid - disscount), max(client.phone), max(client.type) as type, 
        max(sales.deserving) as deserving, sales.currancy, client.post_paid from sales, client
        where sales.clinet=client.id and sales.type=1 and sales.payWay in(1,2) and (sales.currancy in (select currancy from company_info))
        group by sales.clinet, sales.currancy
        UNION
        SELECT sales.clinet as id, max(client.name) as name, sum(sales.total)*-1 as credt, max(client.phone) AS phone, max(client.type) as type, 
        max(sales.deserving) as deserving, sales.currancy, client.post_paid from sales, client
        where sales.clinet=client.id and sales.type=2 and sales.payWay = 2 and (sales.currancy in (select currancy from company_info))
        group by sales.clinet, sales.currancy
        UNION
        select receipt.clinet as id, max(client.name) as name, sum(receipt.value)*-1 as credt, max(client.phone) as phone, max(client.type) as type,
        null, receipt.currancy, client.post_paid FROM receipt, client where receipt.clinet = client.id and receipt.type = 1 and (receipt.currancy in (select currancy from company_info))
        group by receipt.clinet, receipt.currancy
        UNION
        select disscount_clients.client as id, max(client.name) as name, sum(disscount_clients.value)*-1 as credt, max(client.phone) as phone, max(client.type) as type, null as deserving, disscount_clients.currancy, client.post_paid
        FROM disscount_clients, client where disscount_clients.client = client.id and disscount_clients.type = 2
        group by disscount_clients.client
        UNION
        SELECT purchases.clinet, max(client.name), sum(purchases.total - purchases.paid)*-1, max(client.phone), max(client.type) as type, null, purchases.currancy, client.post_paid
        from purchases, client
        where purchases.clinet=client.id and purchases.type=1 and purchases.payWay in(1,2) and (purchases.currancy in (select currancy from company_info))
        group by purchases.clinet, purchases.currancy
        UNION
        select receipt.clinet as id, max(client.name) as name, sum(receipt.value) as credt, max(client.phone) as phone, max(client.type) as type, null, receipt.currancy, client.post_paid
        FROM receipt, client where receipt.clinet = client.id and receipt.type = 2 and (receipt.currancy in (select currancy from company_info))
        group by receipt.clinet, receipt.currancy
        UNION
        select disscount_clients.client as id, max(client.name) as name, sum(disscount_clients.value) as credt, max(client.phone) as phone, max(client.type) as type, null as deserving, disscount_clients.currancy, client.post_paid
        FROM disscount_clients, client where disscount_clients.client = client.id and disscount_clients.type = 1
        group by disscount_clients.client
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m999999_210105_view_depts_v3 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m999999_210105_view_depts_v3 cannot be reverted.\n";

        return false;
    }
    */
}
