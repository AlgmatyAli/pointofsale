<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BalanceHistory;

/**
 * app\models\BalanceHistorySearch represents the model behind the search form about `app\models\BalanceHistory`.
 */
 class BalanceHistorySearch extends BalanceHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'number'],
            [['clinet', 'currancy'], 'integer'],
            [['AT', 'why', 'type', 'client_type'], 'safe'],
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
        $query = BalanceHistory::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => false],
            'sort'=> ['defaultOrder' => [
                'clinet' => SORT_ASC,
                'currancy' => SORT_DESC
            ]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'value' => $this->value,
            'clinet' => $this->clinet,
            'currancy' => $this->currancy,
            'AT' => $this->AT,
        ]);

        $query->andFilterWhere(['like', 'why', $this->why])
            ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
