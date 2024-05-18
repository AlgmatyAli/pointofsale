<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Receipt;
use Yii;
/**
 * ReceiptSearch_ represents the model behind the search form of `app\models\Receipt`.
 */
class ReceiptSearch_ extends Receipt
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'rId', 'clinet', 'type', 'user_insert','agent' ,'user_update', 'branch'], 'safe'],
            [['at', 'why', 'payWay', 'created_at', 'update_at', 'agent','tafqet'], 'safe'],
            [['value'], 'number'],
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
        $query = Receipt::find();

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
            'rId' => $this->rId,
            'clinet' => $this->clinet,
           // 'at' => $this->at,
            'value' => $this->value,
            'type' =>  $this->type,
            'agent' => $this->agent,
            'user_insert' => $this->user_insert,
            'created_at' => $this->created_at,
            'user_update' => $this->user_update,
            'update_at' => $this->update_at,
            'branch' => Yii::$app->user->identity->branch,
        ]);

        $query->andFilterWhere(['like', 'why', $this->why])
            ->andFilterWhere(['like', 'payWay', $this->payWay]);

            if(!empty($this->at) && strpos($this->at, '-') !== false) {
                list($min_date, $max_date) = explode(' - ', $this->at);
            $query->andFilterWhere(['between', 'at', $min_date, $max_date]);
            
            }

        return $dataProvider;
    }
}
