<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "disscount_clients".
 *
 * @property int $id
 * @property int $client
 * @property string|null $at
 * @property string $value
 * @property int $type
 * @property int $currancy
 * @property int $branch
 * @property string|null $notes
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property Branches $branch0
 * @property Client $client0
 * @property User $createdBy
 * @property Currancy $currancy0
 * @property User $updatedBy
 */
class DisscountClients extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'disscount_clients';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['at', 'notes', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'default', 'value' => null],
            [['value'], 'default', 'value' => 0.000],
            [['client', 'type', 'currancy', 'branch'], 'required'],
            [['client', 'type', 'currancy', 'branch', 'created_by', 'updated_by'], 'integer'],
            [['at', 'created_at', 'updated_at'], 'safe'],
            [['value'], 'number'],
            [['notes'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['client'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client' => 'id']],
            [['currancy'], 'exist', 'skipOnError' => true, 'targetClass' => Currancy::class, 'targetAttribute' => ['currancy' => 'id']],
            [['branch'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::class, 'targetAttribute' => ['branch' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client' => Yii::t('app', 'Client'),
            'at' => Yii::t('app', 'At'),
            'value' => Yii::t('app', 'Value'),
            'type' => Yii::t('app', 'Type'),
            'currancy' => Yii::t('app', 'Currancy'),
            'branch' => Yii::t('app', 'Branch'),
            'notes' => Yii::t('app', 'Notes'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Branch0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch0()
    {
        return $this->hasOne(Branches::class, ['id' => 'branch']);
    }

    /**
     * Gets query for [[Client0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient0()
    {
        return $this->hasOne(Client::class, ['id' => 'client']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Currancy0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrancy0()
    {
        return $this->hasOne(Currancy::class, ['id' => 'currancy']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

}
