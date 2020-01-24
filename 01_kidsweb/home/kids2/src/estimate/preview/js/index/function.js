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

// ダウンロード処理
function fileDownload() {
	var sessionID = $('input[name="strSessionID"]').val();
	var estimateNo = $('input[name="estimateNo"]').val();
	var revisionNo = $('#btnSelected').val();

	var actionUrl = "/estimate/preview/download.php?strSessionID=" + sessionID + "&estimateNo=" + estimateNo;
	
	// 一時フォームの生成
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

function sheetPrint() {
	var sessionID = $('input[name="strSessionID"]').val();
	var reportCode = 3;
	var estimateNo = $('input[name="estimateNo"]').val();
	var revisionNo = $('#btnSelected').val();

	var actionUrl = "/list/result/frameset.php?strSessionID=" + sessionID + "&lngReportClassCode=" + reportCode + "&strReportKeyCode=" + estimateNo + "&revisionNo=" + revisionNo;
	
	listW = window.open( actionUrl , 'listWin' , 'width=1000,height=600,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no' );
	return false;
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


