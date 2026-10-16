// ========================
// أدوات مساعدة
// ========================

// الاستجابة قد تصل نصاً (قديماً) أو كائناً جاهزاً (عند ترويسة JSON). هذه الدالة تقبل الحالتين
// ($.parseJSON مهجورة وتُحذف في jQuery 4)
function parseJson(data) {
  if (typeof data === "string") {
    return JSON.parse(data);
  }
  return data;
}

// ========================
// النوافذ المنبثقة
// المعالجات مفوَّضة على document حتى تعمل مع العناصر التي تُرسم لاحقاً
// (بعد تحديث Pjax أو داخل محتوى محمَّل بالنافذة المنبثقة).
// ملاحظة: داخل حاويات Pjax يجب أن يحمل الرابط data-pjax="0" حتى لا يعترضه Pjax قبل وصول النقرة إلى هنا.
// ========================
$(document).on("click", ".popup", function () {
  $("#modal").modal("show").find("#modalContent").load($(this).attr("value"));
  $.fn.modal.Constructor.prototype.enforceFocus = $.noop;
});

$(document).on("click", ".popupModal", function (e) {
  e.preventDefault();
  $("#modal").modal("show").find(".modal-content").load($(this).attr("href"));
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
  var netTotal = parseFloat(total) - parseFloat(paid);

  if (paid != "" && !isNaN(netTotal)) {
    $("#purchases-netTotal").val(parseFloat(netTotal));
  }
}
// ========================
function netTotalSales() {
  var paid = $("#sales-paid").val();
  var total = $("#sales-total").val();
  var netTotal = parseFloat(total) - parseFloat(paid);

  if (paid != "" && !isNaN(netTotal)) {
    $("#sales-netTotal").val(parseFloat(netTotal));
  }
}
// ========================
// إجمالي فاتورة المشتريات: نفس المعادلة (سعر التكلفة × الكمية لكل صف).
// الحلقة تتوقف عند أول صف لا توجد حقوله، وتتخطى الصف الذي كميته أو سعره فارغ
// (كانت تدور بلا نهاية إذا بقيت الكمية فارغة).
function totalsCalculate() {
  var id = 0;
  var suma = 0;

  while (true) {
    var costEl = document.getElementById("purchasesdetails-" + id + "-costprice");
    var qtyEl = document.getElementById("purchasesdetails-" + id + "-quantity");
    if (!costEl || !qtyEl) {
      break;
    }

    if (costEl.value != "" && qtyEl.value != "") {
      var rowTotal = parseFloat(costEl.value) * parseFloat(qtyEl.value);
      suma = suma + rowTotal;
      $("#purchasesdetails-" + id + "-item_total").val(rowTotal);
      $("#purchases-total").val(suma);
      $("#purchases-netTotal").val(suma);
    }
    id = id + 1;
  }
}

// ========================
// إجمالي فاتورة المبيعات: نفس المعادلة (سعر البيع × الكمية لكل صف) مع نفس حراسة الحلقة
function totalsSalesCalculate() {
  var id = 0;
  var suma = 0;

  while (true) {
    var priceEl = document.getElementById("salesdetails-" + id + "-saleprice");
    var qtyEl = document.getElementById("salesdetails-" + id + "-quantity");
    if (!priceEl || !qtyEl) {
      break;
    }

    if (priceEl.value != "" && qtyEl.value != "") {
      suma = suma + parseFloat(priceEl.value) * parseFloat(qtyEl.value);
      $("#sales-total").val(suma);
      $("#sales-netTotal").val(suma);
    }
    id = id + 1;
  }
}

// جلب السعر الأدنى لكل صف في فاتورة المبيعات (رقم الصف يُحفظ لكل طلب حتى لا تتداخل الطلبات غير المتزامنة)
function getInfo() {
  var id = 0;

  while (true) {
    var catEl = document.getElementById("salesdetails-" + id + "-category");
    var priceEl = document.getElementById("salesdetails-" + id + "-saleprice");
    if (!catEl || !priceEl) {
      break;
    }

    if (catEl.value != "") {
      (function (rowId, category) {
        $.get(
          "index.php?r=sales/get-prices",
          { id: category },
          function (data) {
            data = parseJson(data);
            if (!data) {
              return;
            }
            $("#salesdetails-" + rowId + "-saleprice").val(data.minPrice);
          },
        );
      })(id, catEl.value);
    }
    id = id + 1;
  }
}

function getInv() {
  $("#tempinvoicepurchase-category").change(function () {
    var category = $(this).val();
    $.get(
      "index.php?r=temp-invoice-purchase/get-inv",
      { category: category },
      function (data) {
        data = parseJson(data);
        if (!data) {
          return;
        }
        // .val() بدل .attr("value"): الأخيرة لا تحدّث القيمة المعروضة بعد أن يكتب المستخدم في الحقل
        $("#tempinvoicepurchase-quantity").val(data.quantity);
        $("#tempinvoicepurchase-costprice").val(data.costPrice);
        $("#tempinvoicepurchase-saleprice").val(data.maxPrice);
        $("#tempinvoicepurchase-saleprice_").val(data.minPrice);
        $("#tempinvoicepurchase-saleprice_2").val(data.minPrice2);
        $("#tempinvoicepurchase-saleprice_3").val(data.minPrice3);
        $("#category-serialno").val(data.serialNo);
      },
    );
  });
}

// معامل الفاتورة = (إجمالي الفاتورة + إجمالي المصاريف) ÷ إجمالي الفاتورة (المعادلة لم تتغير).
// لا يُكتب شيء إذا كانت النتيجة غير رقم أو لا نهائية (إجمالي فارغ أو صفر).
function totalRate() {
  var totalinvoice = document.getElementById(
    "tempinvoicepurchase-totalinvoice",
  ).value;
  var totalcost = document.getElementById(
    "tempinvoicepurchase-totalcost",
  ).value;
  var rate =
    parseFloat(parseFloat(totalinvoice) + parseFloat(totalcost)) /
    parseFloat(totalinvoice);

  if (!isFinite(rate)) {
    return;
  }
  $("#tempinvoicepurchase-rate").val(rate.toFixed(3));
}

// تكلفة الصنف بعد سعر الصرف والمعامل (المعادلة لم تتغير). لا يُكتب شيء إذا كانت النتيجة غير رقم.
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
    if (isFinite(exchange)) {
      $("#tempinvoicepurchase-costprice").val(exchange.toFixed(3));
    }
  }

  costprice = document.getElementById("tempinvoicepurchase-costprice").value;
  var rate = document.getElementById("tempinvoicepurchase-rate").value;

  if (rate == 0) {
    total = parseFloat(costprice);
  } else {
    total = parseFloat(parseFloat(costprice) * parseFloat(rate));
  }

  if (isNaN(total)) {
    return;
  }
  $("#tempinvoicepurchase-costtotal").val(total.toFixed(3));
}

