<?php

use yii\db\Migration;

/**
 * Class m230725_064827_alter_purchases_table
 */
class m230725_064827_alter_purchases_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('shippingType',[
            'id' => $this->primaryKey(),
            'name' => $this->string(125)->notNull(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);

        $this->addColumn('purchases', 'shippingType', $this->integer()->after('total_currancy'));
        $this->addColumn('purchases', 'dateOfArrival', $this->date()->after('shippingType'));

         $this->insert('shippingType',array(
            'id'=>2,
            'name' => 'بري ',
            'created_by'=>'1',
            'created_at'=>date('Y-m-d')
            ));
            $this->insert('shippingType',array(
                'id'=>3,
                'name' => 'بحري ',
                'created_by'=>'1',
                'created_at'=>date('Y-m-d')
                ));
                $this->insert('shippingType',array(
                    'id'=>1,
                    'name' => 'جوي ',
                    'created_by'=>'1',
                    'created_at'=>date('Y-m-d')
                    ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230725_064827_alter_purchases_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230725_064827_alter_purchases_table cannot be reverted.\n";

        return false;
    }
    */
}
