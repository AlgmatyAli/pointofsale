<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TempInvoicePurchase;

/**
 * app\models\TempInvoicePurchaseSearch represents the model behind the search form about `app\models\TempInvoicePurchase`.
 */
 class TempInvoicePurchaseSearch extends TempInvoicePurchase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category', 'box', 'created_by', 'updated_by'], 'integer'],
            [['quantity', 'costPrice', 'costTotal', 'state', 'salePrice', 'salePrice_', 'salePrice_2', 'salePrice_3'], 'number'],
            [['expire', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TempInvoicePurchase::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>[
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]],
                'pagination' => [ 'pageSize' => 35 ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'category' => $this->category,
            'quantity' => $this->quantity,
            'costPrice' => $this->costPrice,
            'costTotal' => $this->costTotal,
            'state' => $this->state,
            'salePrice' => $this->salePrice,
            'salePrice_' => $this->salePrice_,
            'salePrice_2' => $this->salePrice_2,
            'salePrice_3' => $this->salePrice_3,
            'box' => $this->box,
            'expire' => $this->expire,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
