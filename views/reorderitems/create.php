<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Reorderitems */

$this->title = Yii::t('app', 'Create Reorderitems');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reorderitems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reorderitems-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
