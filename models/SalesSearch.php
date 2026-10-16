<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sales;
use Yii;

/**
 * SalesSearch represents the model behind the search form of `app\models\Sales`.
 */
class SalesSearch extends Sales
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'id',
                'billId',
                'clinet',
                'payWay',
                'branch',
                'type',
                'carpenter',
                'upholstered',
                'agent',
                'paintId',
                'deleviryId',
                'user_insert',
                'user_update',
                'currancy',
            ], 'safe'],
            [[
                'at',
                'notes',
                'path',
                'deleviryAt',
                'created_at',
                'update_at',
                'phone',
                'agent',
                'deleviried',
                'deserving',
                'wholesale'
            ], 'safe'],
            [['total', 'paid', 'disscount', 'payment_type'], 'safe'],
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

        // العلاقات التي يعرضها التقرير تُحمَّل دفعة واحدة بدل استعلام لكل سطر
        $query = Sales::find()->with(['c', 'paymentType0', 'userInsert']);
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
            'pagination' => ['pageSize' => 70],
        ]);

        $this->load($params);

        // من لا يملك can_see_other_branch_sales لا يرى إلا فرعه، مهما غيّر في الرابط أو أفرغ الحقل
        if (!Yii::$app->user->can('can_see_other_branch_sales')) {
            $this->branch = Yii::$app->user->identity->branch;
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('sales.branch='.Yii::$app->user->identity->branch);
            // $query->where('id<=1');
            // $dataProvider->query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'sales.id' => $this->id,
            'sales.billId' => $this->billId,
            'sales.payWay' => $this->payWay,
            'sales.branch' => $this->branch,
            'sales.total' => $this->total,
            'sales.paid' => $this->paid,
            'sales.type' => $this->type,
            'sales.created_at' => $this->created_at,
            'sales.user_update' => $this->user_update,
            'sales.update_at' => $this->update_at,
            'sales.deleviried' => $this->deleviried,
            'sales.wholesale' => $this->wholesale,
            'sales.currancy' => $this->currancy,
            'sales.payment_type' => $this->payment_type,
        ]);

        $query->andFilterWhere(['like', 'sales.notes', $this->notes])
            ->andFilterWhere(['like', 'sales.path', $this->path])
            ->andFilterWhere(['=', 'sales.billId', $this->billId]);

        // الفلترة برقم الهاتف عبر استعلام فرعي على العملاء (كانت تسبب خطأ SQL لأن الجدول غير مربوط)
        if (!empty($this->phone)) {
            $query->andWhere(['in', 'sales.clinet', (new \yii\db\Query())->select('id')->from('client')->where(['phone' => $this->phone])]);
        }

        if (Yii::$app->user->identity->client != null) {
            // مستخدم العميل لا يرى إلا عملاءه. andWhere (وليس andFilterWhere) لأن الأخير يُسقط الشرط
            // إن كانت القائمة فارغة فيرى المستخدم كل الفواتير.
            $ownClients = array_values(array_filter(
                array_map('trim', explode(',', (string) Yii::$app->user->identity->client)),
                'strlen'
            ));
            $query->andWhere($ownClients ? ['in', 'sales.clinet', $ownClients] : '0=1');
        } else {
            $query->andFilterWhere(['=', 'sales.clinet', $this->clinet]);
        }

        if (Yii::$app->user->can('userCanSeeOtherUsersSales')) {
            $query->andFilterWhere(['=', 'sales.user_insert', $this->user_insert]);
        } else {
            $query->andFilterWhere(['=', 'sales.user_insert', Yii::$app->user->identity->id]);
        }

        // نطاق التاريخ بصيغة "من - إلى". تاريخ مفرد يُعامل كيوم واحد بدل أن يسبب خطأ
        if (!empty($this->at) && strpos($this->at, '-') !== false) {
            $parts = array_map('trim', explode(' - ', $this->at));
            $query->andFilterWhere(['between', 'sales.at', $parts[0], $parts[1] ?? $parts[0]]);
        }

        if (!empty($this->deleviryAt) && strpos($this->deleviryAt, '-') !== false) {
            $parts = array_map('trim', explode(' - ', $this->deleviryAt));
            $query->andFilterWhere(['between', 'sales.deleviryAt', $parts[0], $parts[1] ?? $parts[0]]);
        }

        return $dataProvider;
    }
}
