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
function OnClickDownload(obj, lngSlipNo, strSlipCode, lngRevisionNo){
    // --------------------------------------------------------------------------
    // ダウンロードのための非同期POST
    // 
    // 備考：jQueryの$.ajaxのPOSTではファイルダウンロードがうまくいかないらしいので
    //      素のjavascriptを使う
    // --------------------------------------------------------------------------
    // POSTパラメータの設定。セッションIDは隠しフィールドから取得
    var postParams = "strMode=download"
                    + "&lngSlipNo=" + lngSlipNo
                    + "&strSlipCode=" + strSlipCode
                    + "&lngRevisionNo=" + lngRevisionNo
                    + "&strSessionID=" + document.getElementById("strSessionID").value
                    ;

    // ダウンロードファイル名
    var fileName = "KWG" + strSlipCode + ".xlsx";

　　// 非同期リクエストの設定
    var url = "preview.php"
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
    xhr.responseType = 'blob'; //blob型のレスポンスを受け付ける

    // コールバック定義
    xhr.onload = function (e) {
        // 成功時の処理
        if (this.status == 200) {
            var blob = this.response;//レスポンス
            //IEとその他で処理の切り分け
            if (navigator.appVersion.toString().indexOf('.NET') > 0) {
                //IE 10+
                window.navigator.msSaveBlob(blob, fileName);
            } else {
                //aタグの生成
                var a = document.createElement("a");
                //レスポンスからBlobオブジェクト＆URLの生成
                var blobUrl = window.URL.createObjectURL(new Blob([blob], {
                    type: blob.type
                }));
                //上で生成したaタグをアペンド
                document.body.appendChild(a);
                a.style = "display: none";
                //BlobオブジェクトURLをセット
                a.href = blobUrl;
                //ダウンロードさせるファイル名の生成
                a.download = fileName;
                //クリックイベント発火
                a.click();
            }
        }
    };

    // 非同期リクエストの送信
    xhr.send(postParams);
}
