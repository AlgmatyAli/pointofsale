<?php

use yii\db\Migration;

/**
 * Class m201022_064002_view_depts
 */
class m999999_064002_view_depts extends Migration
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
        select id, max(name) as name, sum(balance) as credt, max(phone) as phone, max(type) as type, null as deserving from client where client.type in(0,2) 
        group by id
        UNION
        SELECT sales.clinet, max(client.name), sum(sales.total - sales.paid - disscount), max(client.phone), max(client.type) as type, 
        max(sales.deserving) as deserving from sales, client
        where sales.clinet=client.id and sales.type=1 and sales.payWay in(1,2)
        group by sales.clinet
        UNION
        SELECT sales.clinet as id, max(client.name) as name, sum(sales.total)*-1 as credt, max(client.phone) AS phone, max(client.type) as type, 
        max(sales.deserving) as deserving from sales, client
        where sales.clinet=client.id and sales.type=2 and sales.payWay = 2
        group by sales.clinet
        UNION
        select receipt.clinet as id, max(client.name) as name, sum(receipt.value)*-1 as credt, max(client.phone) as phone, max(client.type) as type,
        null FROM receipt, client where receipt.clinet = client.id and receipt.type = 1
        group by receipt.clinet
        UNION
        SELECT purchases.clinet, max(client.name), sum(purchases.total - purchases.paid)*-1, max(client.phone), max(client.type) as type, null
        from purchases, client
        where purchases.clinet=client.id and purchases.type=1 and purchases.payWay in(1,2)
        group by purchases.clinet
        UNION
        select receipt.clinet as id, max(client.name) as name, sum(receipt.value) as credt, max(client.phone) as phone, max(client.type) as type, null
        FROM receipt, client where receipt.clinet = client.id and receipt.type = 2
        group by receipt.clinet
        ");       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_064002_view_depts cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_064002_view_credts cannot be reverted.\n";

        return false;
    }
    */
}
