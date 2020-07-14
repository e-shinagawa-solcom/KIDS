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
        var sessionID = 'strSessionID=' + getUrlVars(window.location)["strSessionID"];
        var sortList = 'sortList=' + setSortList($('#result thead').eq(0).find("tr:first th"));
        var childSortList = 'childSortList=' + setSortList($(".tablesorter-child thead").eq(0).find("tr:first th"));

        $('input[name="locked"]').val('');

        url = url + '?' + lngslipno
            + '&' + strslipcode
            + '&' + lngsalesno
            + '&' + lngrevisionno
            + '&' + strsalescode
            + '&' + strcustomercode
            + '&' + sessionID
            + '&' + sortList
            + '&' + childSortList;
        // 納品書修正画面を別ウィンドウで表示
        var w = open(url,
            'display-renew',
            'width=1011, height=520, resizable=yes, scrollbars=yes, menubar=no');
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