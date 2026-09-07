```php
<?php

use yii\db\Migration;

class m999999_210106_ftran_with_payWayType extends Migration
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
            VIEW `ftran` AS

            SELECT
                `sales`.`at` AS `date_`,
                CONVERT(
                    CONCAT(
                        'فاتورة مبيعات رقم -',
                        `sales`.`billId`,
                        ' - ',
                        `client`.`name`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                (`sales`.`total` - `sales`.`disscount`) AS `wared`,
                0 AS `sader`,
                `sales`.`payWay` AS `payWay`,
                `sales`.`branch` AS `branch`,
                `sales`.`user_insert` AS `user_insert`,
                0 AS `outBox`,
                `sales`.`disscount` AS `disscount`,
                `sales`.`currancy` AS `currancy`,
                `sales`.`payment_type` AS `payment_type`
            FROM `sales`
            JOIN `client`
            WHERE
                `sales`.`type` = 1
                AND `sales`.`payWay` IN (0)
                AND `sales`.`clinet` = `client`.`id`

            UNION

            SELECT
                `sales`.`at` AS `date_`,
                CONVERT(
                    CONCAT(
                        'دفعة على فاتورة مبيعات رقم -',
                        `sales`.`billId`,
                        ' - ',
                        `client`.`name`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                (`sales`.`total` - `sales`.`disscount`) AS `wared`,
                0 AS `sader`,
                `sales`.`payWay` AS `payWay`,
                `sales`.`branch` AS `branch`,
                `sales`.`user_insert` AS `user_insert`,
                0 AS `outBox`,
                `sales`.`disscount` AS `disscount`,
                `sales`.`currancy` AS `currancy`,
                `sales`.`payment_type` AS `payment_type`
            FROM `sales`
            JOIN `client`
            WHERE
                `sales`.`type` = 1
                AND `sales`.`payWay` IN (2)
                AND `sales`.`clinet` = `client`.`id`

            UNION

            SELECT
                `purchases`.`at` AS `at`,
                CONVERT(
                    CONCAT(
                        'فاتورة مشتريات رقم -',
                        `purchases`.`billId`,
                        ' - ',
                        `client`.`name`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                0 AS `wared`,
                `purchases`.`total` AS `sader`,
                `purchases`.`payWay` AS `payWay`,
                `purchases`.`branch` AS `branch`,
                `purchases`.`user_insert` AS `user_insert`,
                0 AS `outBox`,
                0 AS `disscount`,
                `purchases`.`currancy` AS `currancy`,
                `purchases`.`payment_type` AS `payment_type`
            FROM `purchases`
            JOIN `client`
            WHERE
                `purchases`.`type` = 1
                AND `purchases`.`payWay` IN (0)
                AND `purchases`.`clinet` = `client`.`id`

            UNION

            SELECT
                `purchases`.`at` AS `at`,
                CONVERT(
                    CONCAT(
                        'دفعة على فاتورة مشتريات رقم -',
                        `purchases`.`billId`,
                        ' - ',
                        `client`.`name`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                0 AS `wared`,
                `purchases`.`paid` AS `sader`,
                `purchases`.`payWay` AS `payWay`,
                `purchases`.`branch` AS `branch`,
                `purchases`.`user_insert` AS `user_insert`,
                0 AS `outBox`,
                0 AS `disscount`,
                `purchases`.`currancy` AS `currancy`,
                `purchases`.`payment_type` AS `payment_type`
            FROM `purchases`
            JOIN `client`
            WHERE
                `purchases`.`type` = 1
                AND `purchases`.`payWay` IN (2)
                AND `purchases`.`clinet` = `client`.`id`

            UNION

            SELECT
                `receipt`.`at` AS `at`,
                CONVERT(
                    CONCAT(
                        ' ايصال قبض رقم -',
                        `receipt`.`rId`,
                        ' - ',
                        `client`.`name`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                `receipt`.`value` AS `wared`,
                0 AS `sader`,
                NULL AS `payWay`,
                `receipt`.`branch` AS `branch`,
                `receipt`.`user_insert` AS `user_insert`,
                0 AS `outBox`,
                0 AS `disscount`,
                `receipt`.`currancy` AS `currancy`,
                `receipt`.`payment_type` AS `payment_type`
            FROM `receipt`
            JOIN `client`
            WHERE
                `receipt`.`type` = 1
                AND `receipt`.`clinet` = `client`.`id`

            UNION

            SELECT
                `receipt`.`at` AS `at`,
                CONVERT(
                    CONCAT(
                        'ايصال صرف رقم -',
                        `receipt`.`rId`,
                        ' - ',
                        `client`.`name`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                0 AS `wared`,
                `receipt`.`value` AS `sader`,
                NULL AS `payWay`,
                `receipt`.`branch` AS `branch`,
                `receipt`.`user_insert` AS `user_insert`,
                0 AS `outBox`,
                0 AS `disscount`,
                `receipt`.`currancy` AS `currancy`,
                `receipt`.`payment_type` AS `payment_type`
            FROM `receipt`
            JOIN `client`
            WHERE
                `receipt`.`type` = 2
                AND `receipt`.`clinet` = `client`.`id`

            UNION

            SELECT
                `expenses`.`at` AS `at`,
                CONVERT(
                    CONCAT(
                        'مصروفات نقدية رقم -',
                        `expenses`.`id`,
                        ' - ',
                        `items`.`name`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                0 AS `wared`,
                `expenses`.`value` AS `sader`,
                NULL AS `payWay`,
                `expenses`.`branch` AS `branch`,
                `expenses`.`user_insert` AS `user_insert`,
                `expenses`.`outBox` AS `outBox`,
                0 AS `disscount`,
                `expenses`.`currancy` AS `currancy`,
                `expenses`.`payment_type` AS `payment_type`
            FROM `expenses`
            JOIN `items`
            WHERE
                `expenses`.`itemId` = `items`.`id`

            UNION

            SELECT
                `sales`.`at` AS `date_`,
                CONVERT(
                    CONCAT(
                        'فاتورة مسترجع مبيعات رقم -',
                        `sales`.`billId`,
                        ' - ',
                        `client`.`name`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                0 AS `wared`,
                `sales`.`total` AS `sader`,
                `sales`.`payWay` AS `payWay`,
                `sales`.`branch` AS `branch`,
                `sales`.`user_insert` AS `user_insert`,
                0 AS `outBox`,
                0 AS `disscount`,
                `sales`.`currancy` AS `currancy`,
                `sales`.`payment_type` AS `payment_type`
            FROM `sales`
            JOIN `client`
            WHERE
                `sales`.`type` = 2
                AND `sales`.`payWay` = 1
                AND `sales`.`clinet` = `client`.`id`

            UNION

            SELECT
                `purchases`.`at` AS `at`,
                CONVERT(
                    CONCAT(
                        'فاتورة مشتريات رقم -',
                        `purchases`.`billId`,
                        ' - ',
                        `client`.`name`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                `purchases`.`total` AS `wared`,
                0 AS `sader`,
                `purchases`.`payWay` AS `payWay`,
                `purchases`.`branch` AS `branch`,
                `purchases`.`user_insert` AS `user_insert`,
                0 AS `outBox`,
                0 AS `disscount`,
                `purchases`.`currancy` AS `currancy`,
                `purchases`.`payment_type` AS `payment_type`
            FROM `purchases`
            JOIN `client`
            WHERE
                `purchases`.`type` = 2
                AND `purchases`.`payWay` = 1
                AND `purchases`.`clinet` = `client`.`id`

            UNION

            SELECT
                `emp_salary`.`at` AS `at`,
                CONVERT(
                    CONCAT(
                        'دفعة على الحساب من المرتب -',
                        `employee`.`name`,
                        ' - ',
                        `emp_salary`.`why`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                0 AS `wared`,
                `emp_salary`.`value` AS `sader`,
                0 AS `payWay`,
                1 AS `branch`,
                `emp_salary`.`created_by` AS `user_insert`,
                0 AS `outBox`,
                0 AS `disscount`,
                `emp_salary`.`currancy` AS `currancy`,
                1 AS `payment_type`
            FROM `emp_salary`
            JOIN `employee`
            WHERE
                `emp_salary`.`employee` = `employee`.`id`
                AND `emp_salary`.`type` = 1

            UNION

            SELECT
                `emp_salary`.`at` AS `at`,
                CONVERT(
                    CONCAT(
                        'دفعة اضافي -',
                        `employee`.`name`,
                        ' - ',
                        `emp_salary`.`why`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                0 AS `wared`,
                `emp_salary`.`value` AS `sader`,
                0 AS `payWay`,
                1 AS `branch`,
                `emp_salary`.`created_by` AS `user_insert`,
                0 AS `outBox`,
                0 AS `disscount`,
                `emp_salary`.`currancy` AS `currancy`,
                1 AS `payment_type`
            FROM `emp_salary`
            JOIN `employee`
            WHERE
                `emp_salary`.`employee` = `employee`.`id`
                AND `emp_salary`.`type` = 3

            UNION

            SELECT
                `transfer`.`at` AS `at`,
                CONVERT(
                    CONCAT(
                        'نقل من الخزينة -',
                        `branches`.`name`,
                        ' - ',
                        `transfer`.`why`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                0 AS `wared`,
                `transfer`.`value` AS `sader`,
                0 AS `payWay`,
                `transfer`.`fromBr` AS `branch`,
                `transfer`.`user_insert` AS `user_insert`,
                0 AS `outBox`,
                0 AS `disscount`,
                1 AS `currancy`,
                1 AS `payment_type`
            FROM `transfer`
            JOIN `branches`
            WHERE
                `transfer`.`fromBr` = `branches`.`id`

            UNION

            SELECT
                `transfer`.`at` AS `at`,
                CONVERT(
                    CONCAT(
                        'نقل إلى الخزينة -',
                        `branches`.`name`,
                        ' - ',
                        `transfer`.`why`
                    ) USING utf8mb4
                ) COLLATE utf8mb4_general_ci AS `description`,
                `transfer`.`value` AS `wared`,
                0 AS `sader`,
                0 AS `payWay`,
                `transfer`.`toBr` AS `branch`,
                `transfer`.`user_insert` AS `user_insert`,
                0 AS `outBox`,
                0 AS `disscount`,
                1 AS `currancy`,
                1 AS `payment_type`
            FROM `transfer`
            JOIN `branches`
            WHERE
                `transfer`.`toBr` = `branches`.`id`
            "
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m999999_210106_ftran_with_payWayType cannot be reverted.\n";

        return false;
    }
}
