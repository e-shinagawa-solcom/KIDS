$(function(){
	$('.btnSwitch').on('click', function(){
		var sessionID = $('input[name="strSessionID"]').val();
		var estimateNo = $('input[name="estimateNo"]').val();
		var revisionNo = $(this).val();

		var actionUrl = "/estimate/preview/index.php?strSessionID=" + sessionID + "&estimateNo=" + estimateNo;
		
		var form = $("<form>", {
			method: 'post',
            action: actionUrl,
            target: '_self',
		})

		// フォームに処理モードを追加
		form.append($("<input>", {
			type: 'hidden',
			name: 'revisionNo',
			value: revisionNo
		}));

		form.appendTo(document.body);
		form.submit();
		form.remove();
		
		return false;
	});
});

// ロード時の配置調整
$(window).on('load',function(){
	var height = $('#header').height();
	var width = $('#sideMenu').width();
	$('.header').height(height);
	$('.grid').css({'margin-left': width + 'px'});
});

// URLコピー
function urlCopy() {
	$('body').append('<textarea id="currentURL" style="position:fixed;left:-100%;">' + location.href + '</textarea>');
	$('#currentURL').select();
	document.execCommand('copy');
	$('#currentURL').remove();
	alert("URLをコピーしました。");
}

function makePostData() {
	var url = window.location.href;
	var sessionID = getParam('strSessionID', url);
	var reviseCode = getParam('reviseCode', url);
	var value = [];
	value['strSessionID'] = sessionID;
	value['reviseCode'] = reviseCode;
	return value;
}

// urlからgetパラメータを取得する
function getParam(name, url) {
    if (!url) {
		url = window.location.href;
	}
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) {
		return null;
	}
    if (!results[2]) {
		return '';
	}
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function postChild(data) {
	// フォーム生成
	var html = '<form name="temp_form" style="display:none;">';
	for(var num in data) {
	  if(data[num] == undefined || data[num] == null) {
		continue;
	  }
	  var _val = data[num].replace(/'/g, '\'');
	  html += "<input type='hidden' name='" + num + "' value='" + _val + "' >";
	}
	html += '</form>';

	$("body").append(html);

	var windowName = 'preview_result';
	win = window.open('about:blank', windowName, 'scrollbars=yes, width=1000, height=700, resizable=0 location=0');

	document.temp_form.target = windowName;
	document.temp_form.action = '/estimate/preview/result.php';
	document.temp_form.method = 'POST';
	document.temp_form.submit();
   
	// フォームを削除
	document.temp_form.remove();
}

// 編集モードへの移行
function editModeTransition() {
	// フォームに処理モードを追加
	$("<input>", {
		type: 'hidden',
		class: 'addElements',
		name: 'processMode',
		value: 'edit'
	}).appendTo('#formData');
	
	var formData = $('#formData');
	
	$.ajax({
		url: "/estimate/preview/editExclusive.php",
		type: "post",
		dataType: "json",
		async: false,
		data: formData.serialize()
	
	}).done(function (response) {
		if (response.result === true) {			
			formData.attr('action', response.action);
			formData.attr('target', '');
			formData.submit();
		} else {

			var baseURI = formData[0].baseURI;
			
			if(baseURI.indexOf('/estimate/preview/') > 0) {
				windowName = 'estimateEditChangeError';
				winWidth = 400;
				winHeight = 250;
				var x = (screen.width - winWidth) / 2;
				var y = (screen.height - winHeight) / 2;
				var windowResult = open(
					'about:blank',
					windowName,
					'scrollbars=yes, width=' + winWidth + ', height=' + winHeight +', top=' + y + ', left=' + x + 'resizable=0 location=no'
				);
				// フォーム設定
				formData.append('<input type="hidden" class="addMessage" name="message" value="' + response.message + '">');
				formData.attr('action', response.action);
				formData.attr('target', windowName);		

				// サブミット
				formData.submit();
			}
			else {
				alert('URLが不正です');
				return false;
			}
		}	
	}).fail(function (xhr,textStatus,errorThrown) {
		alert('通信エラー');
		return false;
	}).always(function () {
		$('.addElements').remove();
	});
}

	// テストソース(ダウンロード用)
	function fileDownload() {
		var sessionID = $('input[name="strSessionID"]').val();
		var estimateNo = $('input[name="estimateNo"]').val();
		var revisionNo = $('#btnSelected').val();

		var actionUrl = "/estimate/preview/download.php?strSessionID=" + sessionID + "&estimateNo=" + estimateNo;
		
		var form = $("<form>", {
			method: 'post',
            action: actionUrl,
            target: '_self',
		})

		// フォームに処理モードを追加
		form.append($("<input>", {
			type: 'hidden',
			name: 'revisionNo',
			value: revisionNo
		}));

		form.appendTo(document.body);
		form.submit();
		form.remove();
		
		return false;
	}

