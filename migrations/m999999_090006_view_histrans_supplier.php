<?php

use yii\db\Migration;

/**
 * Class m201026_090006_view_histrans_supplier
 */
class m999999_090006_view_histrans_supplier extends Migration
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

        VIEW `histrans_supplier` AS
        select 1 AS `sort`,'رصيد أول المدة' AS `kind`,  `client`.`id` AS `id`,  `client`.`name` AS `name`,0 AS `credt`,
          `client`.`balance` AS `dept`,  `client`.`phone` AS `phone`,  `client`.`type` AS `type`,
          `client`.`branch` AS `branch`,0 AS `billId`,curdate() AS `trandate`, 0 AS `printId` from   `client` 
        union select 2 AS `2`,'فاتورة مشتريات رقـم - ' AS `purchases`,  `purchases`.`clinet` AS `clinet`,  `client`.`name` AS `name`,
        0 AS `0`,(  `purchases`.`total` -   `purchases`.`paid`) AS `purchases.total-purchases.paid`,
          `client`.`phone` AS `phone`,  `client`.`type` AS `type`,  `purchases`.
        `branch` AS `branch`,  `purchases`.`billId` AS `billId`,  `purchases`.`at` AS `at`, `purchases`.`id` AS `printId` from (  `purchases` join   `client`) 
        where ((  `purchases`.`payWay` in (1,2)) and (  `purchases`.`clinet` =   `client`.`id`) and (  `purchases`.`type` = 1)) 
        union select 3 AS `3`,'ايصال صرف رقم - ' AS `reciept 2`,  `receipt`.`clinet` AS `clinet`,  `client`.`name` AS `name`,
          `receipt`.`value` AS `value`,0 AS `0`,  `client`.`phone` AS `phone`,  `client`.`type` AS `type`,
          `receipt`.`branch` AS `branch`,  `receipt`.`rId` AS `rId`,  `receipt`.`at` AS `at`, `receipt`.`id` AS `printId`
        from (  `receipt` join   `client`) where ((  `receipt`.`clinet` =   `client`.`id`) and (  `receipt`.`type` = 2)) 
        union select 4 AS `4`,'ترجيع مشتريات رقـم - ' AS `back purchases`,  `purchases`.`clinet` AS `clinet`,
          `client`.`name` AS `name`,  `purchases`.`total` AS `total`,0 AS `0`,  `client`.`phone` AS `phone`,
          `client`.`type` AS `type`,  `purchases`.`branch` AS `branch`,  `purchases`.`billId` AS `billId`,
          `purchases`.`at` AS `at`, `purchases`.`id` AS `printId` from (  `purchases` join   `client`) where ((  `purchases`.`payWay` in (1,2)) 
        and (  `purchases`.`clinet` =   `client`.`id`) and (  `purchases`.`type` = 2) and (  `purchases`.`payWay` = 2))
       "
    );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201026_090006_view_histrans_supplier cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201026_090006_view_histrans_supplier cannot be reverted.\n";

        return false;
    }
    */
}
