<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ArrangementDetails;

/**
 * ArrangementDetailsSearch represents the model behind the search form of `app\models\ArrangementDetails`.
 */
class ArrangementDetailsSearch extends ArrangementDetails
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'arrangement', 'category', 'box', 'type', 'stockTaking'], 'integer'],
            [['quantity'], 'number'],
            [['expire'], 'safe'],
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
        $query = ArrangementDetails::find();

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
            'arrangement' => $this->arrangement,
            'category' => $this->category,
            'quantity' => $this->quantity,
            'box' => $this->box,
            'type' => $this->type,
            'expire' => $this->expire,
        ]);

        return $dataProvider;
    }
}
