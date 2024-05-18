<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Reorderitems;

/**
 * app\models\ReorderitemsSearch represents the model behind the search form about `app\models\Reorderitems`.
 */
 class ReorderitemsSearch extends Reorderitems
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'minimum'], 'integer'],
            [['name', 'serialNo', 'class', 'company'], 'safe'],
            [['quantity'], 'number'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Reorderitems::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>[
                'defaultOrder' => ['id' => SORT_ASC]],
                'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'minimum' => $this->minimum,
            'quantity' => $this->quantity,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'serialNo', $this->serialNo])
            ->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['like', 'company', $this->company]);

        return $dataProvider;
    }
}