// function fileDownload() {
// 	var sessionID = $('input[name="strSessionID"]').val();
// 	var productCode = $('input[name="productCode"]').val();
// 	var reviseCode = $('input[name="reviseCode"]').val();
// 	var revisionNo = $('#btnSelected').val();

// 	var actionUrl = "/estimate/preview/download.php";

// 	var formData = $("<form>", {
// 		method: 'post',
// 		action: actionUrl,
// 	})

// 	// フォームに処理モードを追加
// 	formData.append($("<input>", {
// 		type: 'hidden',
// 		name: 'strSessionID',
// 		value: sessionID
// 	}));

// 	formData.append($("<input>", {
// 		type: 'hidden',
// 		name: 'productCode',
// 		value: productCode
// 	}));

// 	formData.append($("<input>", {
// 		type: 'hidden',
// 		name: 'reviseCode',
// 		value: reviseCode
// 	}));

// 	formData.append($("<input>", {
// 		type: 'hidden',
// 		name: 'revisionNo',
// 		value: revisionNo
// 	}));
	
// 	$.ajax({
// 		url: actionUrl,
// 		type: "post",
// 		dataType: "json",
// 		async: false,
// 		data: formData.serialize()
	
// 	}).done(function (response) {
// 		if (response.result === true) {			
// 			formData.attr('action', response.action);
// 			formData.attr('target', '');
// 			formData.submit();
// 		} else {

// 			var baseURI = formData[0].baseURI;
			
// 			if(baseURI.indexOf('/estimate/preview/') > 0) {
// 				windowName = 'estimateEditChangeError';
// 				winWidth = 400;
// 				winHeight = 250;
// 				var x = (screen.width - winWidth) / 2;
// 				var y = (screen.height - winHeight) / 2;
// 				var windowResult = open(
// 					'about:blank',
// 					windowName,
// 					'scrollbars=yes, width=' + winWidth + ', height=' + winHeight +', top=' + y + ', left=' + x + 'resizable=0 location=no'
// 				);
// 				// フォーム設定
// 				formData.append('<input type="hidden" class="addMessage" name="message" value="' + response.message + '">');
// 				formData.attr('action', response.action);
// 				formData.attr('target', windowName);		

// 				// サブミット
// 				formData.submit();
// 			}
// 			else {
// 				alert('URLが不正です');
// 			}
// 		}	
// 	}).fail(function (xhr,textStatus,errorThrown) {
// 		alert('通信エラー');
// 	}).always(function () {
		
// 	});

// 	document.formAction.submit();
// }

function sheetPrint() {
	//プリントしたいエリアの取得
	var printPage = $('#grid0').html();

		//プリント用の要素「#print」を作成
		$('body').append('<div id="print1"></div>');
		$('#print1').append(printPage);

		//「#print」以外の要素に非表示用のclass「print-off」を指定
		$('body > :not(#print1)').addClass('print-off');
		window.print();

	//window.print()の実行後、作成した「#print」と、非表示用のclass「print-off」を削除
	$('#print1').remove();
	$('.print-off').removeClass('print-off');
}

function addPostData(name, value, formElement) {
	var element = document.createElement('input');
	// データを設定
	element.setAttribute('type', 'hidden');
	element.setAttribute('name', name);
	element.setAttribute('value', value);
	
	// 要素を追加
	formElement.appendChild(element);

	return;
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