// أسعار البيع = التكلفة + نسبة الربح (المعادلة لم تتغير). لا يُكتب شيء إذا كانت النتيجة غير رقم.
function profit() {
  var total = 0;
  var costprice = document.getElementById(
    "tempinvoicepurchase-costtotal",
  ).value;
  var profit = document.getElementById("tempinvoicepurchase-profit").value;
  total = parseFloat(parseFloat(costprice) * parseFloat(profit / 100));
  total = parseFloat(total) + parseFloat(costprice);

  if (isNaN(total)) {
    return;
  }
  $("#tempinvoicepurchase-saleprice").val(total.toFixed(3));
  $("#tempinvoicepurchase-saleprice_").val(total.toFixed(3));
  $("#tempinvoicepurchase-saleprice_2").val(total.toFixed(3));
  $("#tempinvoicepurchase-saleprice_3").val(total.toFixed(3));
}

function getData() {
  $("#category-id").change(function () {
    var category = $(this).val();
    $.get(
      "index.php?r=category/get-data",
      { category: category },
      function (data) {
        data = parseJson(data);
        if (!data) {
          return;
        }
        $("#category-serialno").val(data.serialNo);
        $("#category-name").val(data.name);
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
        data = parseJson(data);
        if (!data) {
          return;
        }
        $("#empsalary-salary").val(data.salary);
      },
    );
  });
}

// عند فتح أي Select2: التركيز على حقل البحث الخاص بالقائمة المفتوحة نفسها
// (كان يأخذ أول حقل بحث في الصفحة، وقد يكون لقائمة أخرى، وقد لا يوجد فيحدث خطأ)
$(document).on("select2:open", function () {
  var field = document.querySelector(
    ".select2-container--open .select2-search__field",
  );
  if (field) {
    field.focus();
  }
});

$(document).on("click", ".check-all", function () {
  var selector = $(this).is(":checked") ? ":not(:checked)" : ":checked";
  $('#root-container-id input[type="checkbox"]' + selector).each(function () {
    $(this).trigger("click");
  });
});

function getKestVal() {
  var loanVal = document.getElementById("loans-loanvalue").value;
  var parts = document.getElementById("loans-parts").value;
  var total = parseFloat(parseFloat(loanVal) / parseFloat(parts));

  if (!isFinite(total)) {
    return;
  }
  $("#loans-kestvalue").val(total.toFixed(3));
}

// التأشير: تاريخ اليوم في حقل الاستلام، وإلغاء التأشير: تفريغ الحقل (نفس السلوك السابق).
// التاريخ محلي (كان toISOString يعطي تاريخ UTC فيظهر أمس بين 12 و2 صباحاً).
$(document).on("click", "#todayis", function () {
  if (this.checked) {
    var today = new Date();
    var pad = function (n) {
      return (n < 10 ? "0" : "") + n;
    };
    var formattedDate =
      today.getFullYear() +
      "-" +
      pad(today.getMonth() + 1) +
      "-" +
      pad(today.getDate());
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
        if (response && response.success) {
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
            text: response ? response.message : "",
            type: "warning",
            confirmButtonText: "حسنا",
          });
        }
      },
      error: function () {
        swal({
          title: "منظومة المبيعات",
          text: "حدث خطأ اثناء التخزين",
          type: "error",
          confirmButtonText: "حسنا",
        });
      },
    });
  });
});

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
        if (response && response.success) {
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
            text: response ? response.message : "",
            type: "warning",
            confirmButtonText: "حسنا",
          });
        }
      },
      error: function () {
        swal({
          title: "منظومة المبيعات",
          text: "حدث خطأ اثناء التخزين",
          type: "error", // كانت "wrong" وهي قيمة غير صالحة فلا تظهر الأيقونة
          confirmButtonText: "حسنا",
        });
      },
    });
  });
});

// اختيار صنف في البحث: تعبئة حقل الفرع. مفوَّض على document فيعمل ولو رُسم الحقل بعد تحميل الملف.
$(document).on("select2:select", "#kind, #cat", function (e) {
  // الحصول على بيانات العنصر المحدد مباشرة من حدث Select2
  var data = e.params.data;

  $("#branch").val(data.branchId);
});
