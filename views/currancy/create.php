<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Currancy */

$this->title = Yii::t('app', 'Create Currancy');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Currancy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currancy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
