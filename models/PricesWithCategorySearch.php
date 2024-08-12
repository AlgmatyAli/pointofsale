<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Prices;

/**
 * PricesSearch represents the model behind the search form of `app\models\Prices`.
 */
class PricesWithCategorySearch extends Prices
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category'], 'integer'],
            [['serialNo', 'quantity', 'company'], 'safe'],
            [['costPrice', 'minPrice', 'minPrice2', 'minPrice3', 'maxPrice'], 'number'],
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
        $query = Prices::find()
        //>select(['category.id as id', 'category.name as name', 'costPrice', 'minPrice', 'minPrice2', 'minPrice3', 'maxPrice', 'stocks.quantity'])
        ->joinwith(['category0'])
        ->joinwith(['stocks0']);
        //->where('costPrice<>0');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 100 ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
           // 'id' => $this->id,
           // 'category' => $this->category,
            'costPrice' => $this->costPrice,
            'minPrice' => $this->minPrice,
            'minPrice2' => $this->minPrice2,
            'minPrice3' => $this->minPrice3,
            'maxPrice' => $this->maxPrice,
        ]);
        $query->andFilterWhere(['=', 'category.serialNo', $this->serialNo]);
        $query->andFilterWhere(['=', 'category.company', $this->company]);
        $query->andFilterWhere(['=', 'prices.category', $this->category]);
        $query->andFilterWhere(['<>', 'stocks.quantity', 0]);
        return $dataProvider;
    }
}
