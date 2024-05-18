<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PurchasesDetails;

/**
 * PurchasesDetailsSearch represents the model behind the search form of `app\models\PurchasesDetails`.
 */
class PurchasesDetailsSearch extends PurchasesDetails
{
    /**
     * {@inheritdoc}
     */
    public $clinet,$at;
    public function rules()
    {
        return [
            [['id', 'PurchasesId','clinet', 'category', 'quantity', 'box'], 'integer'],
            [['costPrice', 'salePrice'], 'number'],
            [['totalCost','at', 'expire'], 'safe'],
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
        $query = PurchasesDetails::find()
        ->joinWith(['purchases']) 
        ->joinWith(['client']) ;
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>[
                'defaultOrder' => [
                    'id' => SORT_ASC
                ]],
                'pagination' => [ 'pageSize' => 70 ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'purchases.clinet'=>$this->clinet,
            'PurchasesId' => $this->PurchasesId,
            'category' => $this->category,
            'quantity' => $this->quantity,
            'costPrice' => $this->costPrice,
            'salePrice' => $this->salePrice,
            'box' => $this->box,
            'expire' => $this->expire,
        ]);

        if(!empty($this->at) && strpos($this->at, '-') !== false) {
            list($min_date, $max_date) = explode(' - ', $this->at);
        $query->andFilterWhere(['between', 'at', $min_date, $max_date]);
        
        }

        $query->andFilterWhere(['=', 'totalCost', $this->totalCost]);

        return $dataProvider;
    }
}
