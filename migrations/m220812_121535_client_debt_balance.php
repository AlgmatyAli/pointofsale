<?php

use yii\db\Migration;

/**
 * Class m220812_121535_client_debt_balance
 */
class m220812_121535_client_debt_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
          $auth = Yii::$app->authManager;

         $this->addColumn('client', 'debt', $this->decimal(10,3)->notnull());
         
          $userCansellOverDebt = $auth->createPermission('userCansellOverDebt');
          $userCansellOverDebt->description = 'امكانية البيع في حالة تجاوز سقف الدين';
          $auth->add($userCansellOverDebt);

          $userCanUpdateSalePriceAfterSave = $auth->createPermission('userCanUpdateSalePriceAfterSave');
          $userCanUpdateSalePriceAfterSave->description = 'امكانية تعديل سعر البيع بعد حفظ الفاتورة';
          $auth->add($userCanUpdateSalePriceAfterSave);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220812_121535_client_debt_balance cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220812_121535_client_debt_balance cannot be reverted.\n";

        return false;
    }
    */
}
