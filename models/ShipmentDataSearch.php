<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ShipmentData;

/**
 * ShipmentDataSearch represents the model behind the search form of `app\models\ShipmentData`.
 */
class ShipmentDataSearch extends ShipmentData
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'shipmentId', 'country', 'type', 'created_by', 'updated_by', 'customsOffice'], 'integer'],
            [['at', 'notes', 'created_at', 'updated_at', 'size', 'currancy'], 'safe'],
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
        $query = ShipmentData::find();

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
            'shipmentId' => $this->shipmentId,
            'at' => $this->at,
            'size' => $this->size,
            'country' => $this->country,
            'type' => $this->type,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'notes', $this->notes]);

        return $dataProvider;
    }
}
