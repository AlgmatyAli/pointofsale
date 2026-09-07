$(function () {
  $(".popup").click(function () {
    $("#modal").modal("show").find("#modalContent").load($(this).attr("value"));
    $.fn.modal.Constructor.prototype.enforceFocus = $.noop;
  });
});

$(function () {
  $(".popupModal").click(function (e) {
    e.preventDefault();
    $("#modal").modal("show").find(".modal-content").load($(this).attr("href"));
  });
});
// ========================
// "kartik-v/yii2-widget-datecontrol"
var $hasDateControl = $(this).find("[data-krajee-datecontrol]");
if ($hasDateControl.length > 0) {
  $hasDateControl.each(function () {
    var id = $(this).attr("id");
    var dcElementOptions = eval($(this).attr("data-krajee-datecontrol"));
    if (id.indexOf(dcElementOptions.idSave) < 0) {
      // initialize the NEW DateControl element
      var cdNewOptions = $.extend(true, {}, dcElementOptions);
      cdNewOptions.idSave = $(this).next().attr("id");
      $(this).kvDatepicker(eval($(this).attr("data-krajee-kvdatepicker")));
      $(this).removeAttr("value name data-krajee-datecontrol");
      $(this).datecontrol(cdNewOptions);
    }
  });
}
// ========================
function printContent(el) {
  var restorepage = document.body.innerHTML;
  var printcontent = document.getElementById(el).innerHTML;
  //window.open('', 'Print', 'height=600,width=800');

  document.body.innerHTML = printcontent;
  window.print();
  document.body.innerHTML = restorepage;
}
// ========================
function netTotalsPurchases() {
  var paid = $("#purchases-paid").val();
  var total = $("#purchases-total").val();
  var netTotal = 0;

  netTotal = parseFloat(total) - parseFloat(paid);
  if (paid != "") {
    $("#purchases-netTotal").val(parseFloat(netTotal));
  }
}
// ========================
function netTotalSales() {
  var paid = $("#sales-paid").val();
  var total = $("#sales-total").val();
  var netTotal = 0;

  netTotal = parseFloat(total) - parseFloat(paid);
  if (paid != "") {
    $("#sales-netTotal").val(parseFloat(netTotal));
  }
}
// ========================
function totalsCalculate() {
  id = 0;
  suma = 0;
  sumaMIN = 0;
  existe = true;
  while (existe) {
    var costPrice = "purchasesdetails-" + id + "-costprice";
    var quantity = "purchasesdetails-" + id + "-quantity";
    var total = "purchasesdetails" + id + "item_total";

    try {
      if (document.getElementById(costPrice).value != "") {
        suma =
          suma +
          parseFloat(document.getElementById(costPrice).value) *
            parseFloat(document.getElementById(quantity).value);
        total =
          parseFloat(document.getElementById(costPrice).value) *
          parseFloat(document.getElementById(quantity).value);
        $("#purchasesdetails-" + id + "-item_total").val(total);
        $("#purchases-total").val(suma);
        $("#purchases-netTotal").val(suma);
      }
      if (document.getElementById(quantity).value != "") {
        sumaMIN = sumaMIN + parseInt(document.getElementById(quantity).value);
        id = id + 1;
      }
    } catch (e) {
      existe = false;
    }
  }
}

// ========================
function totalsSalesCalculate() {
  id = 0;
  suma = 0;
  sumaMIN = 0;
  existe = true;

  while (existe) {
    var select2 = "salesdetails-" + id + "-category";
    var saleprice = "salesdetails-" + id + "-saleprice";
    var quantity = "salesdetails-" + id + "-quantity";
    try {
      if (document.getElementById(saleprice).value != "") {
        suma =
          suma +
          parseFloat(document.getElementById(saleprice).value) *
            parseFloat(document.getElementById(quantity).value);
        $("#sales-total").val(suma);
        $("#sales-netTotal").val(suma);
      }
      if (document.getElementById(quantity).value != "") {
        sumaMIN = sumaMIN + parseInt(document.getElementById(quantity).value);
        id = id + 1;
      }
    } catch (e) {
      existe = false;
    }
  }
}

