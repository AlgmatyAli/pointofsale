<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Transfer;

/**
 * TransferSearch represents the model behind the search form of `app\models\Transfer`.
 */
class TransferSearch extends Transfer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fromBr', 'toBr', 'type', 'user_insert', 'user_update'], 'integer'],
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
        $query = Transfer::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'fromBr' => $this->fromBr,
            'toBr' => $this->toBr,
            'value' => $this->value,
            'at' => $this->at,
            'type' => $this->type,
            'user_insert' => $this->user_insert,
            'created_at' => $this->created_at,
            'user_update' => $this->user_update,
            'update_at' => $this->update_at,
        ]);

        $query->andFilterWhere(['like', 'why', $this->why]);

        return $dataProvider;
    }
}
