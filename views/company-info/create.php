<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyInfo */

$this->title = Yii::t('app', 'Create Company Info');
// $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Company Infos'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
?>

<div class="company-info-create">

<br>
<h2><?= Html::encode($this->title) ?></h2><hr>
<br>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