function getInfo() {
  id = 0;
  suma = 0;
  sumaMIN = 0;
  existe = true;

  while (existe) {
    var select2 = "salesdetails-" + id + "-category";
    var saleprice = "salesdetails-" + id + "-saleprice";
    var quantity = "salesdetails-" + id + "-quantity";

    var category = document.getElementById(select2).value;
    try {
      if (document.getElementById(select2).value != "") {
        $.get(
          "index.php?r=sales/get-prices",
          { id: category },
          function (data) {
            data = $.parseJSON(data);

            if (data.id != "") {
              console.log(data.minPrice);
            } else {
              console.log("no data");
            }

            $("#salesdetails-" + id + "-saleprice").val(data.minPrice);
          },
        );
      }
      if (document.getElementById(saleprice).value != "") {
        sumaMIN = sumaMIN + parseInt(document.getElementById(quantity).value);
        id = id + 1;
      }
    } catch (e) {
      existe = false;
    }
  }
}

function getInv() {
  $("#tempinvoicepurchase-category").change(function () {
    var category = $(this).val();
    $.get(
      "index.php?r=temp-invoice-purchase/get-inv",
      { category: category },
      function (data) {
        var data = $.parseJSON(data);
        $("#tempinvoicepurchase-quantity").attr("value", data.quantity);
        $("#tempinvoicepurchase-costprice").attr("value", data.costPrice);
        $("#tempinvoicepurchase-saleprice").attr("value", data.maxPrice);
        $("#tempinvoicepurchase-saleprice_").attr("value", data.minPrice);
        $("#tempinvoicepurchase-saleprice_2").attr("value", data.minPrice2);
        $("#tempinvoicepurchase-saleprice_3").attr("value", data.minPrice3);
        $("#category-serialno").attr("value", data.serialNo);
      },
    );
  });
}

function totalRate() {
  var rate = 0;
  var totalinvoice = document.getElementById(
    "tempinvoicepurchase-totalinvoice",
  ).value;
  var totalcost = document.getElementById(
    "tempinvoicepurchase-totalcost",
  ).value;
  rate =
    parseFloat(parseFloat(totalinvoice) + parseFloat(totalcost)) /
    parseFloat(totalinvoice);
  $("#tempinvoicepurchase-rate").attr("value", rate.toFixed(3));
}

function totalCost() {
  var total = 0;
  var exchange = 0;
  var derhamRarte = document.getElementById(
    "tempinvoicepurchase-derhamrate",
  ).value;
  var costprice = document.getElementById(
    "tempinvoicepurchase-costprice",
  ).value;

  if (derhamRarte != 0) {
    exchange = parseFloat(costprice) / parseFloat(derhamRarte);
    $("#tempinvoicepurchase-costprice").val(exchange.toFixed(3));
  }

  var costprice = document.getElementById(
    "tempinvoicepurchase-costprice",
  ).value;
  var rate = document.getElementById("tempinvoicepurchase-rate").value;

  if (rate == 0) {
    total = parseFloat(costprice);
  } else {
    total = parseFloat(parseFloat(costprice) * parseFloat(rate));
  }

  $("#tempinvoicepurchase-costtotal").attr("value", total.toFixed(3));
}

function profit() {
  var total = 0;
  var costprice = document.getElementById(
    "tempinvoicepurchase-costtotal",
  ).value;
  var profit = document.getElementById("tempinvoicepurchase-profit").value;
  total = parseFloat(parseFloat(costprice) * parseFloat(profit / 100));
  total = parseFloat(total) + parseFloat(costprice);
  $("#tempinvoicepurchase-saleprice").attr("value", total.toFixed(3));
  $("#tempinvoicepurchase-saleprice_").attr("value", total.toFixed(3));
  $("#tempinvoicepurchase-saleprice_2").attr("value", total.toFixed(3));
  $("#tempinvoicepurchase-saleprice_3").attr("value", total.toFixed(3));
}

function getData() {
  $("#category-id").change(function () {
    var category = $(this).val();
    $.get(
      "index.php?r=category/get-data",
      { category: category },
      function (data) {
        var data = $.parseJSON(data);
        $("#category-serialno").attr("value", data.serialNo);
        $("#category-name").attr("value", data.name);
      },
    );
  });
}

