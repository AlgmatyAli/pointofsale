<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ItemCompany */

$this->title = Yii::t('app', 'إضافة شركة');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'الشركات المصنّعة'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-company-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
