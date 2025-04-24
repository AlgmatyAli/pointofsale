<?php

use yii\helpers\Html;
use app\models\CompanyInfo;
/* @var $this yii\web\View */
/* @var $model app\models\Salaryroll */
?>

<div class="row">

    <div class="col-md-12">
        <br><br>

        <p>
            <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print') ?></button>`
            <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btnx']) ?>
        </p>

        <div id='div1' class="site-about">

            <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one(); ?>

            <img src=<?php echo $title["path"] ?> class="img-circle logo" alt="Cinque Terre">
            <h4><?php
                echo  $title['name'];
                echo '<br>';
                ?> </h4>
            <br>
            <center>
                <div>
                    <h3>تقرير صافي المبيعات من تاريخ : <b><?= $model->min_date ?></b> حتى تاريخ <b><?= $model->max_date ?></b></h3>
                    <br>
                    <h3>للموظف : <b><?= $model->userInsert->username ?></b> </h3>
                </div>
            </center>
            <br><br>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            <h5> بيان الحركة</h5>
                        </th>
                        <th>
                            <h5> الإجمالي</h5>
                        </th>
                    </tr>
                </thead>

                <?php
                $coun = 1;
                foreach ($models as $model):
                    $count = $count + 1;
                    $total += $model["NET"];
                ?>

                    <tbody>
                        <tr>
                            <td><?= $coun++ ?></td>
                            <td><?= $model["DESCRIBTION"] ?></td>
                            <td><?= number_format($model["total"], 3) . "\n"; ?></td>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
                <tr>
                    <td></td>
                    <td>الصافي</td>
                    <td><?php echo number_format($total, 3) . "\n"; ?></td>
                </tr>
            </table>
        </div>



    </div>