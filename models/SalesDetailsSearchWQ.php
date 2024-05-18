<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SalesDetails;

/**
 * app\models\SalesDetailsSearchWQ represents the model behind the search form about `app\models\SalesDetails`.
 */
 class SalesDetailsSearchWQ extends SalesDetails
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'salesId', 'category', 'type', 'box'], 'integer'],
            [['serial_number', 'mac_address', 'expire', 'packing', 'waitQnty', 'company', 'serialNo', 'commCode', 'class'], 'safe'],
            [['quantity', 'costPrice', 'salePrice', 'original_price'], 'number'],
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
        $query = SalesDetails::find()
        ->leftJoin('category' , 'salesDetails.category = category.id')
        ->select('salesDetails.category, max(category.name) as name, sum(salesDetails.waitQnty) as waitQnty, max(category.unit) as unit, 
        max(category.company) as company, max(category.box) as box, max(category.class) as class, max(category.serialNo) as serialNo, max(category.commCode) as commCode')
        ->groupBy('salesDetails.category')
        ->having('sum(salesDetails.waitQnty) <> 0');
         
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>[
                'defaultOrder' => ['category' => SORT_ASC]],
                'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'salesId' => $this->salesId,
            'category' => $this->category,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'costPrice' => $this->costPrice,
            'salePrice' => $this->salePrice,
            'original_price' => $this->original_price,
            'box' => $this->box,
            'expire' => $this->expire,
        ]);

        $query->andFilterWhere(['like', 'serial_number', $this->serial_number])
            ->andFilterWhere(['=', 'category.company', $this->company])
            ->andFilterWhere(['like', 'category.class', $this->class])
            ->andFilterWhere(['like', 'category.serialNo', $this->serialNo])
            ->andFilterWhere(['like', 'category.commCode', $this->commCode])
            ->andFilterWhere(['=', 'waitQnty', $this->waitQnty]);

        return $dataProvider;
    }
}
