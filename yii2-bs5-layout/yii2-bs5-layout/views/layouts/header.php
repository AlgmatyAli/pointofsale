<?php

use yii\bootstrap5\Nav;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var string $companyName */

$username = Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->username;
?>
<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">

        <!-- زر طي/فتح الشريط الجانبي -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"
                   aria-label="<?= Html::encode(Yii::t('app', 'Toggle navigation')) ?>">
                    <i class="bi bi-list fs-5"></i>
                </a>
            </li>
        </ul>

        <!-- قائمة المستخدم (ms-auto تُعكس تلقائياً في RTL فتظهر في الطرف المقابل) -->
        <?= Nav::widget([
            'options' => ['class' => 'navbar-nav ms-auto'],
            'items' => [
                [
                    'label' => '<i class="bi bi-person-circle me-1"></i>' . Html::encode($username),
                    'encode' => false,
                    'dropdownOptions' => ['class' => 'dropdown-menu-end'],
                    'items' => [
                        [
                            'label' => Yii::t('app', 'Change Password'),
                            'url' => ['/user/changepassword'],
                            'linkOptions' => ['data-method' => 'post'],
                        ],
                        [
                            'label' => Yii::t('app', 'Sign out'),
                            'url' => ['/site/logout'],
                            'linkOptions' => ['data-method' => 'post'],
                        ],
                    ],
                ],
            ],
        ]) ?>

    </div>
</nav>
