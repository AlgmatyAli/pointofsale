function printContent(el)
{
   var restorepage = document.body.innerHTML;
   var printcontent = document.getElementById(el).innerHTML;
   //window.open('', 'Print', 'height=600,width=800');

   document.body.innerHTML = printcontent;
   window.print();
   document.body.innerHTML = restorepage;
}
