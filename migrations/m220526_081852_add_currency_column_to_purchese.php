<?php

use yii\db\Migration;

/**
 * Class m220526_081852_add_currency_column_to_purchese
 */
class m220526_081852_add_currency_column_to_purchese extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('purchases', 'currancy' , $this->integer());
        $this->addColumn( 'purchases','total_currancy', $this->decimal(10,3));

        // $this->createIndex(
        //     'idx-currancy-purchases',
        //     'currancy',
        //     'purchases'
        // );

        // // add foreign key for table `basicInfo ` 
        // $this->addForeignKey(
        //     'fk-currancy-purchases',
        //     'currancy',
        //     'purchases',
        //     'purchases',
        //     'id',
        //     'CASCADE'
        // );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('currancy', 'purchases');
        $this->dropColumn('total_currancy', 'purchases');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220526_081852_add_currency_column_to_purchese cannot be reverted.\n";

        return false;
    }
    */
}
