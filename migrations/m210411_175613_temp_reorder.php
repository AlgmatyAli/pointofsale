<?php

use yii\db\Migration;

/**
 * Class m210411_175613_temp_reorder
 */
class m210411_175613_temp_reorder extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('temp_reorder', [
            'id' =>$this->primaryKey(),
            'category' =>$this->integer()->notNull(),
            'quantity'=>$this->float()->notNull(),
            'state' =>$this->integer(),
            'branch' =>$this->integer()->notNull(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[category]]) REFERENCES category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[branch]]) REFERENCES branches ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('temp_reordert');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210411_175613_temp_reorder cannot be reverted.\n";

        return false;
    }
    */
}
