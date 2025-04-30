<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Category;

/**
 * CategorySearch represents the model behind the search form of `app\models\Category`.
 */
class CategorySearch extends Category
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'box', 'minimum', 'ending', 'qShow', 'status', 'user_insert', 'user_update'], 'safe'],
            [['name', 'class', 'unit', 'created_at', 'update_at', 'serialNo', 'commCode', 'moreRequest', 'place', 'weight', 'company'], 'safe'],
            [['cost', 'price', 'quantity'], 'number'],
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
        $query = Stocks::find()
        ->select('stocks.category, branch, max(category.name) as name, sum(stocks.quantity) as quantity, sum(stocks.type) as type, max(category.unit) as unit, 
        max(category.company) as company, max(category.box) as box, max(category.class) as class, 
        max(category.serialNo) as serialNo, max(category.commCode) as commCode, max(category.status) as status'
        )
        ->leftJoin('category', 'category.id = stocks.category')
        ->leftJoin('branches', 'branches.id = stocks.branch')
        ->groupBy('stocks.branch, stocks.category');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['category' => SORT_ASC]
            ],
            //'pagination' => false,
            'pagination' => ['pageSize' => 200],
        ]);

        // $query = Category::find()
        // ->select('category.id, category.name, serialNo, commCode, class, company, place, stocks.quantity, branches.name as branch')
        // ->leftJoin('stocks', 'category.id = stocks.category')
        // ->leftJoin('branches', 'branches.id = stocks.branch')
        // ->groupBy('stocks.branch, category.id');
        // add conditions that should always apply here

        // $dataProvider = new ActiveDataProvider([
        //     'query' => $query,
        //     'pagination' => [ 'pageSize' => 200 ],
        // ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'category.id' => $this->id,
            'box' => $this->box,
            'cost' => $this->cost,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'minimum' => $this->minimum,
            'category.serialNo' => $this->serialNo,
            'ending' => $this->ending,
            'qShow' => $this->qShow,
            'category.status' => $this->status,
            'user_insert' => $this->user_insert,
            //'created_at' => $this->created_at,
            'user_update' => $this->user_update,
            'update_at' => $this->update_at,
            'category.commCode' => $this->commCode,

        ]);

        $query->andFilterWhere(['like', 'category.name', $this->name])
            ->andFilterWhere(['=', 'category.class', $this->class])
            ->andFilterWhere(['=', 'category.place', $this->place])
            ->andFilterWhere(['=', 'category.weight', $this->weight])
            ->andFilterWhere(['=', 'category.company', $this->company])
            ->andFilterWhere(['like', 'unit', $this->unit]);
        $query->andFilterWhere(['=', 'created_at', $this->created_at]);

        return $dataProvider;
    }
}
