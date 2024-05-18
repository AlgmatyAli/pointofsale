function saveData(id,salePrice,costPrice,serialNo,category) {
      
     
      $.ajax({
           
              type: "POST",
              url: "?r=temp-invoice/save&id="+id+"&salePrice="+salePrice+"&costPrice="+costPrice,
              data: { 'save_id' : id },
              contentType: "application/json; charset=utf-8",
              dataType: "json",
              success: function (data) {
                 console.log(data);
                  $('#invoice').append(
                
                  "<tr id="+ data +">"+
                                    
                  "<td>"+data +"</td>"+

                   "<td data-field='name' data-footer-formatter='nameFormatter'>"+ category +"</td>"+
                  
                   "<td>"+ serialNo +"</td>"+
                  
                   "<td ><input id='qyt-"+data.id+"'  class='form-control' type='number'  min='1' max='100'  type='text' name='qyt' value="+1+" onfocusout='updateQyt(this)' /></td>"+
                  
                   "<td id='saleP-"+data.id+"' >"+ salePrice +"</td>"+
                  
                   "<td id='salePrice-"+data.id+"' class='salePrice"+data.id+" salePrice' data-field='total' data-footer-formatter='totalFormatter'>"+ salePrice  * 1 +"</td>"+
                  
                   "<td><button type='button' class='btn btn-danger' onclick='deleteRow(this)'><i class='fa fa-trash' aria-hidden='true'></i></button></td>"+

                  "</tr>"
                
                
                  );
                
                
            var  total = document.getElementById("total").innerHTML;
            
            calcTotal();
                 


              },
              error: function (errormessage) {
  
                  //do something else
                  alert("not working");
  
              }
          });
  
  };



function deleteRow(r) {
      var id = r.parentNode.parentNode.id;
            
      $.ajax({ 
      type: "POST",
              url: "?r=temp-invoice/deleteing&id="+id,
              data: { 'save_id' : id },
              contentType: "application/json; charset=utf-8",
              dataType: "json",
              success: function (data) {
             var i = r.parentNode.parentNode.rowIndex;
     
             document.getElementById("myTable").deleteRow(i);
             calcTotal();
            },
            error: function (errormessage) {

            
            alert("not Deleted");

            }
      }
)};

function updateQyt(r) {
      var id = r.parentNode.parentNode.id;
      var qyt = document.getElementById("qyt-"+id).value;
      var salePrice = document.getElementById("saleP-"+id).innerHTML; 

      $.ajax({ 
      type: "POST",
              url: "?r=temp-invoice/updatqyt&id="+id+"&qyt="+qyt,
              data: { 'save_id' : id },
              contentType: "application/json; charset=utf-8",
              dataType: "json",
              success: function (data) {
             var i = r.parentNode.parentNode.rowIndex;
    
              document.getElementById('salePrice-'+id).innerHTML=qyt*salePrice;  
                    
             calcTotal();
      
            },
            error: function (errormessage) {

                  alert("not Updated"); 

            }
      }
)};




 function calcTotal(e){ 
      
      var sum_total_data = 0;
      var counts = 0 
       $("tr .salePrice").each(function(index,value){
         getEachRow = parseFloat($(this).text());
        
        
         sum_total_data += getEachRow;
         counts =counts+1;
        

       });
       document.getElementById('total').innerHTML = sum_total_data;
       document.getElementById('totalUp').innerHTML = sum_total_data;
       document.getElementById('counts').innerHTML = counts;
      
  }
;