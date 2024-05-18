<?php

use yii\db\Migration;

/**
 * Class m210126_064614_transfer_items
 */
class m210126_064614_transfer_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('transferItems', [
            'id' =>$this->primaryKey(),
            'fromBranch' =>$this->integer()->notNull(),
            'toBranch' =>$this->integer()->notNull(),
            'at' =>$this->date()->notNull(),
            'user_insert' =>$this->integer()->notNull(),
            'created_at'=>$this->date()->notNull(),
            'user_update' =>$this->integer(),
            'update_at'=>$this->date(),

            'FOREIGN KEY ([[user_insert]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_update]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[fromBranch]]) REFERENCES branches ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[toBranch]]) REFERENCES branches ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('transferItems');
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
