<?php

use yii\db\Migration;

/**
 * Class m241022_185333_ftran
 */
class m999999_185333_ftran extends Migration
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

        VIEW `ftran` AS
        select  `sales`.`at` AS `date_`,concat('فاتورة مبيعات رقم -', `sales`.`billId`,' - ', `client`.`name`) AS `description`,
        ( `sales`.`total` - `sales`.`disscount`) AS `wared`,0 AS `sader`, `sales`.`payWay` AS `payWay`, `sales`.`branch` AS `branch`, 
        `sales`.`user_insert` AS `user_insert`, 0 AS outBox, (`sales`.`disscount`) AS `disscount` from  `sales` join  `client` where (( `sales`.`type` = 1) 
        and ( `sales`.`payWay` in (0)) and ( `sales`.`clinet` =  `client`.`id`)) 
        union select  `sales`.`at` AS `date_`,concat('دفعة على فاتورة مبيعات رقم -', `sales`.`billId`,' - ', `client`.`name`) AS `description`,
        ( `sales`.`total` - `sales`.`disscount`) AS `wared`,0 AS `sader`, `sales`.`payWay` AS `payWay`, `sales`.`branch` AS `branch`, 
        `sales`.`user_insert` AS `user_insert`, 0 AS outBox, (`sales`.`disscount`) AS `disscount` from  `sales` join  `client` where (( `sales`.`type` = 1) 
        and ( `sales`.`payWay` in (2)) and ( `sales`.`clinet` =  `client`.`id`)) 
        union select  `purchases`.`at` AS `at`,concat('فاتورة مشتريات رقم -', `purchases`.`billId`,' - ', `client`.`name`) AS `description`,
        0 AS `0`,( `purchases`.`total`) AS `purchases.total-purchases.paid`, `purchases`.`payWay` AS `payWay`, `purchases`.`branch` AS `branch`, 
        `purchases`.`user_insert` AS `user_insert`, 0 AS outBox, 0 AS `disscount` from  `purchases` join  `client` where (( `purchases`.`type` = 1) 
        and ( `purchases`.`payWay` in (0)) and ( `purchases`.`clinet` =  `client`.`id`)) 
        union select  `purchases`.`at` AS `at`,concat('دفعة على فاتورة مشتريات رقم -', `purchases`.`billId`,' - ', `client`.`name`) AS `description`,
        0 AS `0`,(`purchases`.`paid`) AS `purchases.total-purchases.paid`, `purchases`.`payWay` AS `payWay`, `purchases`.`branch` AS `branch`, 
        `purchases`.`user_insert` AS `user_insert`, 0 AS outBox, 0 AS `disscount` from  `purchases` join  `client` where (( `purchases`.`type` = 1) 
        and ( `purchases`.`payWay` in (2)) and ( `purchases`.`clinet` =  `client`.`id`)) 
        union select  `receipt`.`at` AS `at`,concat(' ايصال قبض رقم -', `receipt`.`rId`,' - ', `client`.`name`) AS `description`, 
        `receipt`.`value` AS `value`,0 AS `0`,NULL AS `NULL`, `receipt`.`branch` AS `branch`, 
        `receipt`.`user_insert` AS `user_insert`, 0 AS outBox, 0 AS `disscount` from  `receipt` join  `client` where (( `receipt`.`type` = 1) 
        and ( `receipt`.`clinet` =  `client`.`id`)) 
        union select  `receipt`.`at` AS `at`,concat('ايصال صرف رقم -', `receipt`.`rId`,' - ', `client`.`name`) AS `description`,
        0 AS `0`, `receipt`.`value` AS `value`,NULL AS `NULL`, `receipt`.`branch` AS `branch`, `receipt`.`user_insert` 
        AS `user_insert`, 0 AS outBox, 0 AS `disscount` from  `receipt` join  `client` where (( `receipt`.`type` = 2) 
        and ( `receipt`.`clinet` =  `client`.`id`)) 
        union select  `expenses`.`at` AS `at`,concat('مصروفات نقدية رقم -', `expenses`.`id`,' - ', `items`.`name`) AS `description`,
        0 AS `0`, `expenses`.`value` AS `value`,NULL AS `NULL`, `expenses`.`branch` AS `branch`, 
        `expenses`.`user_insert` AS `user_insert`, `expenses`.`outBox` AS outBox, 0 AS `disscount` from  `expenses` join  `items` where ( `expenses`.`itemId` =  `items`.`id`) 
        union select  `sales`.`at` AS `date_`,concat('فاتورة مسترجع مبيعات رقم -', `sales`.`billId`,' - ', `client`.`name`) AS `description`,
        0 AS `wared`, `sales`.`total` AS `sader`, `sales`.`payWay` AS `payWay`, `sales`.`branch` AS `branch`, 
        `sales`.`user_insert` AS `user_insert`, 0 AS outBox, 0 AS `disscount` from  `sales` join  `client` where (( `sales`.`type` = 2) 
        and ( `sales`.`payWay` = 1) and ( `sales`.`clinet` =  `client`.`id`)) 
        union select  `purchases`.`at` AS `at`,concat('فاتورة مشتريات رقم -', `purchases`.`billId`,' - ', `client`.`name`) AS `description`, 
        `purchases`.`total` AS `total`,0 AS `0`, `purchases`.`payWay` AS `payWay`, `purchases`.`branch` AS `branch`, 
        `purchases`.`user_insert` AS `user_insert`, 0 AS outBox, 0 AS `disscount` from  `purchases` join  `client` where (( `purchases`.`type` = 2) 
        and ( `purchases`.`payWay` = 1) and ( `purchases`.`clinet` =  `client`.`id`))
        union select  `emp_salary`.`at` AS `at`,concat('دفعة على الحساب من المرتب -', `employee`.`name`, ' - ',`emp_salary`.`why`) AS `description`, 
        0 AS `total`,`emp_salary`.`value` AS `sader`, 0 AS `payWay`, 1 AS `BrId`, `emp_salary`.`created_by` AS `created_by`, 0 AS outBox, 0 AS `disscount` 
        from  `emp_salary` join  `employee` where (( `emp_salary`.`employee` =  `employee`.`id` and emp_salary.type=1))
        union select  `emp_salary`.`at` AS `at`,concat('دفعة اضافي -', `employee`.`name`, ' - ',`emp_salary`.`why`) AS `description`, 
        0 AS `total`,`emp_salary`.`value` AS `sader`, 0 AS `payWay`, 1 AS `BrId`, `emp_salary`.`created_by` AS `created_by`, 0 AS outBox, 0 AS `disscount` 
        from  `emp_salary` join  `employee` where (( `emp_salary`.`employee` =  `employee`.`id` and emp_salary.type=3))
        union select  `transfer`.`at` AS `at`,concat('نقل من الخزينة -', `branches`.`name`, ' - ',`transfer`.`why`) AS `description`, 
        0 AS `wared`, `transfer`.`value` AS `sader` , 0 AS `payWay`, `transfer`.`fromBr` AS `BrId`, `transfer`.`user_insert` AS `user_insert`, 0 AS outBox, 0 AS `disscount`
        from  `transfer` join  `branches` where  ( `transfer`.`fromBr` =  `branches`.`id`)
        union select  `transfer`.`at` AS `at`,concat('نقل إلى الخزينة -', `branches`.`name`, ' - ',`transfer`.`why`) AS `description`, 
        `transfer`.`value` AS `wared`, 0 AS `sader`, 0 AS `payWay`, `transfer`.`toBr` AS `BrId`, `transfer`.`user_insert` AS `user_insert`, 0 AS outBox, 0 AS `disscount`
        from  `transfer` join  `branches` where  ( `transfer`.`toBr` =  `branches`.`id`)
        "); 
         
     }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_185333_ftran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_185333_ftran cannot be reverted.\n";

        return false;
    }
    */
}