function main() {
  var fraction = document.getElementById("receipt-value").value.split(".");

  if (fraction.length == 2) {
    document.getElementById("receipt-tafqet").innerHTML =
      tafqeet(fraction[0]) + " دينار و " + tafqeet(fraction[1]) + " درهم ";
  } else if (fraction.length == 1) {
    document.getElementById("receipt-tafqet").innerHTML =
      tafqeet(fraction[0]) + " دينار ";
  }
}

function getSalary() {
  $("#empsalary-employee").change(function () {
    var user_id = $(this).val();

    $.get(
      "index.php?r=employee/get-salary",
      { user_id: user_id },
      function (data) {
        var data = $.parseJSON(data);
        $("#empsalary-salary").val(data.salary);
      },
    );
  });
}

// $(document).ready(function(){
//     $("body").addClass("sidebar-collapse");
// });

$(document).on("select2:open", () => {
  document.querySelector(".select2-search__field").focus();
});

$(".check-all").click(function () {
  var selector = $(this).is(":checked") ? ":not(:checked)" : ":checked";
  $('#root-container-id input[type="checkbox"]' + selector).each(function () {
    $(this).trigger("click");
  });
});

function getKestVal() {
  var total = 0;
  var loanVal = document.getElementById("loans-loanvalue").value;
  var parts = document.getElementById("loans-parts").value;
  total = parseFloat(parseFloat(loanVal) / parseFloat(parts));
  $("#loans-kestvalue").attr("value", total.toFixed(3));
}

$("#todayis").click(function () {
  var selector = $(this).is(":checked") ? ":not(:checked)" : ":checked";
  if (selector != ":checked") {
    var today = new Date();
    var formattedDate = today.toISOString().substr(0, 10);
    $("#sales-deleviryat").val(formattedDate);
  } else {
    $("#sales-deleviryat").val("");
  }
});

$(document).ready(function () {
  $("#temp-invoice-form").submit(function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    var form = $(this);

    $.ajax({
      url: form.attr("action"),
      type: "POST",
      data: form.serialize(),
      success: function (response) {
        if (response.success) {
          // 1. إعادة تحميل الأجزاء المطلوبة عبر Pjax
          $.pjax.reload({ container: "#pjax-grid-view", async: false });
          $.pjax
            .reload({
              container: "#w0",
              url: "?r=temp-invoice/create",
              async: true,
            })
            .done(function () {
              // إعادة التركيز على Select2 بعد تحديث الـ Pjax إذا لزم الأمر
              $("#kind").select2("open");
            });

          // 2. تحديث العداد
          $("#submit-counter").text(response.counter);

          // 3. تفريغ جميع مدخلات النص والمدخلات المخفية
          form.find("input[type='text'], input[type='hidden']").val("");

          // 4. تفريغ حقل Select2 بالشكل الصحيح ليسمح باختيار نفس العنصر مجدداً
          $("#kind").val(null).trigger("change");
        } else {
          swal({
            title: "منظومة المبيعات",
            text: response.message,
            type: "warning",
            confirmButtonText: "حسنا",
          });
        }
      },
      error: function () {
        swal({
          title: "منظومة المبيعات",
          text: "حدث خطأ اثناء التخزين",
          type: "error", // تم تصحيح الكلمة من wrong إلى error
          confirmButtonText: "حسنا",
        });
      },
    });
  });
});

// $(document).ready(function () {
//   $("#temp-invoice-form").submit(function (e) {
//     e.preventDefault();
//     e.stopImmediatePropagation();
//     var form = $(this);
//     $.ajax({
//       url: form.attr("action"),
//       type: "POST",
//       data: form.serialize(),
//       success: function (response) {
//         if (response.success) {
//           $.pjax.reload({ container: "#pjax-grid-view", async: false });
//           $.pjax.reload({
//             container: "#w0",
//             url: "?r=temp-invoice/create",
//             async: true,
//           });
//           alert();
//           var $el = $(this);
//           setTimeout(function () {
//             $el.val(null).trigger("change.select2");
//           }, 100);
//           counter = response.counter;
//           $("#submit-counter").text(counter);
//           $("input").each(function () {
//             $(this).val("");
//           });
//         } else {
//           swal({
//             title: "منظومة المبيعات",
//             text: response.message,
//             type: "warning",
//             confirmButtonText: "حسنا",
//           });
//         }
//       },
//       error: function () {
//         swal({
//           title: "منظومة المبيعات",
//           text: "حدث خطأ اثناء التخزين",
//           type: "wrong",
//           confirmButtonText: "حسنا",
//         });
//       },
//     });
//   });
// });

