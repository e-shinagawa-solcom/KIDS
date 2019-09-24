//
// revision.js
//
$(window).on('load', function(){
    var records = $('tbody tr');
    var orderCode = "";
    $.each(records, function(i, tr){
        if(!orderCode){
            orderCode = $(tr).children('td.td-strordercode').text();    
        }
        var nextTr = $(tr).next();
        var nextOrderCode = $(nextTr).children('td.td-strordercode').text();
        if(orderCode === nextOrderCode) { return true; }
        if(checkOrderCodeBase(orderCode, nextOrderCode)){
            $(nextTr).hide();
            changeRowNum();
            return true;
        }
        orderCode = nextOrderCode;
    });

    function checkOrderCodeBase(od1, od2){
        return od1.split('_')[0] === od2.split('_')[0];
    }
    function changeRowNum(){
        $('tbody tr').find('td.rownum:visible').each(function(idx){
            var i = idx + 1;
            $(this).html(i);
            var tr = $(this).parent();
            $(tr).removeClass('odd');
            $(tr).removeClass('even');
            if(i % 2){
                $(tr).addClass('odd')
            } else {
                $(tr).addClass('even');
            }
        });
    }

    // events
    $('img.record.button').on('click', function(){
        // console.log("ÍúÎò¥Ü¥¿¥ó");
        var orderCode = $(this).attr('strordercode').split('_');
        var revision = ("00" + (parseInt(orderCode[1], 10) - 1)).slice(-2);
        var childOrderCode = orderCode[0] + '_' + revision;
        // var childOrderCode = orderCode[0] + '_' + (parseInt(orderCode[1], 10) - 1);
        var tds = $('td.td-strordercode');
        $.each(tds, function(i, td){
            // console.log($(td)[0]);
            var oc = $(td).attr('baseordercode');
            if(orderCode[0] !== oc) { return true; }
            if($(td).text() === childOrderCode){
                var tr = $(td).parent();
                $(tr).show();
                changeRowNum();
            }
        });
        $(this).remove();
    });
});