<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */
?>
<div class="category-info">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <br>
            <h2>
                <?php echo $modelInfo->name; ?>
            </h2>
            <br>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $modelInfo,
            'attributes' => [
                'category0.id',
                'category0.serialNo',
                'category0.commCode',
                'category0.company',
                'category0.place',
                [
                    'label' => Yii::t('app', 'Weight'),
                    'attribute' => 'category0.weight',
                    'format' => ['decimal', 3],
                    'hAlign' => 'right',

                ],
                [
                    'label' => Yii::t('app', 'Quantity'),
                    'attribute' => 'quantity',
                    'format' => ['decimal', 3],
                    'hAlign' => 'right',

                ],
                [
                    'label' => Yii::t('app', ''),
                    'format' => 'raw',
                    'value' => function ($searchModel) {
                        if (Yii::$app->user->identity->seeCostPrice == 1) {
                            return 'سعر التكلفة' . ' ' . $searchModel->prices->costPrice;
                        } else {
                            return 'سعر البيع الأدنى' . ' ' . $searchModel->prices->minPrice;
                        }
                    }
                ],

                [
                    'label' => Yii::t('app', ''),
                    'format' => 'raw',
                    'value' => function ($searchModel) {
                        if (Yii::$app->user->identity->seeCostPrice == 1) {
                            return 'سعر التكلفة بالدرهم' . ' ' . $searchModel->prices->minPrice3;
                        }
                    }
                ],

                [
                    'label' => Yii::t('app', ''),
                    'format' => 'raw',
                    'value' => function ($searchModel) {
                        if (Yii::$app->user->identity->seeCostPrice == 1) {
                            return 'سعر التكلفة الدولار' . ' ' . $searchModel->prices->minPrice2;
                        }
                    }
                ],

            ],
        ]) ?>
        <div class="form-group">
            <div class="btn-group">
                <?= Html::a(
                    '<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update'),
                    ['update', 'id' => $modelInfo->category0->id],
                    ['class' => 'btn btn-primary']
                ) ?>
                <?= Html::a(
                    Yii::t('app', 'Create Category Histrans'),
                    ['sales/category-histrans', 'category' => $modelInfo->category0->id, 'allData' => 1],
                    ['class' => 'btn btn-success']
                ) ?>
                <?= Html::a(
                    '<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update Prices'),
                    ['prices/index_', 'category' => $modelInfo->category0->id],
                    ['class' => 'btn btn-primary']
                ) ?>
            </div>
            <?= Html::a(
                '<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Cancel'),
                Yii::$app->request->referrer,
                ['class' => 'btn btn-danger pull-left']
            ) ?>
        </div>
    </div>
    <div class="col-md-6">
        <?php
        if ($dateOfArrival != null) {
            echo DetailView::widget([
                'model' => $dateOfArrival,
                'attributes' => [
                    [
                        'label' => Yii::t('app', 'رقم الفاتورة'),
                        'attribute' => 'billId',
                        'hAlign' => 'right',

                    ],
                    [
                        'label' => Yii::t('app', 'اسم العميل'),
                        'attribute' => 'c.name',
                        'hAlign' => 'right',

                    ],
                    [
                        'label' => Yii::t('app', 'نوع الشحن'),
                        'attribute' => 'shippingType0.name',
                        'hAlign' => 'right',

                    ],
                    [
                        'label' => Yii::t('app', 'تاريخ الوصول'),
                        'attribute' => 'dateOfArrival',
                        'hAlign' => 'right',

                    ],
                    [
                        'label' => Yii::t('app', 'الزمن المتبقي للوصول'),
                        'format' => 'raw',
                        'value' => function ($searchModel) {
                            $datetime1 = date_create($searchModel->dateOfArrival);
                            $datetime2 = date_create(date('Y-m-d'));
                            return  $datetime1->diff($datetime2)->days;
                        }
                    ],
                ],
            ]);
        }
        ?>
    </div>
</div>


</div>