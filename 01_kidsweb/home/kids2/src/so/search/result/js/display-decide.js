(function () {
    // 確定ボタンのイベント
    $('img.decide.button').on('click', function () {
        url = '/so/decide/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngReceiveNo = 'lngReceiveNo=' + $(this).attr('id');
        lngRevisionNo = 'revisionNo=' + $(this).attr('revisionno');
        lngEstimateNo = 'estimateNo=' + $(this).attr('lngestimateno');
            
        window.open(url + '?' + sessionID + '&' + lngReceiveNo + '&' + lngRevisionNo + '&' + lngEstimateNo, 'display-detail', 'width=1011, height=620, resizable=yes, scrollbars=yes, menubar=no');
    });
})();