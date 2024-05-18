<?php

use yii\db\Migration;

/**
 * Class m221226_182148_total_inventory
 */
class m999999_182148_total_inventory extends Migration
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
        VIEW `Totalinventory` AS
        
        select max( inventory.company ) AS  company , inventory.id  AS  id , max( inventory.type ) AS  type ,
        max( inventory.name ) AS  name ,sum( inventory.quantity ) AS  quantity ,max( inventory.unit ) AS  unit ,
        max( inventory.serialNo ) AS  serialNo ,max( inventory.box ) AS  box ,max( inventory.class ) AS  class ,
        max( inventory.branch ) AS  branch ,max(prices.costPrice)  AS  costPrice, 
        max(prices.maxPrice)  AS  maxPrice, max(prices.minPrice)  AS  minPrice, 
        max(prices.minPrice2)  AS  minPrice2, max(prices.minPrice3)  AS  minPrice3,  max( inventory.place ) AS  place,  max( inventory.commCode) AS commCode
        from ( inventory  
        left join  prices  on(( inventory.id  =  prices.category ))) 
        where (( inventory.type  in (1,2))) group by  inventory.id, inventory.branch 
        having (sum( inventory.quantity ) > 0) 
        UNION
        select max( inventory.company ) AS  company , inventory.id  AS  id , inventory.type  AS  type ,
        max( inventory.name ) AS  name ,sum( inventory.quantity ) AS  quantity ,max( inventory.unit ) AS  unit ,
        max( inventory.serialNo ) AS  serialNo ,max( inventory.box ) AS  box ,max( inventory.class ) AS  class ,
        max( inventory.branch ) AS  branch ,max(prices.costPrice)  AS  costPrice, 
        max(prices.maxPrice)  AS  maxPrice, max(prices.minPrice)  AS  minPrice, 
        max(prices.minPrice2)  AS  minPrice2, max(prices.minPrice3)  AS  minPrice3,  max( inventory.place ) AS  place,  max( inventory.commCode) AS commCode
        from ( inventory  
        left join  prices  on(( inventory.id  =  prices.category ))) 
        where (( inventory.type  in (3))) group by  inventory.id , inventory.type  
        having (sum( inventory.quantity ) > 0) "
    );
    }
    // CREATE OR REPLACE 
    // ALGORITHM = UNDEFINED 
    // SQL SECURITY DEFINER                        
    // VIEW `Totalinventory` AS
    
    // select max( inventory.company ) AS  company , inventory.id  AS  id , inventory.type  AS  type ,
    // max( inventory.name ) AS  name ,sum( inventory.quantity ) AS  quantity ,max( inventory.unit ) AS  unit ,
    // max( inventory.serialNo ) AS  serialNo ,max( inventory.box ) AS  box ,max( inventory.class ) AS  class ,
    // max( inventory.branch ) AS  branch ,max(prices.costPrice)  AS  costPrice, 
    // max(prices.maxPrice)  AS  maxPrice, max(prices.minPrice)  AS  minPrice, 
    // max(prices.minPrice2)  AS  minPrice2, max(prices.minPrice3)  AS  minPrice3  
    // from ( inventory  
    // left join  prices  on(( inventory.id  =  prices.category ))) 
    // where (( inventory.type  in (1,3))) group by  inventory.id , inventory.type  
    // having (sum( inventory.quantity ) > 0) order by  inventory.id
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201226_182148_total_inventory cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201226_182148_total_inventory cannot be reverted.\n";

        return false;
    }
    */
}
