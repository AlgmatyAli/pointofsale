<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * الشركات المصنّعة للأصناف (جدول itemCompany).
 *
 * @property int $id
 * @property string $name
 * @property string|null $created_at
 * @property string|null $update_at
 *
 * @property Category[] $categories
 */
class ItemCompany extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'itemCompany';
    }

    public function rules()
    {
        return [
            [['name'], 'filter', 'filter' => 'trim'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'اسم الشركة'),
            'created_at' => Yii::t('app', 'Created At'),
            'update_at' => Yii::t('app', 'Update At'),
        ];
    }

    public function getCategories()
    {
        return $this->hasMany(Category::class, ['companyId' => 'id']);
    }

    /** للقوائم المنسدلة: [id => name] */
    public static function map(): array
    {
        return ArrayHelper::map(self::find()->select(['id', 'name'])->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name');
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $now = date('Y-m-d H:i:s');
        if ($insert) {
            $this->created_at = $now;
        } else {
            $this->update_at = $now;
        }
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        // عمود category.company النصي تعتمد عليه التقارير، فيبقى مطابقاً لاسم الشركة
        if (!$insert && array_key_exists('name', $changedAttributes)) {
            Category::updateAll(['company' => $this->name], ['companyId' => $this->id]);
        }
    }
}
