<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\LoanPaid */

$this->title = $model->employee0->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Loan Paids'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="loan-paid-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <br>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'employee0.name',
            'loanId',
            'kestValue',
            'at',
            'month',
            'year',
            'notes',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
        ],
    ]) ?>

</div>