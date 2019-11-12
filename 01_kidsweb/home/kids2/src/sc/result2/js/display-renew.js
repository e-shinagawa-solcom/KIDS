(function () {
    $('img.renew.button').on('click', function () {
        // 納品書修正画面
        var url = '/sc/regist2/renew.php';

        var lngslipno = 'lngSlipNo=' + $(this).attr('lngslipno');
        var lngrevisionno = 'lngRevisionNo=' + $(this).attr('revisionno');
        var strslipcode = 'strSlipCode=' + $(this).attr('strslipcode');
        var lngsalesno = 'lngSalesNo=' + $(this).attr('lngsalesno');
        var strsalescode = 'strSalesCode=' + $(this).attr('strsalescode');
        var strcustomercode = 'strCustomerCode=' + $(this).attr('strcustomercode');
        var sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        $('input[name="locked"]').val('');

        url = url + '?' + lngslipno
            + '&' + lngrevisionno
            + '&' + strslipcode
            + '&' + lngsalesno
            + '&' + strsalescode
            + '&' + strcustomercode
            + '&' + sessionID;
        // 納品書修正画面を別ウィンドウで表示
        var w = open(url,
            'display-renew',
            'width=1011, height=650, resizable=yes, scrollbars=yes, menubar=no');
        // サブウインドウズを閉じる前のイベント
        w.addEventListener("beforeunload", function (event) {
            var locked = $('input[name="locked"]').val();
            if (locked == "1") {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { strMode: "releaseLock" },
                    async: true,
                }).done(function (data) {
                    console.log(data);

                }).fail(function (error) {
                    console.log("fail");
                    console.log(error);
                });
            }
        });
    });
})();

function getUrlVars() {
    var vars = {};
    var param = location.search.substring(1).split('&');
    for (var i = 0; i < param.length; i++) {
        var keySearch = param[i].search(/=/);
        var key = '';
        if (keySearch != -1) key = param[i].slice(0, keySearch);
        var val = param[i].slice(param[i].indexOf('=', 0) + 1);
        if (key != '') vars[key] = decodeURI(val);
    }
    return vars;
}