<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SalesDetails;

/**
 * app\models\SalesDetailsSearch represents the model behind the search form about `app\models\SalesDetails`.
 */
 class SalesDetailsSearch extends SalesDetails
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'salesId', 'category', 'type', 'box'], 'integer'],
            [['serial_number', 'mac_address', 'expire', 'packing', 'waitQnty'], 'safe'],
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
        $query = SalesDetails::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>[
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]],
            'pagination' => [ 'pageSize' => 200 ],
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
            ->andFilterWhere(['like', 'mac_address', $this->mac_address])
            ->andFilterWhere(['=', 'waitQnty', $this->waitQnty]);

        return $dataProvider;
    }
}
