function myFunction() {
    var input, filter, ul, li, a, i, txtValue;
   
    input = document.getElementById("categoryInput");
        if (input.value.length == 0)
        {
            $("#categotyList").hide();
        } else{ 	
            
            $("#categotyList").show(); 
        }

    filter = input.value.toUpperCase();
    ul = document.getElementById("categotyList");
    li = ul.getElementsByTagName("li");
    
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
            // $("#categotyList").show();
        } else {
            li[i].style.display = "none";
            // $("#categotyList").hide();
        }
    }
}

window.onload = function() 
    {
       $("#categotyList").hide();
    };
