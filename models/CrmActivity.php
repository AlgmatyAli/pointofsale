<?php

namespace app\models;

/**
 * مهام المتابعة (اتصال، واتساب، زيارة...). المهام التلقائية تنشأ من التحديث اليومي.
 *
 * @property int $id
 * @property int $clientId
 * @property string $type
 * @property string $dueDate
 * @property int|null $userId
 * @property string|null $note
 * @property int $auto
 * @property string|null $sourceKey
 * @property string|null $doneAt
 * @property int|null $doneBy
 * @property string|null $result
 * @property int|null $created_by
 * @property string $created_at
 *
 * @property Client $client
 * @property User $user
 * @property CrmClient $crm
 */
class CrmActivity extends \yii\db\ActiveRecord
{
    const TYPES = [
        'call' => 'اتصال هاتفي',
        'whatsapp' => 'تذكير واتساب',
        'visit' => 'زيارة',
        'manager' => 'تصعيد للمدير',
        'task' => 'مهمة أخرى',
    ];

    const ICONS = [
        'call' => 'bi-telephone',
        'whatsapp' => 'bi-whatsapp',
        'visit' => 'bi-geo-alt',
        'manager' => 'bi-person-up',
        'task' => 'bi-check2-square',
    ];

    const RESULTS = [
        'تم التواصل',
        'لم يرد',
        'الرقم خطأ أو مغلق',
        'رفض الدفع',
        'طلب مهلة',
        'أُرسل تذكير واتساب',
        'تم السداد',
        'أخرى',
    ];

    public static function tableName()
    {
        return 'crmActivity';
    }

    public function rules()
    {
        return [
            [['clientId', 'type', 'dueDate'], 'required'],
            [['clientId', 'userId'], 'integer'],
            [['type'], 'in', 'range' => array_keys(self::TYPES)],
            [['dueDate'], 'date', 'format' => 'php:Y-m-d'],
            [['note'], 'string', 'max' => 2000],
            [['clientId'], 'exist', 'targetClass' => Client::class, 'targetAttribute' => 'id'],
            [['userId'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id', 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'clientId' => 'الزبون',
            'type' => 'نوع المهمة',
            'dueDate' => 'تاريخ المتابعة',
            'userId' => 'المكلّف',
            'note' => 'ملاحظة',
            'result' => 'النتيجة',
        ];
    }

    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'clientId']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public function getCrm()
    {
        return $this->hasOne(CrmClient::class, ['clientId' => 'clientId']);
    }

    public function beforeSave($insert)
    {
        if ($insert && empty($this->created_at)) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    /** المهام المفتوحة المستحقة اليوم أو المتأخرة لمستخدم */
    public static function dueCountFor(int $userId): int
    {
        return (int) static::find()
            ->where(['doneAt' => null, 'userId' => $userId])
            ->andWhere(['<=', 'dueDate', date('Y-m-d')])
            ->count();
    }

    public function close(string $result, ?int $by): bool
    {
        $this->doneAt = date('Y-m-d H:i:s');
        $this->doneBy = $by;
        $this->result = mb_substr($result, 0, 255);
        return $this->save(false, ['doneAt', 'doneBy', 'result']);
    }
}
