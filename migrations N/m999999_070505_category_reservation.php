<?php

use yii\db\Migration;

/**
 * Class m999999_070505_category_reservation
 */
class m999999_070505_category_reservation extends Migration
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
        VIEW `category_reservation` AS
        SELECT salesDetails.category, sum(salesDetails.quantity)quantity, max(salesDetails.salesId)salesId FROM sales
        JOIN salesDetails on sales.id = salesDetails.salesId
        where sales.deleviried = 0
        group by salesDetails.category
        order by salesDetails.category
            "); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m999999_070505_category_reservation cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211212_070505_category_reservation cannot be reverted.\n";

        return false;
    }
    */
}
