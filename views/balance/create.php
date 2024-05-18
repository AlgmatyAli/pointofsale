<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Balance */

$this->title = Yii::t('app', 'Create Balance');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Balance'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="balance-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
