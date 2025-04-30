<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sales;
use Yii;
/**
 * SalesSearch represents the model behind the search form of `app\models\Sales`.
 */
class SalesSearch extends Sales
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'billId', 'clinet', 'payWay', 'branch', 'type', 'carpenter', 'upholstered','agent', 'paintId',
             'deleviryId', 'user_insert', 'user_update', 'currancy'], 'safe'],
            [['at', 'notes', 'path', 'deleviryAt', 'created_at', 'update_at', 'phone','agent', 'deleviried', 
            'deserving', 'wholesale'], 'safe'],
            [['total', 'paid', 'disscount'], 'safe'],
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
        
        $query = Sales::find();
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
            // $query->where('sales.branch='.Yii::$app->user->identity->branch);
            // $query->where('id<=1');
           // $dataProvider->query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'sales.id' => $this->id,
            'billId' => $this->billId,
            'payWay' => $this->payWay,
            'branch' => $this->branch,
            'total' => $this->total,
            'paid' => $this->paid,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'user_update' => $this->user_update,
            'update_at' => $this->update_at,
            'deleviried' => $this->deleviried,
            'wholesale' => $this->wholesale,
            'currancy' => $this->currancy,
        ]);

        $query->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'path', $this->path])
            ->andFilterWhere(['=', 'billId', $this->billId])
            ->andFilterWhere(['=', 'client.phone', $this->phone]);

             if(Yii::$app->user->identity->client != null){
                if (Yii::$app->user->can('userCanSeeOtherUsersSales')){
                    $query->andFilterWhere(['in', 'clinet', explode(',' ,Yii::$app->user->identity->client)]);
                  }else{
                    $query->andFilterWhere(['in', 'clinet', explode(',' ,Yii::$app->user->identity->client)]);
                  }
             }else{
                $query->andFilterWhere(['=', 'clinet', $this->clinet]);
             }

              if (Yii::$app->user->can('userCanSeeOtherUsersSales')){
                $query->andFilterWhere(['=', 'user_insert', $this->user_insert]);
              }else{
                $query->andFilterWhere(['=', 'user_insert', Yii::$app->user->identity->id]);
              }

            if(!empty($this->at) && strpos($this->at, '-') !== false) {
                list($min_date, $max_date) = explode(' - ', $this->at);
            $query->andFilterWhere(['between', 'at', $min_date, $max_date]);
            
            }
            
            if(!empty($this->deleviryAt) && strpos($this->deleviryAt, '-') !== false) {
                list($min_date, $max_date) = explode(' - ', $this->deleviryAt);
            $query->andFilterWhere(['between', 'deleviryAt', $min_date, $max_date]);
            
            }
           
        return $dataProvider;
    }
}