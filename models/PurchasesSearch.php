<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Purchases;
use Yii;
/**
 * PurchasesSearch represents the model behind the search form of `app\models\Purchases`.
 */
class PurchasesSearch extends Purchases
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'billId', 'clinet','currancy','total_currancy', 'payWay', 'BuyFor', 'branch', 'type', 'user_insert', 'user_update'], 'integer'],
            [['at', 'clientBill', 'notes', 'path', 'created_at', 'update_at', 'shippingType', 'dateOfArrival'], 'safe'],
            [['total', 'paid'], 'number'],
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
        $query = Purchases::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>[
                'defaultOrder' => [
                    'id' => SORT_DESC
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
            'billId' => $this->billId,
            'clinet' => $this->clinet,
            //'at' => $this->at,
            'payWay' => $this->payWay,
            'BuyFor' => $this->BuyFor,
            'branch' => Yii::$app->user->identity->branch,
            'total' => $this->total,
            'paid' => $this->paid,
            'type' => $this->type,
            'user_insert' => $this->user_insert,
            'created_at' => $this->created_at,
            'user_update' => $this->user_update,
            'update_at' => $this->update_at,
            'currancy' => $this->currancy,
            'shippingType' => $this->shippingType,
            'total_currancy' => $this->total_currancy,
        ]);

        $query->andFilterWhere(['like', 'clientBill', $this->clientBill])
            ->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'path', $this->path]);

            if(!empty($this->dateOfArrival) && strpos($this->dateOfArrival, '-') !== false) {
                list($min_date, $max_date) = explode(' - ', $this->dateOfArrival);
            $query->andFilterWhere(['between', 'dateOfArrival', $min_date, $max_date]);
            }
            if(!empty($this->at) && strpos($this->at, '-') !== false) {
                list($min_date, $max_date) = explode(' - ', $this->at);
            $query->andFilterWhere(['between', 'at', $min_date, $max_date]);
            
            }

        return $dataProvider;
    }
}
