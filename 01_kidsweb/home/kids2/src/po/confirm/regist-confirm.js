// <!--
// window.onbeforeunload = unLock;

function RegistPO(){
    $.ajax({
        type: 'POST',
        url: '/po/confirm/index.php?strSessionID=' + $('input[name="strSessionID"]').val(),
        data: {
            strSessionID: $('input[name="strSessionID"]').val(),
            lngOrderNo: $('input[name="lngOrderNo"]').val(),
            strMode: "insert",
            lngRevisionNo: $('input[name="lngRevisionNo"]').val(),
            lngPayConditionCode: $('input[name="lngPayConditionCode"]').val(),
            lngLocationCode: $('input[name="lngLocationCode"]').val(),
            strNote: $('input[name="strNote"]').val(),
            strProductCode: $('input[name="strProductCode"]').val(),
            strReviseCode: $('input[name="strReviseCode"]').val(),
            lngDetailCount: $('input[name="lngDetailCount"]').val(),
            aryDetail: getUpdateDetail(),
        },
        async: true,
    }).done(function (data) {
        console.log("done");
        console.log(data);
        //$('html').html(data);
        document.open();
        document.write(data);
        document.close();
    }).fail(function (error) {
        console.log("fail");
        console.log(error);
    });
}

function getUpdateDetail() {
    var result = [];
 
    for( i = 0; i < $('input[name="lngDetailCount"]').val(); i++) {
        var param = {
            lngOrderDetailNo: $(getDetailName('lngOrderDetailNo', i)).val(),
            lngSortKey: i + 1,
            lngDeliveryMethodCode: $(getDetailName('lngCarrierCode', i)).val(),
            strDeliveryMethodName: $(getDetailName('strDeliveryMethodName' ,i)).val(),
            lngProductUnitCode: $(getDetailName('lngProductUnitCode', i)).val(),
            lngOrderNo: $(getDetailName('lngOrderNo' ,i)).val(),
            lngRevisionNo: $(getDetailName('lngRevisionNo' ,i)).val(),
            lngStockSubjectCode: $(getDetailName('lngStockSubjectCode' ,i)).val(),
            lngStockItemCode: $(getDetailName('lngStockItemCode' ,i)).val(),
            strMoldNo: $(getDetailName('strMoldNo' ,i)).val(),
            lngMonetaryUnitCode: $(getDetailName('lngMonetaryUnitCode' ,i)).val(),
            lngCustomerCompanyCode: $(getDetailName('lngCustomerCompanyCode' ,i)).val(),
            curProductPrice: $(getDetailName('curProductPrice' ,i)).val(),
            lngProductQuantity: $(getDetailName('lngProductQuantity' ,i)).val(),
            curSubtotalPrice: $(getDetailName('curSubtotalPrice' ,i)).val(),
            dtmDeliveryDate: $(getDetailName('dtmDeliveryDate' ,i)).val(),
            strDetailNote: $(getDetailName('strDetailNote' ,i)).val()
        };
        var msg = 
        "lngOrderDetailNo=" + param.lngOrderDetailNo + "\n" +
            "lngSortKey=" + param.lngSortKey + "\n" +
            "lngDeliveryMethodCode=" + param.lngDeliveryMethodCode + "\n" +
            "strDeliveryMethodName=" + param.strDeliveryMethodName + "\n" +
            "lngProductUnitCode=" + param.lngProductUnitCode + "\n" +
            "lngOrderNo=" + param.lngOrderNo + "\n" +
            "lngRevisionNo=" + param.lngRevisionNo + "\n" +
            "lngStockSubjectCode=" + param.lngStockSubjectCode + "\n" +
            "lngStockItemCode=" + param.lngStockItemCode + "\n" +
            "lngMonetaryUnitCode=" + param.lngMonetaryUnitCode + "\n" +
            "lngCustomerCompanyCode=" + param.lngCustomerCompanyCode + "\n" +
            "curProductPrice=" + param.curProductPrice + "\n" +
            "lngProductQuantity=" + param.lngProductQuantity + "\n" +
            "curSubtotalPrice=" + param.curSubtotalPrice + "\n" +
            "dtmDeliveryDate=" + param.dtmDeliveryDate + "\n" +
            "strDetailNote=" + param.strDetailNote + "\n";
        result.push(param);
    }
    
    return result;
};

function getDetailName(name, rowno)
{
    var itemBasename = "aryPoDitail[";
    var ret = 'input[name="' +itemBasename + rowno + '][' + name + ']"]';
//    alert(ret);
    return ret;
}

function unLock()
{
    $.ajax({
        url: '/po/confirm/index.php',
        type: 'post',
    // dataType: 'json',
        type: 'POST',
        data: {
            'strSessionID': $('input[type="hidden"][name="strSessionID"]').val(),
            'strMode': 'cancel',
        }
    })
    .done(function (response) {
    })
    .fail(function (response) {
    });
        
    return false;
}

//-->