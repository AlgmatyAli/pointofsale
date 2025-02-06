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
            [['id', 'type', 'currency'], 'integer'],
            [['name', 'phone', 'deserving'], 'safe'],
            [['credt'], 'number'],
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
        $query = Balance::find()
            ->select(['id, max(name) as name, sum(credt*-1) as credt, max(phone) as phone, currency, max(type) as type, max(deserving) as deserving'])
            ->where(['<>', 'credt', 0])
            ->groupBy(['name', 'currency']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // 'sort' => [
            //     'defaultOrder' => [
            //         'id' => SORT_ASC
            //     ]
            // ],
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
            'credt' => $this->credt,
            'deserving' => $this->deserving,
            'currency' => $this->currency,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        if ($this->type == 0 || $this->type == 2) {
            $query->andFilterWhere(['in', 'type', [0, 2]]);
        }elseif ($this->type == 1) {
            $query->andFilterWhere(['=', 'type', 1]);
        }else{
            $query->andFilterWhere(['in', 'type', [0, 1, 2]]);
        }
        return $dataProvider;
    }
}
