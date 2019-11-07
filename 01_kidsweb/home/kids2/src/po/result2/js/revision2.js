//
// revision2.js
//
$(window).on('load', function () {
    var records = $('tbody tr');
    var orderCode = "";
    $.each(records, function (i, tr) {
        if (!orderCode) {
            orderCode = $(tr).children('td.td-strordercode').text();
        }
        var nextTr = $(tr).next();
        var nextOrderCode = $(nextTr).children('td.td-strordercode').text();
        if (orderCode === nextOrderCode) { return true; }
        if (checkOrderCodeBase(orderCode, nextOrderCode)) {
            $(nextTr).hide();
            changeRowNum();
            return true;
        }
        orderCode = nextOrderCode;
    });

    function checkOrderCodeBase(od1, od2) {
        return od1.split('_')[0] === od2.split('_')[0];
    }
    function changeRowNum() {
        $('tbody tr').find('td.rownum:visible').each(function (idx) {
            var i = idx + 1;
            $(this).html(i);
            var tr = $(this).parent();
            $(tr).removeClass('odd');
            $(tr).removeClass('even');
            if (i % 2) {
                $(tr).addClass('odd')
            } else {
                $(tr).addClass('even');
            }
        });
    }

});