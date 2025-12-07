<?php

use yii\db\Schema;

class m250427_090102_category extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'class' => $this->string(255)->notNull(),
            'unit' => $this->string(100)->notNull(),
            'box' => $this->integer(11)->notNull(),
            'cost' => $this->decimal(9, 3)->notNull(),
            'price' => $this->decimal(9, 3)->notNull(),
            'quantity' => $this->float()->notNull(),
            'minimum' => $this->integer(11)->notNull(),
            'ending' => $this->integer(11)->notNull(),
            'qShow' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->notNull(),
            'place' => $this->text(),
            'serialNo' => $this->string(255),
            'country' => $this->string(255),
            'company' => $this->string(255),
            'path' => $this->string(255),
            'commCode' => $this->text(),
            'moreRequest' => $this->integer(11),
            'user_insert' => $this->integer(11)->notNull(),
            'created_at' => $this->date()->notNull(),
            'user_update' => $this->integer(11),
            'update_at' => $this->date(),
            'weight' => $this->decimal(10, 3),
            'FOREIGN KEY ([[user_insert]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_update]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->execute(
            " CREATE TRIGGER `insertPrices` AFTER INSERT ON `category`
                    FOR EACH ROW INSERT INTO prices(category, costPrice, minPrice, maxPrice, minPrice2, minPrice3)
                    VALUES(new.id, new.cost, new.price, new.price, 0,0)"
        );

        $this->execute(
            " CREATE TRIGGER `insertStocks` AFTER INSERT ON `category`
                    FOR EACH ROW 
                    BEGIN
                        DECLARE branch_id INT;
                        DECLARE done INT DEFAULT 0;
                        DECLARE branch_cursor CURSOR FOR SELECT id FROM branches;
                        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

                        OPEN branch_cursor;

                        read_loop: LOOP
                            FETCH branch_cursor INTO branch_id;
                            IF done THEN
                                LEAVE read_loop;
                            END IF;
                            INSERT INTO stocks(category, quantity, branch, type)
                            VALUES (NEW.id, NEW.quantity, branch_id, 1);
                        END LOOP;

                        CLOSE branch_cursor;
                    END"
        );
    }

    public function down()
    {
        $this->dropTable('category');
    }
}
