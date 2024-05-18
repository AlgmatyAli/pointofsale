<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Ftran;

/**
 * app\models\FtranSearch represents the model behind the search form about `app\models\Ftran`.
 */
 class FtranSearch extends Ftran
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_', 'description', 'currancy'], 'safe'],
            [['wared'], 'number'],
            [['sader', 'payWay', 'branch', 'user_insert'], 'integer'],
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
        $query = Ftran::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>[
                'defaultOrder' => [
                    'user_insert' => SORT_DESC,

                    'date_' => SORT_DESC, 
                ],
               
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
           
            'wared' => $this->wared,
            'sader' => $this->sader,
            'payWay' => $this->payWay,
            'branch' => $this->branch,
            'user_insert' => $this->user_insert,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        
        if(!empty($this->date_) && strpos($this->date_, '-') !== false) {
            list($min_date, $max_date) = explode(' - ', $this->date_);
        $query->andFilterWhere(['between', 'date_', $min_date, $max_date]);}

        return $dataProvider;
    }
}
