<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyInfo */

// $this->title = Yii::t('app', 'Update {modelClass}: ', [
//     'modelClass' => 'Company Info',
// ]) . $model->name;
// $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Company Infos'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
// $this->params['breadcrumbs'][] = Yii::t('app', 'Update');
$this->title = Yii::t('app', 'Update Company Info');

?>
<div class="company-info-update">

<br>
    <h2><?= Html::encode($this->title) ?></h2><hr>
    <br>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
