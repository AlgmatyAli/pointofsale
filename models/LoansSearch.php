<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Loans;

/**
 * LoansSearch represents the model behind the search form of `app\models\Loans`.
 */
class LoansSearch extends Loans
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'employee', 'parts', 'paid', 'status', 'created_by', 'updated_by'], 'integer'],
            [['loanValue', 'kestValue'], 'number'],
            [['at', 'notes', 'created_at', 'updated_at', 'emp_name'], 'safe'],
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
        $query = Loans::find()
        ->joinWith('employee0');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'employee' => $this->employee,
            'loanValue' => $this->loanValue,
            'kestValue' => $this->kestValue,
            'at' => $this->at,
            'parts' => $this->parts,
            'paid' => $this->paid,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);
        $query->andFilterWhere(['like', 'employee.name', $this->emp_name]);
        $query->andFilterWhere(['like', 'notes', $this->notes]);

        return $dataProvider;
    }
}
