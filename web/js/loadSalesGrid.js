$(function(){

$( ".kv-editable-form" ).submit(function( event ) {
    setTimeout(() => {  $.pjax({container: '#w0'}); }, 1000);
  });

  


  $( ".kv-editable-submit" ).click(function( event ) {
    setTimeout(() => {  $.pjax({container: '#w0'}); }, 1000);
  });
});


