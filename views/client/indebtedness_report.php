<?php

use yii\helpers\Html;
use app\models\CompanyInfo;

/* @var $this yii\web\View */
/* @var $model app\models\Salaryroll */

/** @var array $models */
/** @var int $indebtedness */
?>

<div class="row">

    <div class="col-md-1">
    </div>

    <div class="col-md-10">
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

            <center>
                <div>
                    <?php if ($indebtedness == 0) {
                        echo '<h3>تقرير بإجمالي الدائنون حتى تاريخ: <u><b>' . date("Y/m/d") . '</b></u></h3>';
                    } elseif ($indebtedness == 1) {
                        echo '<h3>تقرير بإجمالي المدينون حتى تاريخ: <u><b>' . date("Y/m/d") . '</b></u></h3>';
                    }
                    ?>
                </div>
            </center>
            <div class="clearfix"></div>
            <br><br>



            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>
                            <h5> رقم الحساب</h5>
                        </th>
                        <th>
                            <h5> اسم العميل</h5>
                        </th>
                        <th>
                            <h5> الرصيـد</h5>
                        </th>
                        <th>
                            <h5> رقم الهاتف</h5>
                        </th>
                        <th>
                            <h5></h5>
                        </th>
                    </tr>
                </thead>

                <?php
                $sum = 0;
                $credt = 0;
                $coun = 1;
                foreach ($models as $model):
                    if ($model["credt"] < 0) {
                        $credt = $credt + $model["credt"];
                    } else {
                        $dept = $dept + $model["credt"];
                    }
                    $count = $count + 1;
                ?>

                    <tbody>
                        <tr>
                            <td><?= $coun++ ?></td>
                            <td><?= $model["id"] ?></td>

                            <?php

                            if ($model["post_paid"] == 0) {
                                echo '<td style="color: red;  text-decoration: underline;">' . $model["name"] . '</td>';
                            } else {
                                echo '<td>' . $model["name"] . ' </td>';
                            }
                            ?>

                            <td><?php
                                if ($indebtedness == 0) {
                                    echo number_format($model["credt"], 3)  . "\n";
                                } elseif ($indebtedness == 1) {
                                    echo number_format($model["credt"] * -1, 3)  . "\n";
                                }
                                ?></td>
                            <td><?= $model["phone"] ?></td>
                            <td>
                                <?php
                                // if ($model["type"] != 1) {
                                //     if (!is_null($model["deserving"])) {
                                //         echo Html::a('<i class="fa fa-circle opacity2"></i>', ['#'], [
                                //             'title' => Yii::t('yii', 'View'),
                                //             'class' => ''
                                //         ]);
                                //     }
                                // }
                                ?>
                            </td>
                            <td><?= Html::a(
                                    '<i class="fa fa-folder-open"></i>',
                                    ['sales/histrans', 'client' => $model["id"], 'allData' => 1, 'type' => $model["type"]],
                                    ['class' => 'btn btn-success']
                                ) ?></td>
                        </tr>
                    </tbody>
                <?php endforeach; ?>

                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <?php
                        if ($indebtedness == 0) {
                            echo 'الاجمالي : ' . number_format($dept, 3) . "\n";
                        } elseif ($indebtedness == 1) {
                            echo 'الاجمالي : ' . number_format($credt * -1, 3) . "\n";
                        }
                        ?>
                    </td>
                    <td></td>
                </tr>
            </table>
        </div>

        <div class="col-md-1">
        </div>

    </div>