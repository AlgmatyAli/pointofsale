<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?> 
<br><br><br>
    <p>
      <button class='btn btn-danger' onclick="printContent('div1')"><?= Yii::t('app', 'Print User')?></button>
      <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <div id='div1' class="site-about">
     <center><h3>بيانات المستخديمن المسجلين بالمنظومة حتى تاريخ <?php echo date("Y/m/d") ; ?></h3></center>
     <br>
     
      <table class="table table-hover ">
      <thead>
       <tr>
        <th>#</th>
        <th>رقم المستخدم</th>
        <th>اسم المستخدم</th>
        <th>رقم الهاتف</th>
        <th>البريد الالكتروني</th>
        <th>تاريخ التسجيل</th>
        <th>حالة المستخدم</th>
       </tr>
      </thead>
       
       <?php 
         $count=1;
         foreach ($users as $user):
         $id = $user->id;
       ?>
       <tbody>
          <tr>
           <td><?=$count++?></td>
           <td><?= $user->id ?></td>
           <td><?= $user->username?></td>
           <td><?= $user->phone?></td>
           <td><?= $user->email?></td>
           <td><?= $user->createedDate?></td>
           <td><?= $user->isActive?></td>
          </tr>
       </tbody>
       <?php endforeach; ?>
    </table>
    
</div>