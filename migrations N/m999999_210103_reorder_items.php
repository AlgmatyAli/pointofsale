<?php

use yii\db\Migration;

/**
 * Class m999999_210103_reorder_items
 */
class m999999_210103_reorder_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("create or replace view reorderItems as SELECT `category`.`id`, 
        max(category.name) AS `name`, max(category.serialNo) AS `serialNo`, 
        max(category.minimum) AS `minimum`, 
        sum(stocks.quantity) AS `quantity`, max(category.class) as class, MAX(category.company) as company
        FROM `stocks` LEFT JOIN `category` ON category.id = stocks.category
        GROUP BY `stocks`.`id`, `category`.`minimum` 
        HAVING sum(stocks.quantity) <= category.minimum");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m999999_210103_reorder_items cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220810_084414_reorder_items cannot be reverted.\n";

        return false;
    }
    */
}
