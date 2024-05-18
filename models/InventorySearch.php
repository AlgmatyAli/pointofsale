<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Inventory;

/**
 * app\models\InventorySearch represents the model behind the search form about `app\models\Inventory`.
 */
 class InventorySearch extends Inventory
{
    /**
     * @inheritdoc
     */
    Public $costPrice,$maxPrice,$minPrice;
    public function rules()
    {
        return [
            [['name', 'unit', 'class', 'costPrice' , 'maxPrice' , 'minPrice' , 'serialNo', 'commCode', 'company'], 'safe'],
            [['id', 'box', 'branch'], 'integer'],
            [['quantity'], 'number'],
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
        //max(prices.maxPrice) as maxPrice, max(prices.minPrice) as minPrice, max(prices.costPrice) as costPrice'
        // $query = Inventory::find()
        // ->select('inventory.id, max(inventory.name) as name, sum(inventory.quantity) as quantity, max(inventory.unit) as unit, 
        // max(inventory.box) as box, max(inventory.class) as class, max(inventory.serialNo) as serialNo, branch, max(inventory.commCode) as commCode')
        // ->leftJoin('prices', 'inventory.id = prices.category')
        // ->groupBy('inventory.id, inventory.branch')
        // ->having('sum(inventory.quantity) <>0');
        $query = Stocks::find()
        ->select(' category.id, max(category.name) as name, sum(stocks.quantity) as quantity, max(category.unit) as unit, 
        max(category.box) as box, max(category.class) as class, max(category.serialNo) as serialNo, branch, max(category.commCode) as commCode, max(maxPrice) as maxPrice')
        ->leftJoin('category', 'category.id = stocks.category')
        ->leftJoin('prices', 'category.id = prices.category')
        ->groupBy('stocks.category, stocks.branch')
        ->having('sum(stocks.quantity) <>0');
         
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>[
                'defaultOrder' => ['id' => SORT_ASC]],
                'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'inventory.id' => $this->id,
            'quantity' => $this->quantity,
            'box' => $this->box,
            'inventory.branch' => $this->branch,
            'serialNo'=> $this->serialNo,
            'commCode'=> $this->commCode,
            'type'=> [1,2],
        ]);

        $query->andFilterWhere(['like', 'inventory.name', $this->name])
            ->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['like', 'company', $this->company])
            ->andFilterWhere(['=', 'prices.costPrice', $this->costPrice])
            ->andFilterWhere(['=', 'prices.minPrice', $this->minPrice])
            ->andFilterWhere(['=', 'prices.maxPrice', $this->maxPrice]);

        return $dataProvider;
    }
}
