<?php

use yii\db\Migration;

/**
 * Class m220526_081852_add_currency_column_to_purchese
 */
class m220526_081852_add_createdBy_column_to_currancy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('currancy', 'created_by' , $this->integer());
        $this->addColumn( 'currancy','updated_by', $this->integer());

        $this->createIndex(
            'idx-currancy-updated_by',
            'currancy',
            'updated_by'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-currancy-updated_by',
            'currancy',
            'updated_by',
            'user',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-currancy-created_by',
            'currancy',
            'created_by'
        );

        // add foreign key for table `basicInfo ` 
        $this->addForeignKey(
            'fk-currancy-created_by',
            'currancy',
            'created_by',
            'user',
            'id',
            'CASCADE'
        );
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
