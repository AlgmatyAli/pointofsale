<?php

namespace app\models;

/**
 * وعود الدفع. يُعدّ الوعد منفّذاً إذا بلغ المقبوض من الزبون منذ تسجيله قيمة الوعد،
 * أو صار رصيده صفراً. إذا مرّ تاريخه دون ذلك يصبح منكوثاً ويظهر تنبيه للمدير.
 *
 * @property int $id
 * @property int $clientId
 * @property float $amount
 * @property string $promiseDate
 * @property int $status
 * @property string|null $note
 * @property int $afterReceiptId
 * @property float $paidSince
 * @property int $reviewed
 * @property int|null $created_by
 * @property string $created_at
 * @property string|null $closed_at
 *
 * @property Client $client
 * @property User $createdBy
 */
class CrmPromise extends \yii\db\ActiveRecord
{
    const OPEN = 0;
    const KEPT = 1;
    const BROKEN = 2;
    const CANCELLED = 3;

    const STATUSES = [
        self::OPEN => 'قائم',
        self::KEPT => 'نُفّذ',
        self::BROKEN => 'منكوث',
        self::CANCELLED => 'ملغى',
    ];

    const COLORS = [
        self::OPEN => 'primary',
        self::KEPT => 'success',
        self::BROKEN => 'danger',
        self::CANCELLED => 'secondary',
    ];

    public static function tableName()
    {
        return 'crmPromise';
    }

    public function rules()
    {
        return [
            [['clientId', 'amount', 'promiseDate'], 'required'],
            [['clientId'], 'integer'],
            [['amount'], 'number', 'min' => 0.001],
            [['promiseDate'], 'date', 'format' => 'php:Y-m-d'],
            [['note'], 'string', 'max' => 255],
            [['clientId'], 'exist', 'targetClass' => Client::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'clientId' => 'الزبون',
            'amount' => 'المبلغ الموعود',
            'promiseDate' => 'تاريخ الدفع الموعود',
            'note' => 'ملاحظة',
            'status' => 'الحالة',
            'paidSince' => 'المقبوض منذ الوعد',
        ];
    }

    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'clientId']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = $this->created_at ?: date('Y-m-d H:i:s');
            // عمود created_at في receipt تاريخ بلا وقت، فنحسب المقبوض بعد آخر إيصال موجود لحظة الوعد
            $this->afterReceiptId = (int) (new \yii\db\Query())->from('receipt')->max('id');
        }
        return parent::beforeSave($insert);
    }

    /** الوعود المنكوثة التي لم يطّلع عليها المدير */
    public static function brokenCount(): int
    {
        return (int) static::find()->where(['status' => self::BROKEN, 'reviewed' => 0])->count();
    }

    public static function openFor(int $clientId): ?self
    {
        return static::find()->where(['clientId' => $clientId, 'status' => self::OPEN])->orderBy(['promiseDate' => SORT_ASC])->one();
    }
}
