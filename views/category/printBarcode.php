<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Barcode Pront</title>
  <style>
   
    @page { 
      size: 4cm 3cm;
      margin-left: 0.06cm; 
      } /* output size */
    body.receipt .sheet {
       width: 4cm; height: 3cm; 
       margin-left: 0.06cm;        
      } /* sheet size */
    @media print { 
      body.receipt {
         width: 4cm ; height: 3cm;
          margin-left: 0.06cm; 
        }
         } 
      } /* fix for Chrome */
  </style>
</head>

<body class="receipt">
<section class="sheet">
<div class="fonts">
<?= $model->name ?>
</div>
<div id="showBarcode">

<?php use barcode\barcode\BarcodeGenerator as BarcodeGenerator;
       $Array = array(
           'elementId'=> 'showBarcode', /* div or canvas id*/
           'value'=> '0-'.$model->serialNo, /* value for EAN 13 be careful to set right values for each barcode type */
           'type'=>'code39',/*supported types  ean8, ean13, upc, std25, int25, code11, code39, code93, code128, codabar, msi, datamatrix*/ 
           ); 
       echo BarcodeGenerator::widget($Array);
  ?>
<div>
   
</div>


</div><!--This element id should  be passed on to options-->

</div>
  </section>


</body>
    <script type="text/javascript">
      window.onload = function() { window.print(); }
    </script>
</html>


