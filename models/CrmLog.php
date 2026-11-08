<?php

namespace app\models;

/**
 * الخط الزمني للزبون: ملاحظات، رسائل واتساب، مهام مغلقة، وعود، تغيير المسؤول أو المرحلة.
 *
 * @property int $id
 * @property int $clientId
 * @property string $kind note, whatsapp, task, promise, state, assign, level, group
 * @property string $text
 * @property int|null $userId
 * @property string $created_at
 *
 * @property User $user
 */
class CrmLog extends \yii\db\ActiveRecord
{
    const ICONS = [
        'note' => 'bi-chat-left-text',
        'whatsapp' => 'bi-whatsapp',
        'task' => 'bi-check2-circle',
        'promise' => 'bi-hand-thumbs-up',
        'state' => 'bi-signpost-split',
        'assign' => 'bi-person-check',
        'level' => 'bi-bar-chart-steps',
        'group' => 'bi-collection',
    ];

    public static function tableName()
    {
        return 'crmLog';
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public static function add(int $clientId, string $kind, string $text, ?int $userId = null): void
    {
        $row = new static([
            'clientId' => $clientId,
            'kind' => $kind,
            'text' => $text,
            'userId' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $row->save(false);
    }
}
