<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = Yii::t('app', 'Create User');
// $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

<br><h1 class="page-header"><?= Html::encode($this->title) ?></h1><hr><br>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
