<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Safe;
use Yii;
/**
 * SafeSearch represents the model behind the search form of `app\models\Safe`.
 */
class SafeSearch extends Safe
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'branch', 'type', 'user_insert', 'user_update', 'safeNo'], 'integer'],
            [['value'], 'number'],
            [['at', 'why', 'created_at', 'update_at'], 'safe'],
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
        $query = Safe::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>[
                'defaultOrder' => [
                    'at' => SORT_DESC
                ]],
            'pagination' => [ 'pageSize' => 200 ],

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
            'branch' => Yii::$app->user->identity->branch,
            'value' => $this->value,
            //'at' => $this->at,
            'type' => $this->type,
            'user_insert' => $this->user_insert,
            'created_at' => $this->created_at,
            'user_update' => $this->user_update,
            'update_at' => $this->update_at,
        ]);
        
        $query->andFilterWhere(['like', 'safeNo', $this->safeNo])
              ->andFilterWhere(['like', 'why', $this->why]);

        if(!empty($this->at) && strpos($this->at, '-') !== false) {
            list($min_date, $max_date) = explode(' - ', $this->at);
        $query->andFilterWhere(['between', 'at', $min_date, $max_date]);
        
        }

        return $dataProvider;
    }
}
