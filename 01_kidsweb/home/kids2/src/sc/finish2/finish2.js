// ダウンロードボタンの画像パス
var imagepath_download_off = "/img/type01/cmn/querybt/listout_off_ja_bt.gif";
var imagepath_download_on  = "/img/type01/cmn/querybt/listout_off_on_ja_bt.gif";

// HTMLエレメント構築後
function OnloadBody(){
    // ダウンロードボタンの初期設定
    var images = document.getElementsByClassName('btn-download');
    for (var i = 0; i < images.length; i++) 
    {
        var image = images[i];
        image.src = imagepath_download_off;
        image.alt ="ダウンロード"
    }
}

// ダウンロードボタンにマウスカーソルが乗ったとき
function OnMouseOverDownload(obj){
    // マウスオーバー用画像に差し替える
    obj.src = imagepath_download_on
}
// ダウンロードボタンからマウスカーソルが外れたとき
function OnMouseOutDownload(obj){
    // 初期画像に戻す
    obj.src = imagepath_download_off
}
// ダウンロードボタンが押された
function OnClickDownload(obj, strSlipCode, lngRevisionNo){
    alert("strSlipCode="+strSlipCode+",lngRevisionNo="+lngRevisionNo);
    
    $.ajax({
        type: 'POST',
        url: "preview.php",
        data: {
            strMode : "download",
            strSessionID: $('input[name="strSessionID"]').val(),
            strSlipCode: strSlipCode,
            lngRevisionNo: lngRevisionNo,
        },
        async: true,
    }).done(function(data){
        console.log("done:download");
        alert(data);
    }).fail(function(error){
        console.log("fail:download");
        console.log(error);
    });

}
