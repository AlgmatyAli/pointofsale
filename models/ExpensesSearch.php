<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Expenses;
use Yii;

/**
 * ExpensesSearch represents the model behind the search form of `app\models\Expenses`.
 */
class ExpensesSearch extends Expenses
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'itemId', 'user_insert', 'user_update', 'branch', 'outBox'], 'integer'],
            [['expenseTo', 'at', 'why', 'created_at', 'update_at', 'payment_type'], 'safe'],
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
        $query = Expenses::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
            'pagination' => ['pageSize' => 70],
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
            'payment_type' => $this->payment_type,
            'itemId' => $this->itemId,
            'value' => $this->value,
            'user_insert' => $this->user_insert,
            'created_at' => $this->created_at,
            'user_update' => $this->user_update,
            'update_at' => $this->update_at,
            'outBox' => $this->outBox,
            'branch' => Yii::$app->user->identity->branch,
        ]);

        $query->andFilterWhere(['like', 'expenseTo', $this->expenseTo])
            ->andFilterWhere(['like', 'why', $this->why]);

        if (!empty($this->at) && strpos($this->at, '-') !== false) {
            list($min_date, $max_date) = explode(' - ', $this->at);
            $query->andFilterWhere(['between', 'at', $min_date, $max_date]);
        }

        return $dataProvider;
    }
}
