/* شاشة نقطة البيع (التجزئة). الإعدادات في window.RETAIL من الصفحة. كل النصوص القادمة من الخادم تُدرج بـ textContent (لا HTML). */
(function ($) {
    'use strict';

    var R = window.RETAIL;
    var U = R.urls;
    var cart = R.cart;
    var chosenClient = null;     // العميل المختار للآجل
    var lastSaleId = null;
    var searchXhr = null;
    var searchTimer = null;
    var currentClass = '';
    var busy = false;

    try { lastSaleId = parseInt(localStorage.getItem('pos_last_sale') || '', 10) || null; } catch (e) { }

    // ---------- أدوات ----------
    function fmt(n, d) { return Number(n || 0).toFixed(d === undefined ? 3 : d); }
    function fmtQty(n) { return String(parseFloat(Number(n).toFixed(3))); }
    function csrf() { return (window.yii && yii.getCsrfToken) ? yii.getCsrfToken() : $('meta[name=csrf-token]').attr('content'); }

    function el(tag, cls, text) {
        var e = document.createElement(tag);
        if (cls) { e.className = cls; }
        if (text !== undefined && text !== null) { e.textContent = text; }
        return e;
    }

    function post(url, data) {
        return $.ajax({
            url: url, type: 'POST', dataType: 'json',
            data: $.extend({ _csrf: csrf() }, data || {})
        });
    }

    function toast(message, type) {
        if (!message) { return; }
        var t = el('div', 'toast align-items-center border-0 text-bg-' + (type || 'secondary'));
        t.setAttribute('role', 'alert');
        var wrap = el('div', 'd-flex');
        wrap.appendChild(el('div', 'toast-body', message));
        var close = el('button', 'btn-close btn-close-white me-2 m-auto');
        close.type = 'button';
        close.setAttribute('data-bs-dismiss', 'toast');
        wrap.appendChild(close);
        t.appendChild(wrap);
        document.getElementById('toasts').appendChild(t);
        var inst = new bootstrap.Toast(t, { delay: type === 'danger' ? 6000 : 3500 });
        t.addEventListener('hidden.bs.toast', function () { t.remove(); });
        inst.show();
    }

    var audio = null;
    function beep(ok) {
        try {
            audio = audio || new (window.AudioContext || window.webkitAudioContext)();
            var o = audio.createOscillator(), g = audio.createGain();
            o.frequency.value = ok ? 880 : 220;
            g.gain.value = 0.08;
            o.connect(g); g.connect(audio.destination);
            o.start(); o.stop(audio.currentTime + (ok ? 0.06 : 0.25));
        } catch (e) { }
    }

    function focusScan() {
        var s = document.getElementById('scan');
        if (s && !document.querySelector('.modal.show')) { s.focus(); }
    }

    function showMsg(text, type) {
        var m = document.getElementById('scan-msg');
        m.className = 'small mt-1 text-' + (type || 'muted');
        m.textContent = text || '';
    }

    // ---------- السلة ----------
    function applyResult(res, okBeep) {
        if (res && res.cart) { cart = res.cart; renderCart(); }
        if (res && res.message) { toast(res.message, res.success ? 'warning' : 'danger'); }
        if (okBeep !== false) { beep(!!(res && res.success)); }
    }

    function ajaxFail(xhr) {
        var msg = 'تعذر الاتصال بالخادم.';
        if (xhr && xhr.status === 403) { msg = 'لا تملك صلاحية هذه العملية.'; }
        if (xhr && xhr.status === 400) { msg = 'انتهت الجلسة أو الطلب غير صالح، حدّث الصفحة.'; }
        toast(msg, 'danger');
        beep(false);
    }

    function renderCart() {
        var tbody = document.getElementById('cart-rows');
        tbody.textContent = '';
        var has = cart.items.length > 0;
        document.getElementById('cart-empty').classList.toggle('d-none', has);
        document.getElementById('cart-table').classList.toggle('d-none', !has);
        document.getElementById('btn-pay').disabled = !has;
        document.getElementById('cart-total').textContent = fmt(cart.total);
        document.getElementById('cart-count').textContent = has ? '(' + cart.count + ' صنف)' : '';
        document.getElementById('holded-count').textContent = cart.holded || 0;

        cart.items.forEach(function (it) {
            var tr = el('tr');
            var nameTd = el('td');
            nameTd.appendChild(el('div', 'fw-semibold', it.name));
            if (it.serialNo) { nameTd.appendChild(el('div', 'text-muted small', it.serialNo)); }
            tr.appendChild(nameTd);

            var qtyTd = el('td', 'text-center');
            var qty = el('input', 'form-control form-control-sm text-center cart-qty');
            qty.type = 'number'; qty.step = 'any'; qty.min = '0'; qty.value = fmtQty(it.qty);
            qty.dataset.id = it.id;
            qtyTd.appendChild(qty);
            tr.appendChild(qtyTd);

            var priceTd = el('td', 'text-end');
            if (R.canEditPrice) {
                var pr = el('input', 'form-control form-control-sm text-end cart-price');
                pr.type = 'number'; pr.step = 'any'; pr.min = '0'; pr.value = fmt(it.price);
                pr.dataset.id = it.id;
                priceTd.appendChild(pr);
            } else {
                priceTd.textContent = fmt(it.price);
            }
            tr.appendChild(priceTd);

            tr.appendChild(el('td', 'text-end fw-bold', fmt(it.total)));

            var act = el('td', 'text-end text-nowrap');
            var minus = el('button', 'btn btn-outline-secondary btn-sm cart-minus');
            minus.type = 'button'; minus.dataset.id = it.id; minus.title = 'إنقاص';
            minus.innerHTML = '<i class="bi bi-dash"></i>';
            var del = el('button', 'btn btn-outline-danger btn-sm ms-1 cart-del');
            del.type = 'button'; del.dataset.id = it.id; del.title = 'حذف';
            del.innerHTML = '<i class="bi bi-trash"></i>';
            act.appendChild(minus); act.appendChild(del);
            tr.appendChild(act);
            tbody.appendChild(tr);
        });

        var body = document.getElementById('cart-body');
        body.scrollTop = body.scrollHeight;
    }

    function cartAction(url, data, okBeep) {
        if (busy) { return $.Deferred().reject().promise(); }
        busy = true;
        return post(url, data).done(function (res) { applyResult(res, okBeep); })
            .fail(ajaxFail)
            .always(function () { busy = false; });
    }

    $(document).on('click', '.cart-minus', function () { cartAction(U.minus, { id: $(this).data('id') }, false).always(focusScan); });
    $(document).on('click', '.cart-del', function () { cartAction(U.delete, { id: $(this).data('id') }, false).always(focusScan); });
    $(document).on('change', '.cart-qty', function () { cartAction(U.qty, { id: $(this).data('id'), qty: this.value }).always(focusScan); });
    $(document).on('change', '.cart-price', function () { cartAction(U.price, { id: $(this).data('id'), price: this.value }).always(focusScan); });
    $(document).on('focus', '.cart-qty, .cart-price', function () { this.select(); });
    $(document).on('keydown', '.cart-qty, .cart-price', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $(this).trigger('change'); }
    });

    $('#btn-clear').on('click', function () {
        if (!cart.count) { return; }
        if (!window.confirm('هل أنت متأكد من إلغاء الفاتورة؟')) { return; }
        cartAction(U.deleteAll, {}, false).always(focusScan);
    });

    // ---------- المسح والبحث ----------
    function scan(value) {
        value = $.trim(value);
        if (!value) { return; }
        if (busy) { return; }
        busy = true;
        post(U.scan, { barcode: value }).done(function (res) {
            if (res.success) {
                $('#scan').val('');
                showMsg('');
                closeSuggest();
                loadProducts('', currentClass);
                applyResult(res);
            } else {
                // ليس باركوداً مسجلاً: نبحث بالاسم، وإن طابق صنفاً واحداً فقط نضيفه مباشرة
                $('#scan').select();
                fetchProducts(value).done(function (rows) {
                    if (rows.length === 1) {
                        addFromSuggest(rows[0].id);
                    } else if (rows.length === 0) {
                        closeSuggest();
                        showMsg(res.message, 'danger');
                        beep(false);
                    } else {
                        showMsg('');
                        renderSuggest(rows);
                    }
                });
            }
        }).fail(ajaxFail).always(function () { busy = false; });
    }

    // ---------- قائمة الاقتراحات ----------
    var sgRows = [];
    var sgActive = -1;
    var sgXhr = null;

    function fetchProducts(q) {
        if (sgXhr) { sgXhr.abort(); }
        sgXhr = $.getJSON(U.products, { q: q || '', 'class': '' });
        sgXhr.fail(function (x, status) { if (status !== 'abort') { toast('تعذر تحميل الأصناف.', 'danger'); } });
        return sgXhr;
    }

    function closeSuggest() {
        sgRows = []; sgActive = -1;
        var box = document.getElementById('scan-results');
        box.textContent = '';
        box.classList.add('d-none');
    }

    function setActive(i) {
        var items = document.querySelectorAll('#scan-results .list-group-item');
        if (!items.length) { return; }
        sgActive = (i + items.length) % items.length;
        items.forEach(function (it, k) { it.classList.toggle('active', k === sgActive); });
        items[sgActive].scrollIntoView({ block: 'nearest' });
    }

    function renderSuggest(rows) {
        var box = document.getElementById('scan-results');
        box.textContent = '';
        sgRows = rows.slice(0, 12);
        sgActive = -1;
        if (!sgRows.length) {
            var none = el('div', 'list-group-item text-muted', 'لا توجد أصناف مطابقة.');
            box.appendChild(none);
            box.classList.remove('d-none');
            return;
        }
        sgRows.forEach(function (p, k) {
            var item = el('button', 'list-group-item list-group-item-action');
            item.type = 'button';
            item.dataset.index = k;
            var left = el('div');
            left.appendChild(el('div', 'sg-name', p.name));
            var meta = [];
            if (p.serialNo) { meta.push(p.serialNo); }
            if (p.company && p.company !== '-') { meta.push(p.company); }
            left.appendChild(el('div', 'sg-meta', meta.join('  •  ')));
            item.appendChild(left);
            var right = el('div', 'text-end text-nowrap');
            right.appendChild(el('div', 'fw-bold text-primary', fmt(p.price)));
            right.appendChild(el('div', 'sg-meta', 'المتوفر: ' + fmtQty(p.qty)));
            item.appendChild(right);
            box.appendChild(item);
        });
        box.classList.remove('d-none');
        setActive(0);
    }

    function addFromSuggest(categoryId) {
        closeSuggest();
        showMsg('');
        cartAction(U.add, { categoryid: categoryId, qty: 1 }).done(function (r) {
            if (r.success) { $('#scan').val(''); }
        }).always(focusScan);
    }

    $('#scan-results').on('mousedown', '.list-group-item', function (e) { e.preventDefault(); });
    $('#scan-results').on('click', '.list-group-item', function () {
        var p = sgRows[parseInt($(this).data('index'), 10)];
        if (p) { addFromSuggest(p.id); }
    });

    $('#scan').on('keydown', function (e) {
        var open = !document.getElementById('scan-results').classList.contains('d-none') && sgRows.length;
        if (e.key === 'ArrowDown' && open) { e.preventDefault(); setActive(sgActive + 1); }
        else if (e.key === 'ArrowUp' && open) { e.preventDefault(); setActive(sgActive - 1); }
        else if (e.key === 'Escape') { closeSuggest(); }
        else if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimer);
            if (open && sgActive >= 0 && sgRows[sgActive] && $.trim(this.value) !== '') {
                addFromSuggest(sgRows[sgActive].id);
            } else {
                scan(this.value);
            }
        }
    });

    $('#scan').on('input', function () {
        var v = $.trim(this.value);
        clearTimeout(searchTimer);
        if (v === '' ) { closeSuggest(); return; }
        // الباركود الطويل الرقمي أو «3*باركود» لا يُبحث عنه أثناء الكتابة السريعة (يعالجه Enter)
        if (/^\d{6,}$/.test(v) || /^\d+(\.\d+)?\*/.test(v)) { closeSuggest(); return; }
        searchTimer = setTimeout(function () {
            fetchProducts(v).done(renderSuggest);
        }, 200);
    });
    $('#scan-clear').on('click', function () { $('#scan').val(''); showMsg(''); closeSuggest(); focusScan(); });

    $('#class-tabs').on('click', '.class-tab', function () {
        $('.class-tab').removeClass('btn-primary').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
        currentClass = $(this).data('class') || '';
        loadProducts('', currentClass);
        focusScan();
    });

    function renderProducts(rows) {
        var grid = document.getElementById('product-grid');
        grid.textContent = '';
        if (!rows.length) {
            grid.appendChild(el('div', 'text-muted p-4', 'لا توجد أصناف مطابقة.'));
            return;
        }
        rows.forEach(function (p) {
            var card = el('button', 'pos-card');
            card.type = 'button';
            card.dataset.id = p.id;
            var top = el('div', 'pos-card-img');
            if (p.path) {
                var img = el('img');
                img.src = p.path; img.alt = '';
                img.onerror = function () { img.remove(); top.innerHTML = '<i class="bi bi-box-seam"></i>'; };
                top.appendChild(img);
            } else {
                top.innerHTML = '<i class="bi bi-box-seam"></i>';
            }
            card.appendChild(top);
            card.appendChild(el('div', 'pos-card-name', p.name));
            var foot = el('div', 'pos-card-foot');
            foot.appendChild(el('span', 'fw-bold text-primary', fmt(p.price)));
            foot.appendChild(el('span', 'badge rounded-pill ' + (p.qty > 0 ? 'bg-light text-dark' : 'bg-danger'), fmtQty(p.qty)));
            card.appendChild(foot);
            grid.appendChild(card);
        });
    }

    // يعيد الـ xhr ليتمكن المستدعي من انتظار النتيجة (rows)
    function loadProducts(q, cls) {
        if (searchXhr) { searchXhr.abort(); }
        var xhr = $.getJSON(U.products, { q: q || '', 'class': cls || '' });
        searchXhr = xhr;
        xhr.done(function (rows) {
            try { renderProducts(rows); } catch (e) { console.error('retail: render products failed', e); }
        }).fail(function (x, status) {
            if (status !== 'abort') { toast('تعذر تحميل الأصناف.', 'danger'); }
        });
        return xhr;
    }

    $('#product-grid').on('click', '.pos-card', function () {
        cartAction(U.add, { categoryid: $(this).data('id'), qty: 1 }).always(focusScan);
    });

    // ---------- التعليق ----------
    function hold() {
        if (!cart.count) { return; }
        cartAction(U.hold, {}, false).always(focusScan);
    }
    $('#btn-hold').on('click', hold);

    $('#btn-holded').on('click', function () {
        $.getJSON(U.holded).done(function (rows) {
            var body = document.getElementById('holded-body');
            body.textContent = '';
            if (!rows.length) { body.appendChild(el('div', 'text-muted text-center p-3', 'لا توجد فواتير معلّقة.')); }
            rows.forEach(function (h) {
                var row = el('div', 'd-flex justify-content-between align-items-center border rounded p-2 mb-2');
                row.appendChild(el('div', '', 'فاتورة #' + h.number + '  —  ' + h.lines + ' صنف  —  ' + fmt(h.total)));
                var b = el('button', 'btn btn-primary btn-sm', 'استرجاع');
                b.type = 'button';
                b.addEventListener('click', function () {
                    post(U.unhold, { number: h.number }).done(function (res) {
                        applyResult(res);
                        if (res.success) { bootstrap.Modal.getOrCreateInstance(document.getElementById('holdedModal')).hide(); }
                    }).fail(ajaxFail);
                });
                row.appendChild(b);
                body.appendChild(row);
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('holdedModal')).show();
        }).fail(ajaxFail);
    });
    document.getElementById('holdedModal').addEventListener('hidden.bs.modal', focusScan);

    // ---------- الدفع ----------
    var payModalEl = document.getElementById('payModal');
    var payModal = bootstrap.Modal.getOrCreateInstance(payModalEl);

    function netAmount() {
        var d = R.canDiscount ? Math.max(0, parseFloat($('#pay-discount').val()) || 0) : 0;
        return Math.max(0, cart.total - d);
    }

    function updatePay() {
        var net = netAmount();
        var credit = $('input[name=payWay]:checked').val() === '1';
        $('#pay-total').text(fmt(cart.total));
        $('#pay-net').text(fmt(net));
        $('#pay-cash-box').toggleClass('d-none', credit);
        $('#pay-credit-box').toggleClass('d-none', !credit);
        if (credit) {
            var paid = Math.max(0, parseFloat($('#pay-paid').val()) || 0);
            $('#pay-remaining').text(fmt(net - paid));
        } else {
            var t = parseFloat($('#pay-tendered').val());
            $('#pay-change').text(isNaN(t) ? fmt(0) : fmt(t - net));
            $('#pay-change').toggleClass('text-danger', !isNaN(t) && t < net);
        }
    }

    function quickCash() {
        var net = netAmount();
        var box = document.getElementById('quick-cash');
        box.textContent = '';
        var vals = [net];
        [5, 10, 20, 50, 100, 200].forEach(function (s) {
            var v = Math.ceil(net / s) * s;
            if (v > net && vals.indexOf(v) === -1) { vals.push(v); }
        });
        vals.slice(0, 5).forEach(function (v, i) {
            var b = el('button', 'btn btn-outline-secondary btn-sm', i === 0 ? 'المبلغ بالضبط' : fmt(v, v % 1 ? 3 : 0));
            b.type = 'button';
            b.addEventListener('click', function () { $('#pay-tendered').val(fmt(v)).trigger('input'); });
            box.appendChild(b);
        });
    }

    function openPay() {
        if (!cart.count) { return; }
        $('#pw-cash').prop('checked', true);
        $('#pay-discount').val(0);
        $('#pay-tendered').val('');
        $('#pay-paid').val(0);
        $('#pay-client').val('');
        $('#pay-client-results').empty();
        $('#pay-client-chosen').addClass('d-none').text('');
        $('#pay-error').addClass('d-none');
        chosenClient = null;
        updatePay(); quickCash();
        payModal.show();
    }
    $('#btn-pay').on('click', openPay);
    payModalEl.addEventListener('shown.bs.modal', function () { $('#pay-tendered').trigger('focus'); });
    payModalEl.addEventListener('hidden.bs.modal', focusScan);

    $('#pay-discount').on('input', function () { updatePay(); quickCash(); });
    $('#pay-tendered, #pay-paid').on('input', updatePay);
    $('input[name=payWay]').on('change', function () {
        updatePay();
        if (this.value === '1') { $('#pay-client').trigger('focus'); } else { $('#pay-tendered').trigger('focus'); }
    });

    var clientTimer = null;
    $('#pay-client').on('input', function () {
        var q = $.trim(this.value);
        clearTimeout(clientTimer);
        chosenClient = null;
        $('#pay-client-chosen').addClass('d-none');
        if (q.length < 1) { $('#pay-client-results').empty(); return; }
        clientTimer = setTimeout(function () {
            $.getJSON(U.clients, { q: q }).done(function (rows) {
                var box = document.getElementById('pay-client-results');
                box.textContent = '';
                if (!rows.length) { box.appendChild(el('div', 'list-group-item text-muted small', 'لا يوجد عميل مطابق (يظهر عملاء الآجل فقط).')); }
                rows.forEach(function (c) {
                    var b = el('button', 'list-group-item list-group-item-action', c.name + (c.phone ? '  —  ' + c.phone : ''));
                    b.type = 'button';
                    b.addEventListener('click', function () {
                        chosenClient = c;
                        $('#pay-client-chosen').removeClass('d-none').text('العميل: ' + c.name);
                        box.textContent = '';
                        $('#pay-client').val(c.name);
                        $('#pay-paid').trigger('focus');
                    });
                    box.appendChild(b);
                });
            });
        }, 250);
    });

    function showPayError(m) { $('#pay-error').removeClass('d-none').text(m); beep(false); }

    function confirmPay() {
        if (busy) { return; }
        var credit = $('input[name=payWay]:checked').val() === '1';
        var net = netAmount();
        var data = {
            payWay: credit ? 1 : 0,
            disscount: R.canDiscount ? (parseFloat($('#pay-discount').val()) || 0) : 0,
            payment_type: $('#pay-type').val() || 1
        };
        if (credit) {
            if (!chosenClient) { showPayError('اختر العميل من القائمة.'); return; }
            data.clinet = chosenClient.id;
            data.paid = parseFloat($('#pay-paid').val()) || 0;
        } else {
            var t = $('#pay-tendered').val();
            if (t !== '' && parseFloat(t) < net) { showPayError('المبلغ المستلم أقل من الصافي.'); return; }
        }
        $('#pay-error').addClass('d-none');
        busy = true;
        $('#btn-confirm-pay').prop('disabled', true);
        post(U.checkout, data).done(function (res) {
            if (!res.success) { showPayError(res.message); return; }
            payModal.hide();
            lastSaleId = res.id;
            try { localStorage.setItem('pos_last_sale', String(res.id)); } catch (e) { }
            $('#btn-reprint').prop('disabled', false);
            (res.notes || []).forEach(function (n) { toast(n, 'warning'); });
            if (res.cart) { cart = res.cart; renderCart(); }
            beep(true);
            printReceipt(res.id);
            loadProducts('', currentClass);
        }).fail(function (xhr) {
            ajaxFail(xhr);
        }).always(function () { busy = false; $('#btn-confirm-pay').prop('disabled', false); });
    }
    $('#btn-confirm-pay').on('click', confirmPay);
    $('#payModal').on('keydown', function (e) {
        if (e.key === 'Enter' && e.target.tagName !== 'BUTTON' && e.target.id !== 'pay-client') { e.preventDefault(); confirmPay(); }
    });

    // ---------- الطباعة (إيصال 80mm داخل إطار خفي) ----------
    function printReceipt(id) {
        var old = document.getElementById('receipt-frame');
        if (old) { old.remove(); }
        var f = document.createElement('iframe');
        f.id = 'receipt-frame';
        f.style.cssText = 'position:fixed;width:0;height:0;border:0;right:0;bottom:0;';
        f.src = U.receipt + (U.receipt.indexOf('?') > -1 ? '&' : '?') + 'id=' + id + '&print=1';
        document.body.appendChild(f);
    }
    $('#btn-reprint').on('click', function () { if (lastSaleId) { printReceipt(lastSaleId); } });
    if (lastSaleId) { $('#btn-reprint').prop('disabled', false); }

    // ---------- اختصارات لوحة المفاتيح ----------
    $(document).on('keydown', function (e) {
        var modalOpen = !!document.querySelector('.modal.show');
        if (e.key === 'F9') { e.preventDefault(); if (!modalOpen) { openPay(); } else { confirmPay(); } }
        else if (e.key === 'F8') { e.preventDefault(); if (!modalOpen) { hold(); } }
        else if (e.key === 'F7') { e.preventDefault(); if (!modalOpen) { $('#btn-clear').trigger('click'); } }
        else if (e.key === 'Escape' && !modalOpen) { $('#scan').trigger('focus').select(); }
    });
    // أي ضغط خارج حقول الإدخال يعيد التركيز لحقل الباركود
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#scan-wrap').length) { closeSuggest(); }
        if (!$(e.target).closest('input, select, textarea, button, a, .modal').length) { focusScan(); }
    });

    // ---------- الساعة ----------
    function clock() {
        var n = new Date();
        var p = function (x) { return (x < 10 ? '0' : '') + x; };
        var c = document.getElementById('pos-clock');
        if (c) { c.textContent = n.getFullYear() + '-' + p(n.getMonth() + 1) + '-' + p(n.getDate()) + '  ' + p(n.getHours()) + ':' + p(n.getMinutes()); }
    }
    clock(); setInterval(clock, 20000);

    // ---------- البداية ----------
    renderCart();
    loadProducts('', '');
    focusScan();
})(jQuery);
