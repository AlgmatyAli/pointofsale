<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */
?>
<div class="category-info">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <br>
            <h2>
                <?php echo $modelInfo->category0->name; ?>
            </h2>
            <hr>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
            <tr style="font-size: 10px; font-weight: bold;">
                <th><?= Yii::t('app', 'ID') ?></th>
                <th><?= Yii::t('app', 'Serial No') ?></th>
                <th><?= Yii::t('app', 'Comm Code') ?></th>
                <th><?= Yii::t('app', 'Company') ?></th>
                <th><?= Yii::t('app', 'Place') ?></th>
                <th><?= Yii::t('app', 'Weight') ?></th>
                <th><?= Yii::t('app', 'Quantity') ?></th>
            </tr>
            <tr>
                <td><?= Html::encode($modelInfo->category0->id) ?></td>
                <td><?= Html::encode($modelInfo->category0->serialNo) ?></td>
                <td><?= Html::encode($modelInfo->category0->commCode) ?></td>
                <td><?= Html::encode($modelInfo->category0->company) ?></td>
                <td><?= Html::encode($modelInfo->category0->place) ?></td>
                <td><?= Yii::$app->formatter->asDecimal($modelInfo->category0->weight, 3) ?></td>
                <td><?= Yii::$app->formatter->asDecimal($modelInfo->quantity, 3) ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-1"></div>
</div>
<br>
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-6">
        <h5> <?= Yii::t('app', 'آخر 3 فواتير مبيعات') ?></h5>
        <hr>
        <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
            <tr style="font-size: 10px; font-weight: bold;">
                <th><?= Yii::t('app', 'At') ?></th>
                <th><?= Yii::t('app', 'Sale Price') ?></th>
            </tr>
            <tr>
                <?php
                if ($salesInfo != null) {
                    foreach ($salesInfo as $sale) {
                        echo '<td>' . $sale->at . '</td>';
                        echo '<td>' . Yii::$app->formatter->asDecimal($sale->salePrice, 2) . '</td>';
                        echo '</tr><tr>';
                    }
                } else {
                    echo '<td colspan="2">' . Yii::t('app', 'No Sales Found') . '</td>';
                }
                ?>
            </tr>
        </table>
    </div>
    <div class="col-md-5">
        <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
            <tr style="font-size: 10px; font-weight: bold;">
                <?php if (Yii::$app->user->identity->seeCostPrice == 1) {
                    echo '<th>' . Yii::t('app', 'سعر التكلفة') . '</th>';
                    echo '<th>' . Yii::t('app', 'سعر التكلفة بالدرهم') . '</th>';
                    echo '<th>' . Yii::t('app', 'سعر التكلفة الدولار') . '</th>';
                    echo '<th>' . Yii::t('app', 'سعر البيع الأدنى') . '</th>';
                } ?>
            </tr>
            <tr>
                <?php if (Yii::$app->user->identity->seeCostPrice == 1) {
                    echo '<td>' . Html::encode($modelInfo->prices->costPrice) . '</td>';
                    echo '<td>' . Html::encode($modelInfo->prices->minPrice3) . '</td>';
                    echo '<td>' . Html::encode($modelInfo->prices->minPrice2) . '</td>';
                    echo '<td>' . Html::encode($modelInfo->prices->minPrice) . '</td>';
                } ?>
            </tr>
        </table>
        <!-- --- --- --- -->
        <h15> <?= Yii::t('app', 'معلومات الشحنة') ?></h5>
        <hr>        
        <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
            <tr style="font-size: 10px; font-weight: bold;">
                <th><?= Yii::t('app', 'رقم الفاتورة') ?></th>
                <th><?= Yii::t('app', 'اسم العميل') ?></th>
                <th><?= Yii::t('app', 'نوع الشحن') ?></th>
                <th><?= Yii::t('app', 'تاريخ الوصول') ?></th>
                <th><?= Yii::t('app', 'الزمن المتبقي للوصول') ?></th>
            </tr>
            <tr>
                <?php
                if ($dateOfArrival != null) {
                    echo " <td> . Html::encode($dateOfArrival->billId) ?></td>";
                    echo " <td> . Html::encode($dateOfArrival->c->name) ?></td>";
                    echo " <td> . Html::encode($dateOfArrival->shippingType0->name) ?></td>";
                    echo " <td> . Html::encode($dateOfArrival->dateOfArrival) ?></td>";
                    echo " <td> . $datetime1 = date_create($dateOfArrival->dateOfArrival)";
                    "$datetime2 = date_create(date('Y-m-d'))";
                    echo  " $datetime1->diff($datetime2)->days";
                }
                ?>
                </td>
            </tr>
        </table>
    </div>

</div>
<br>

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
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
                    ['class' => 'btn btn-warning']
                ) ?>
            </div>
            <?= Html::a(
                '<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Cancel'),
                Yii::$app->request->referrer,
                ['class' => 'btn btn-danger pull-left']
            ) ?>
        </div>
    </div>
    <div class="col-md-1"></div>
</div>
<br>

<div class="row">
    <div class="col-md-12">
        <?php
        // if ($dateOfArrival != null) {
        //     echo DetailView::widget([
        //         'model' => $dateOfArrival,
        //         'attributes' => [
        //             [
        //                 'label' => Yii::t('app', 'رقم الفاتورة'),
        //                 'attribute' => 'billId',
        //                 'hAlign' => 'right',

        //             ],
        //             [
        //                 'label' => Yii::t('app', 'اسم العميل'),
        //                 'attribute' => 'c.name',
        //                 'hAlign' => 'right',

        //             ],
        //             [
        //                 'label' => Yii::t('app', 'نوع الشحن'),
        //                 'attribute' => 'shippingType0.name',
        //                 'hAlign' => 'right',

        //             ],
        //             [
        //                 'label' => Yii::t('app', 'تاريخ الوصول'),
        //                 'attribute' => 'dateOfArrival',
        //                 'hAlign' => 'right',

        //             ],
        //             [
        //                 'label' => Yii::t('app', 'الزمن المتبقي للوصول'),
        //                 'format' => 'raw',
        //                 'value' => function ($searchModel) {
        //                     $datetime1 = date_create($searchModel->dateOfArrival);
        //                     $datetime2 = date_create(date('Y-m-d'));
        //                     return  $datetime1->diff($datetime2)->days;
        //                 }
        //             ],
        //         ],
        //     ]);
        // }
        ?>
    </div>
</div>


</div>