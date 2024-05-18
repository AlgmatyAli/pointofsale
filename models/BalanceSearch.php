<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Balance;

/**
 * app\models\BalanceSearch represents the model behind the search form about `app\models\Balance`.
 */
 class BalanceSearch extends Balance
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'number'],
            [['clinet', 'currancy'], 'integer'],
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
        $query = Balance::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>[
                'defaultOrder' => [
                    'clinet' => SORT_ASC
                ]],
            'pagination' => false,//[ 'pageSize' => 70 ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'clinet' => $this->clinet,
            'currancy' => $this->currancy,
        ]);
        $query->andFilterWhere(['<>', 'value', 0]);
      
        return $dataProvider;
    }
}
