<?php

use yii\db\Migration;

/**
 * Class m201025_061859_view_dept_supp
 */
class m999999_061859_view_dept_supp extends Migration
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

        VIEW `dept_supp` AS
        select id, max(name) as name, sum(balance) as credt, max(phone) as phone, max(type) as type from client where client.type in(1) group by id
        UNION
        SELECT purchases.clinet, max(client.name), sum(purchases.total - purchases.paid), max(client.phone), max(client.type) as type from purchases, client
        where purchases.clinet=client.id and purchases.type=1 and purchases.payWay in(1,2)
        group by purchases.clinet
        UNION
        SELECT purchases.clinet as id, max(client.name) as name, sum(purchases.total - purchases.paid)*-1 as credt, max(client.phone) AS phone, max(client.type) as type from purchases, client
        where purchases.clinet=client.id and purchases.type=2 and purchases.payWay = 2
        group by purchases.clinet
        UNION
        select receipt.clinet as id, max(client.name) as name, sum(receipt.value)*-1 as credt, max(client.phone) as phone, max(client.type) as type
        FROM receipt, client where receipt.clinet = client.id and receipt.type = 2
        group by receipt.clinet
        ");    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201025_061859_view_dept_supp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201025_061859_view_dept_supp cannot be reverted.\n";

        return false;
    }
    */
}
