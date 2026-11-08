<?php

namespace app\models;

/**
 * إعدادات متابعة الزبائن (صف واحد، id = 1). كل محل له قاعدة بياناته، فالتفعيل خاص بكل محل.
 *
 * @property int $id
 * @property int $enabled
 * @property int|null $defaultUserId
 * @property string|null $lastRunDate
 * @property string|null $updated_at
 */
class CrmSetting extends \yii\db\ActiveRecord
{
    private static $_current;

    public static function tableName()
    {
        return 'crmSetting';
    }

    public function rules()
    {
        return [
            [['enabled'], 'boolean'],
            [['defaultUserId'], 'integer'],
            [['defaultUserId'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id', 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'enabled' => 'تفعيل متابعة الزبائن في هذا المحل',
            'defaultUserId' => 'المسؤول الافتراضي عن الزبائن بلا مسؤول',
        ];
    }

    /** الصف الوحيد؛ null إذا لم تُنفَّذ الـ migration بعد */
    public static function current(): ?self
    {
        if (self::$_current === null) {
            try {
                self::$_current = static::findOne(1) ?: false;
            } catch (\Throwable $e) {
                self::$_current = false;
            }
        }
        return self::$_current ?: null;
    }

    public static function isEnabled(): bool
    {
        $s = static::current();
        return $s !== null && (int) $s->enabled === 1;
    }

    public function beforeSave($insert)
    {
        $this->updated_at = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }
}
