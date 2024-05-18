<?php

use yii\db\Migration;

/**
 * Class m230625_110607_v_customsOffice
 */
class m230625_110607_v_customsOffice extends Migration
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

        VIEW `v_customsOffice` AS      
        select `a`.`id` AS `id`,`a`.`customsOffice` AS `customsOffice`,0 AS `paid`,`a`.`value` AS `value`,
        `a`.`currancy` AS `currancy`,`a`.`notes` AS `notes`,`a`.`at` AS `at`,`b`.`phone` AS `phone`,
        `b`.`name` AS `name` 
        from  `shipmentData` `a` join  `customsDeclaration` `b` 
        where (`a`.`customsOffice` = `b`.`id`) 
        union 
        select `d`.`id` AS `id`,`d`.`customId` AS `customId`,`d`.`value` AS `paids`,0 AS `value`,
        `d`.`currancy` AS `currancy`,`d`.`why` AS `why`,`d`.`at` AS `at`,`c`.`phone` AS `phone`,
        `c`.`name` AS `name` from  `customsDeclaration` `c` join  `customsOffice` `d` 
        where (`c`.`id` = `d`.`customId`) order by `at`,`id`"
    );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230625_110607_v_customsOffice cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230625_110607_v_customsOffice cannot be reverted.\n";

        return false;
    }
    */
}
