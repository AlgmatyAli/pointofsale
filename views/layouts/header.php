<?php
use yii\helpers\Html;
use app\models\User;
use yii\helpers\Url;
/* @var $this \yii\web\View */
/* @var $content string */
?>
<?php  $path = User::find()->select(['path','username'])->where(['id'=>Yii::$app->user->identity->id])->one(); 
?>
<header class="main-header">

    <?= Html::a('<span class="logo-mini">Z</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>
         
    <nav class="navbar navbar-static-top " role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav ">  
            
            <!-- <form class="navbar-form navbar-left" role="search">
              <div class="form-group">
                <input type="text" class="form-control" id="navbar-search-input" placeholder="Search">
              </div>
            </form> -->

            <!-- Collect the nav links, forms, and other content for toggling -->
            <!-- <ul class="nav navbar-nav ">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-fw fa-plus"></i>  <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="?r=claim-form/create">تسجيل فاتورة مبيعات</a></li>
                  <li class="divider"></li>
                  <li><a href="#">Policy</a></li>
                  <li class="divider"></li>
                  <li><a href="?r=ehealth-care">E Health Care Card</a></li>
                </ul>
              </li>
            </ul> -->
            <!--  -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $path["path"]  ?>" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?=$path["username"]?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= $path["path"]?>"  class="img-circle" alt="User Image"/>
                            <p>
                               <?=$path["username"]?>
                                <small><?= date('Y-m-d');?></small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="col-xs-4 text-center">
                                <a href="#"></a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#"></a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#"></a>
                            </div>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                            <?= Html::a(
                                    Yii::t('app', 'Change Password'),
                                    ['/user/changepassword'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                                <!-- <a href="?r=user/changepassword" class="btn btn-default btn-flat">
                                Change Password</a> -->
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    Yii::t('app', 'Sign out'),
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-arrow-left"></i></a>
                </li>
                
            </ul>
        </div>
    </nav>
</header>
