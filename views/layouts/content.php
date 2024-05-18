<?php
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;
?>


<div class="content-wrapper">
    <section class="content-header">
        <!-- <?php if (isset($this->blocks['content-header'])) { ?>
            <h1><?= $this->blocks['content-header'] ?></h1>
        <?php } else { ?>
            <h1>
                <?php
                if ($this->title !== null) {
                    echo \yii\helpers\Html::encode($this->title);
                } else {
                    echo \yii\helpers\Inflector::camel2words(
                        \yii\helpers\Inflector::id2camel($this->context->module->id)
                    );
                    echo ($this->context->module->id !== \Yii::$app->id) ? '<small>Module</small>' : '';
                } ?>
            </h1>
        <?php } ?> -->

        <?=
        Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
    </section>

    <section class="content">
        <?= Alert::widget() ?>
        <?= $content ?>
    </section>
</div>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy;<?=date('Y')?> <a href="http://Zain.ly" target=”_blank”>Zain</a>.</strong> All rights
    reserved.
   
</footer>

<!-- Control Sidebar -->
<!-- <aside class="control-sidebar control-sidebar-dark">
    <div class="tab-content">
    <h3><center>Claims Today</center></h3>
    <hr>
   <ul>
       
       <li>
          <a href='javascript::;'>
              <div class="menu-info">
                  <h4 class="control-sidebar-subheading"><?
                //   = $value["name"].' '.$value["family"] ?></h4><br>
              </div>
          </a>
       </li>
       
    </ul>
         
    </div>
</aside>/.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class='control-sidebar-bg'></div>