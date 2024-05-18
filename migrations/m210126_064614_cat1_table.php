<?php

use yii\db\Migration;

/**
 * Class m210126_064614_transfer_items
 */
class m210126_064614_cat1_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cat', [
            'id' =>$this->primaryKey(),
            'name' =>$this->text(),
         
            'created_by' =>$this->integer()->notNull(),
            'created_at'=>$this->date()->notNull(),
            'updated_by' =>$this->integer(),
            'update_at'=>$this->date(),

            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('cat');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210126_064614_transfer_items cannot be reverted.\n";

        return false;
    }
    */
}
