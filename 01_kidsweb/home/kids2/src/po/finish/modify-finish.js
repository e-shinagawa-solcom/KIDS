
(function () {
    // 閉じた際の処理
    $(window).on('beforeunload', function () {
        window.opener.opener.location.reload();
        window.opener.close();
        window.close();
    });


    function fncListOutput(strURL) {
        listW = window.open(strURL, 'listWin', 'width=800,height=768,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no');
        return false;
    }
})();