<?php

namespace app\models;

/**
 * بيانات المتابعة لكل زبون: المسؤول، الحالة اليدوية، ولقطة الرصيد والمرحلة (تُحدّث يومياً).
 *
 * @property int $clientId
 * @property int|null $userId
 * @property int $state
 * @property int|null $levelId
 * @property float $balance
 * @property string|null $sinceDate
 * @property int|null $days
 * @property string|null $refreshed_at
 *
 * @property Client $client
 * @property User $user
 * @property CrmLevel $level
 */
class CrmClient extends \yii\db\ActiveRecord
{
    const STATE_NORMAL = 0;
    const STATE_DISPUTED = 1;
    const STATE_ESCALATED = 2;

    const STATES = [
        self::STATE_NORMAL => 'حسب المرحلة',
        self::STATE_DISPUTED => 'متنازع عليه',
        self::STATE_ESCALATED => 'مصعّد للمدير',
    ];

    public static function tableName()
    {
        return 'crmClient';
    }

    public static function primaryKey()
    {
        return ['clientId'];
    }

    public function rules()
    {
        return [
            [['clientId'], 'required'],
            [['clientId', 'userId', 'levelId', 'days'], 'integer'],
            [['state'], 'in', 'range' => array_keys(self::STATES)],
            [['balance'], 'number'],
            [['sinceDate', 'refreshed_at'], 'safe'],
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

    public function getLevel()
    {
        return $this->hasOne(CrmLevel::class, ['id' => 'levelId']);
    }

    /** يعيد السجل أو ينشئه (غير محفوظ) */
    public static function forClient(int $clientId): self
    {
        $row = static::findOne($clientId);
        if ($row === null) {
            $row = new static(['clientId' => $clientId, 'state' => self::STATE_NORMAL, 'balance' => 0]);
        }
        return $row;
    }
}
