<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TempInvoice;

/**
 * app\models\TempInvoiceSearch represents the model behind the search form about `app\models\TempInvoice`.
 */
 class TempInvoiceSearch extends TempInvoice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'invoice_number', 'category', 'box', 'state', 'created_by', 'updated_by', 'cat', 'kind'], 'integer'],
            [['serial_number', 'expire', 'created_at', 'updated_at'], 'safe'],
            [['quantity', 'costPrice', 'salePrice'], 'number'],
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
        $query = TempInvoice::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>[
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]],
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
            'invoice_number' => $this->invoice_number,
            'category' => $this->category,
            'quantity' => $this->quantity,
            'costPrice' => $this->costPrice,
            'salePrice' => $this->salePrice,
            'box' => $this->box,
            'state' => $this->state,
            'expire' => $this->expire,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'serial_number', $this->serial_number]);

        return $dataProvider;
    }
}
