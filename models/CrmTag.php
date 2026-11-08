<?php

namespace app\models;

/**
 * مجموعات الزبائن (وسوم): مثلاً «زبائن الجملة» أو «زبائن أحمد». الزبون قد يكون في أكثر من مجموعة.
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property string $created_at
 */
class CrmTag extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'crmTag';
    }

    public function rules()
    {
        return [
            [['name', 'color'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['name'], 'unique'],
            [['color'], 'in', 'range' => array_keys(CrmLevel::COLORS)],
        ];
    }

    public function attributeLabels()
    {
        return ['name' => 'اسم المجموعة', 'color' => 'اللون'];
    }

    public function beforeSave($insert)
    {
        if ($insert && empty($this->created_at)) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        static::getDb()->createCommand()->delete('crmClientTag', ['tagId' => $this->id])->execute();
    }

    public function getMemberIds(): array
    {
        return (new \yii\db\Query())->select('clientId')->from('crmClientTag')->where(['tagId' => $this->id])->column();
    }

    /** @return array id => name لكل المجموعات */
    public static function listAll(): array
    {
        return \yii\helpers\ArrayHelper::map(static::find()->orderBy('name')->all(), 'id', 'name');
    }

    /** وسوم زبون معيّن */
    public static function forClient(int $clientId): array
    {
        return static::find()->innerJoin('crmClientTag ct', 'ct.tagId = crmTag.id')
            ->where(['ct.clientId' => $clientId])->orderBy('name')->all();
    }
}
