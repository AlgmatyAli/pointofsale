<?php

use yii\db\Migration;

/**
 * Class m999999_210104_view_histrans_clients_v2
 */
class m999999_210104_view_histrans_clients_v2 extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->execute(
      " 
        CREATE OR REPLACE 
        ALGORITHM = UNDEFINED 
        SQL SECURITY DEFINER                        
        VIEW `histrans_client` AS
        select 1 AS `sort`,'رصيد أول المدة' AS `kind`,  `client`.`id` AS `id`,  `client`.`name` AS `name`,
          `client`.`balance` AS `dept`,0 AS `credt`,  `client`.`phone` AS `phone`,
          `client`.`type` AS `type`,  `client`.`branch` AS `branch`,'2020-01-01' AS `trandate`,0 AS `BillId`, 0 AS  `printId`, 1 AS deleviried, 0 AS currency
        from   `client` 
        union 
        select 2 AS `2`,'مبيعات فاتورة رقم - ' AS `sales`,  `sales`.`clinet` AS `clinet`,
          `client`.`name` AS `name`,(  `sales`.`total` -   `sales`.`paid`-   `sales`.`disscount`) AS `sales.total-sales.paid-sales.disscount`,0 AS `0`,
          `client`.`phone` AS `phone`,  `client`.`type` AS `type`,  `sales`.`branch` AS `branch`,  `sales`.`at` AS `at`,
          `sales`.`billId` AS `billId`, `sales`.`id` AS  `printId`,  deleviried AS deleviried, sales.currancy AS currancy
          from (  `sales` join `client` ) where ((  `sales`.`payWay` in (1,2)) 
        and (  `sales`.`clinet` =   `client`.`id`) and (  `sales`.`type` = 1)) and (sales.currancy in (select currancy from company_info))
        union 
        select 3 AS `3`,'ترجيع مبيعات فاتورة رقم - ' AS `back sales`,
          `sales`.`clinet` AS `clinet`,  `client`.`name` AS `name`,0 AS `0`,  `sales`.`total` AS `total`,  `client`.`phone` AS `phone`,
          `client`.`type` AS `type`,  `sales`.`branch` AS `branch`,  `sales`.`at` AS `at`,  `sales`.`billId` AS `billId`, `sales`.`id` AS  `printId`, 1 AS deleviried, sales.currancy AS currancy
        from (  `sales` join   `client`) where ((  `sales`.`clinet` =   `client`.`id`) and (  `sales`.`type` = 2) 
        and (  `sales`.`payWay` = 2)) and (sales.currancy in (select currancy from company_info))
        union 
        select 4 AS `4`,'تحصيل ايصال رقـم - ' AS `reciept`,  `receipt`.`clinet` AS `clinet`,
          `client`.`name` AS `name`,0 AS `0`,  `receipt`.`value` AS `value`,  `client`.`phone` AS `phone`,  `client`.
        `type` AS `type`,  `receipt`.`branch` AS `branch`,  `receipt`.`at` AS `at`,  `receipt`.`rId` AS `rId`, `receipt`.`id` AS `printId`, 1 AS deleviried, receipt.currancy AS currancy 
        from (  `receipt` join   `client`) where ((  `receipt`.`clinet` =   `client`.`id`) 
        and (  `receipt`.`type` = 1)) and (receipt.currancy in (select currancy from company_info))
        union 
        select 5 AS `5`,'ايصال صرف رقـم - ' AS `reciept 2`,  `receipt`.`clinet` AS `clinet`,
          `client`.`name` AS `name`,  `receipt`.`value` AS `value`,0 AS `0`,  `client`.`phone` AS `phone`,  
        `client`.`type` AS `type`,  `receipt`.`branch` AS `branch`,  `receipt`.`at` AS `at`,  `receipt`.`rId` AS `rId`, `receipt`.`id` AS `printId`, 1 AS deleviried, receipt.currancy AS currancy
        from (  `receipt` join   `client`) where ((  `receipt`.`clinet` =   `client`.`id`) and (  `receipt`.`type` = 2)) and (receipt.currancy in (select currancy from company_info))
        union
        select 6 AS `6`,'مشتريات قاتورة رقم - ' AS `purchases`,  `purchases`.`clinet` AS `clinet`,  `client`.`name` 
        AS `name`,0 AS `0`,(  `purchases`.`total` -   `purchases`.`paid`) AS `purchases.total-purchases.paid`,
          `client`.`phone` AS `phone`,  `client`.`type` AS `type`,  `purchases`.`branch` AS `branch`,  `purchases`.`at` 
        AS `at`,  `purchases`.`billId` AS `billId`,  `purchases`.`id` AS `printId`, 1 AS deleviried, purchases.currancy AS currancy
        from (  `purchases` join   `client`) where ((  `purchases`.`payWay` in (1,2)) 
        and (  `purchases`.`clinet` =   `client`.`id`) and (  `purchases`.`type` = 1)) and (purchases.currancy in (select currancy from company_info))
        union 
        select 7 AS `7`,'ترجيع مشتريات فاتورة رقـم - ' AS `back purchases`,  `purchases`.`clinet` AS `clinet`,  `client`.`name` 
        AS `name`,   `purchases`.`total` AS `total`, 0 AS `0`,  `client`.`phone` AS `phone`,  `client`.`type` AS `type`,
          `purchases`.`branch` AS `branch`,  `purchases`.`at` AS `at`,  `purchases`.`billId` AS `billId`,  `purchases`.`id` AS `printId`, 1 AS deleviried, purchases.currancy AS currancy
        from (  `purchases` join   `client`) where ((  `purchases`.`payWay` in (1,2)) 
        and (  `purchases`.`clinet` =   `client`.`id`) and (  `purchases`.`type` = 2) 
        and (  `purchases`.`payWay` = 2)) and (purchases.currancy in (select currancy from company_info))
        union
        select 8 AS `8`,'  تخفيض على المبيعات - ' AS `kind`,  `disscount_clients`.`client` AS `clinet`,  `client`.`name` AS `name`,
          `disscount_clients`.`value` AS `value`,0 AS `0`,  `client`.`phone` AS `phone`,  `client`.`type` AS `type`,
          `disscount_clients`.`branch` AS `branch`, `disscount_clients`.`at` AS `at`, `disscount_clients`.`notes` AS `billId`, `disscount_clients`.`id` AS `printId`
          , 1 AS deleviried, `disscount_clients`.`currancy`
        from (  `disscount_clients` join   `client`) where ((  `disscount_clients`.`client` =   `client`.`id`) and (  `disscount_clients`.`type` = 1)) 
        and (disscount_clients.currancy in (select currancy from company_info)) 
        union
        select 9 AS `9`,'  خصم من المشتريات - ' AS `kind`,  `disscount_clients`.`client` AS `clinet`,  `client`.`name` AS `name`,
          0 AS `0`,  `disscount_clients`.`value` AS `value`, `client`.`phone` AS `phone`,  `client`.`type` AS `type`,
          `disscount_clients`.`branch` AS `branch`, `disscount_clients`.`at` AS `at`, `disscount_clients`.`notes` AS `billId`, `disscount_clients`.`id` AS `printId`
          , 1 AS deleviried, `disscount_clients`.`currancy`
        from (  `disscount_clients` join   `client`) where ((  `disscount_clients`.`client` =   `client`.`id`) and (  `disscount_clients`.`type` = 2)) 
        and (disscount_clients.currancy in (select currancy from company_info)) 
       "
    );
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    echo "m999999_210104_view_histrans_clients_v2 cannot be reverted.\n";

    return false;
  }

  /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m999999_210104_view_histrans_clients_v2 cannot be reverted.\n";

        return false;
    }
    */
}
