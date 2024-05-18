<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CustomsOffice;

/**
 * CustomsOfficeSearch represents the model behind the search form of `app\models\CustomsOffice`.
 */
class CustomsOfficeSearch extends CustomsOffice
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customId', 'user_insert', 'user_update'], 'integer'],
            [['at', 'why', 'created_at', 'update_at', 'min_date', 'max_date', 'allData', 'currancy', 'customId',], 'safe'],
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
        $query = CustomsOffice::find();

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
            'customId' => $this->customId,
            'at' => $this->at,
            'value' => $this->value,
            'user_insert' => $this->user_insert,
            'created_at' => $this->created_at,
            'user_update' => $this->user_update,
            'update_at' => $this->update_at,
        ]);

        $query->andFilterWhere(['like', 'why', $this->why]);

        return $dataProvider;
    }
}
