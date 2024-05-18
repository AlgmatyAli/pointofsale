<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Image;

/**
 * ImageSearch represents the model behind the search form of `app\models\Image`.
 */
class ImageSearch extends Image
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'claimId', 'user_insert', 'user_update'], 'integer'],
            [['path', 'name', 'created_at', 'update_at'], 'safe'],
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
        $query = Image::find();

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
            'claimId' => $this->claimId,
            'user_insert' => $this->user_insert,
            'user_update' => $this->user_update,
            'created_at' => $this->created_at,
            'update_at' => $this->update_at,
        ]);

        $query->andFilterWhere(['like', 'path', $this->path])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
