<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Stocks;

/**
 * StocksSearch represents the model behind the search form of `app\models\Stocks`.
 */
class StocksSearch extends Stocks
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category'], 'integer'],
            [['quantity'], 'number'],
            [['company', 'class', 'serialNo', 'branch', 'costPrice', 'maxPrice'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Stocks::find()
        ->select('category.id, max(category.name) as name, sum(stocks.quantity) as quantity, sum(stocks.type) as type, max(category.unit) as unit, max(category.company) as company,
        max(category.box) as box, max(category.class) as class, max(category.serialNo) as serialNo, branch, max(category.commCode) as commCode
        ,max(maxPrice) as maxPrice, max(costPrice) as costPrice, max(minPrice) as minPrice'
        )
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

        // grid filtering conditions
        $query->andFilterWhere([
            'category.id' => $this->id,
            'stocks.category' => $this->category,
            'quantity' => $this->quantity,
            'company' => $this->company,
            'class' => $this->class,
            'serialNo' => $this->serialNo,
            'branch' => $this->branch,
            'costPrice' => $this->costPrice,
            'stocks.type' => 1,
        ]);

        return $dataProvider;
    }
}
