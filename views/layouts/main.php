<?php
use yii\helpers\Html;
use app\models\CompanyInfo;
use yii\bootstrap\Modal;


/* @var $this \yii\web\View */
/* @var $content string */

if (Yii::$app->controller->action->id === 'login') { 
/**
 * Do not use this code in your template. Remove it. 
 * Instead, use the code  $this->layout = '//main-login'; in your controller.
 */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {
    app\assets\AppAsset::register($this);
    //dmstr\web\AdminLteAsset::register($this);
    airani\AdminLteRtlAsset::register($this);


    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
        app\assets\AppAsset::register($this);
    ?>
    <?php $info = CompanyInfo::find()->select(['skin', 'name'])->one(); 
          $body = '"' ."hold-transition ". $info->skin ." sidebar-mini" .'"';
          $title = $info->name;
    ?>
    <?php //die($body);?>

    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($title) ?></title>
        <?php $this->head() ?>
    </head> 
    <!-- skin-blue -->
    <body class= <?= $body ?>>
    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>

        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset]
        )
        ?>
        
        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>

    </div>

    <?php
        Modal::begin([
            'header' => '<b>' . Yii::t('app', 'منظومة المبيعات') . '<hr></b>',
            'headerOptions' => ['id' => 'modalHeader'],
            'id' => 'modal',
            'size' => Modal::SIZE_LARGE,
             //keeps from closing modal with esc key or by clicking out of the modal.
             // user must click cancel or X to close
            'clientOptions' => ['backdrop' => 'static', 'keyboard' => true]
        ]);
            echo "<div id='modalContent'></div>";
            Modal::end();
    ?>

     <?= \ibrarturi\scrollup\ScrollUp::widget([
    	'theme' => 'pill',   // pill, link, image, tab
    ]); ?>
    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
