function fncDownload(url) {
    // 再印刷チェックボックスの状態を取得
    var reprintFlag = parent.button.document.getElementById('rePrintChk').checked;
    // URL設定
    url = url + '&reprintFlag=' + reprintFlag;
    // ページ遷移
    location.href = url;
    // ３０秒経過後、親画面クローズ
//    setTimeout("alert('30秒経過');parent.window.close();", 30000);

    return false;

}