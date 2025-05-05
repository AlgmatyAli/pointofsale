<?php

use yii\db\Migration;

/**
 * Class m999999_075950_stagnant_view
 */
class m999999_075950_stagnant_view extends Migration
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
        VIEW `stagnant` AS 
        select  `sales`.`at` AS `at`,date_format( `sales`.`at`,'%Y') AS `year`,
         `salesDetails`.`salesId` AS `salesId`,
         `salesDetails`.`category` AS `category` 
        from ( `sales` 
        left join  `salesDetails` on(( `sales`.`id` =  `salesDetails`.`salesId`))) 
        order by  `sales`.`at`
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m999999_075950_stagnant_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m999999_075950_stagnant_view" cannot be reverted.\n";

        return false;
    }
    */
}
