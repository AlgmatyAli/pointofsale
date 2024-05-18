<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ReceiptArch;

/**
 * ReceiptArchSearch represents the model behind the search form of `app\models\ReceiptArch`.
 */
class ReceiptArchSearch extends ReceiptArch
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'rId', 'clinet', 'type', 'delete_by', 'delete_at'], 'integer'],
            [['at', 'why', 'payWay'], 'safe'],
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
        $query = ReceiptArch::find();

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
            'rId' => $this->rId,
            'clinet' => $this->clinet,
            'at' => $this->at,
            'value' => $this->value,
            'type' => $this->type,
            'delete_by' => $this->delete_by,
            'delete_at' => $this->delete_at,
        ]);

        $query->andFilterWhere(['like', 'why', $this->why])
            ->andFilterWhere(['like', 'payWay', $this->payWay]);

        return $dataProvider;
    }
}
