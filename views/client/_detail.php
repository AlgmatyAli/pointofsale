<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Client */

?>
<div class="client-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Html::encode($model->name) ?></h2>
        </div>
    </div>

    <div class="row">
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'phone',
        'mobile',
        'address',
        'email:email',
        'balance',
        'type',
        [
            'attribute' => 'userInsert.username',
            'label' => 'User Insert',
        ],
        [
            'attribute' => 'userUpdate.username',
            'label' => 'User Update',
        ],
        'update_at',
        'branch',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]); 
?>
    </div>
</div>