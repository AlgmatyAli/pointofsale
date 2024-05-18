<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = Yii::t('app', 'Update User');
// $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
// $this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="user-update">
<br><h1 class="page-header"><?= Html::encode($this->title) ?></h1><hr><br>
    <?= $this->render('update_form', [
        'model' => $model,
    ]) ?>

</div>
