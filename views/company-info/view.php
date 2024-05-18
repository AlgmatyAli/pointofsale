<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyInfo */

//$this->title = $model->name;
// $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Company Infos'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-info-view">

    <br>
    <h1><?= Html::encode($this->title) ?></h1>
    <br>
    <p>
        <?= Html::a('<i class="fa fa-fw fa fa-pencil-square-o"></i>'.' '.Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-minus"></i>'.' '.Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-lg',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
           <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Cancel'), ['/company-info/index'], ['class'=>'btn btn-primary btn-lg']) ?>
    </p><br>
<div class="row">
<div class="col-md-12">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'work',
            'address:ntext',
            'phone1',
            'phone2',
            'phone3',
            'fax',
            'email:email',
            [
                'attribute' => 'terms',
                'format' => 'raw',
            ],
            'skin',
            'rate',
            'criteriaـvalue',
            
        ],
    ]) ?>
</div>
<div class="col-md-6"> 

  </div>
</div>
</div>
