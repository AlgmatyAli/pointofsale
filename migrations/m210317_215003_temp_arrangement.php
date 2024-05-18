<?php

use yii\db\Migration;

/**
 * Class m210317_215003_temp_arrangement
 */
class m210317_215003_temp_arrangement extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('temp_arrangement', [
            'id' =>$this->primaryKey(),
            'category' =>$this->integer()->notNull(),
            'quantity'=>$this->float()->notNull(),
            'box' =>$this->integer(),
            'type' =>$this->integer()->notNull(),
            'state' =>$this->integer(),
            'expire' =>$this->date(),
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
        echo "m210317_215003_temp_arrangement cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210317_215003_arrangement cannot be reverted.\n";

        return false;
    }
    */
}
