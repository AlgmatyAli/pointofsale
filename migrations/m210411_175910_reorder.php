<?php

use yii\db\Migration;

/**
 * Class m210411_175910_reorder
 */
class m210411_175910_reorder extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('reorder', [
            'id' =>$this->primaryKey(),
            'at' =>$this->date(),
            'branch' =>$this->integer()->notNull(),
            'created_by' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_by' => $this->integer(11),
            'updated_at' => $this->datetime(),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[updated_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[branch]]) REFERENCES branches ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210411_175910_reorder cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210411_175910_reorder cannot be reverted.\n";

        return false;
    }
    */
}
