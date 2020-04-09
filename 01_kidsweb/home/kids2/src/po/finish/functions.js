
(function () {
    // 閉じた際の処理
    $(window).on('beforeunload', function () {
        window.opener.location.reload();
        // $(window.opener.opener.document).find('form').submit();
    });


    function fncListOutput(strURL) {
        listW = window.open(strURL, 'listWin', 'width=800,height=670,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no');
        return false;
    }
})();