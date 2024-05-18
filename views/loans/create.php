<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Loans */

$this->title = Yii::t('app', 'Create Loans');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Loans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loans-create">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