$(document).ready(function () {
  $("#temp-invoice-purchase-form").submit(function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    var form = $(this);
    $.ajax({
      url: form.attr("action"),
      type: "POST",
      data: form.serialize(),
      success: function (response) {
        if (response.success) {
          //$.pjax.reload({ container: '#pjax-grid-view', async: true });
          $.pjax.reload({
            container: "#w0",
            url: "?r=temp-invoice-purchase/create",
            async: true,
          });
          $("#tempinvoicepurchase-quantity").val("");
          $("#tempinvoicepurchase-costprice").val("");
          $("#tempinvoicepurchase-saleprice").val("");
          $("#tempinvoicepurchase-saleprice_").val("");
          $("#tempinvoicepurchase-saleprice_2").val("");
          $("#tempinvoicepurchase-saleprice_3").val("");
        } else {
          swal({
            title: "منظومة المبيعات",
            text: response.message,
            type: "warning",
            confirmButtonText: "حسنا",
          });
        }
      },
      error: function () {
        swal({
          title: "منظومة المبيعات",
          text: "حدث خطأ اثناء التخزين",
          type: "wrong",
          confirmButtonText: "حسنا",
        });
      },
    });
  });
});

// $("#kind").on("change", function () {
//   var data = $(this).select2("data");
//   alert(data[0].branchId);
//   $("#branch").val("");
//   $("#branch").val(data[0].branchId);
// });

$("#kind").on("select2:select", function (e) {
  // الحصول على بيانات العنصر المحدد مباشرة من حدث Select2
  var data = e.params.data;

  $("#branch").val(data.branchId);
  // var $el = $(this);
  // setTimeout(function () {
  //   $el.val(null).trigger("change.select2");
  // }, 100);
});

$("#cat").on("select2:select", function (e) {
  // الحصول على بيانات العنصر المحدد مباشرة من حدث Select2
  var data = e.params.data;

  $("#branch").val(data.branchId);
  // var $el = $(this);
  // setTimeout(function () {
  //   $el.val(null).trigger("change.select2");
  // }, 100);
});

// دالة موحدة لتحديث حقل الفرع بناءً على الاختيار

// function updateBranch(e) {
//   var data = e.params.data;

//   $("#branch").val(null).trigger("change");

//   if (data && data.branchId) {
//     alert(data.branchId);
//     $("#branch").val(data.branchId).trigger("change");
//   }
// }

// function resetBranch() {
//   $("#branch").val(null).trigger("change");
// }

// $("#kind, #cat")
//   .on("select2:select", updateBranch)
//   .on("select2:unselect", resetBranch);

// $("#kind").on("change", function () {
//   var data = $(this).select2("data");

//   // تصفير القيمة وتحديث واجهة Select2
//   $("#branch").val(null).trigger("change");

//   // وضع القيمة الجديدة إذا كانت موجودة
//   if (data && data[0] && data[0].branchId) {
//     $("#branch").val(data[0].branchId).trigger("change");
//   }
// });

// $("#cat").on("change", function () {
//   var data = $(this).select2("data");

//   // تصفير القيمة وتحديث واجهة Select2
//   $("#branch").val(null).trigger("change");

//   // وضع القيمة الجديدة إذا كانت موجودة
//   if (data && data[0] && data[0].branchId) {
//     $("#branch").val(data[0].branchId).trigger("change");
//   }
// });

// $('#delete-all-button').on('click', function(e) {
//     e.preventDefault();
//     $.ajax({
//         url: '/temp-invoice/delete-all',
//         type: 'POST',
//         success: function(response) {
//             if (response.success) {
//                 alert('All records have been deleted.');
//             } else {
//                 alert('Failed to delete all records.');
//             }
//         },
//         error: function() {
//             alert('An error occurred while deleting all records.');
//         }
//     });
// });
