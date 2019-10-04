$(function(){
	$('.btnSwitch').on('click', function(){
		var sessionID = $('input[name="strSessionID"]').val();
		var productCode = $('input[name="productCode"]').val();
		var reviseCode = $('input[name="reviseCode"]').val();
		var revisionNo = $(this).val();

		var actionUrl = "/estimate/preview/index.php?strSessionID=" + sessionID + "&productCode=" + productCode + "&reviseCode=" + reviseCode;
		
		var form = $("<form>", {
			method: 'post',
            action: actionUrl,
            target: '_self',
		})

		// �ե�����˽����⡼�ɤ��ɲ�
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

$(window).on('load',function(){
	var height = $('#header').height();
	var width = $('#sideMenu').width();
	$('.header').height(height);
	$('.grid').css({'margin-left': width + 'px'});
});

function urlCopy() {
	$('body').append('<textarea id="currentURL" style="position:fixed;left:-100%;">' + location.href + '</textarea>');
	$('#currentURL').select();
	document.execCommand('copy');
	$('#currentURL').remove();
	alert("URL�򥳥ԡ����ޤ�����");
}

function orderCancel (name) {
	var target = $('[name=' + name + ']:checked').map(function() {
		return $(this).val();
	}).get().join(",");
	
	if (target) {
		var text = '���򤵤줿ȯ��γ������ä��ޤ���\n������Ǥ�����';
		var result = confirm(text);
		if (result) {
			var value = [];
			value = makePostData();

			value['detailNo'] = target;
			value['action'] = 'cancel';

			value['revisionNo'] = document.revisionNo.value;

			postChild(value);
		} else {
			return false;
		}
	} else {
		alert('�о����٤����򤵤�Ƥ��ޤ���');
	}
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

// url����get�ѥ�᡼�����������
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
	// �ե���������
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
   
	// �ե��������
	document.temp_form.remove();
}

function editModeTransition() {
	// �ե�����˽����⡼�ɤ��ɲ�
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
				// �ե���������
				formData.append('<input type="hidden" class="addMessage" name="message" value="' + response.message + '">');
				formData.attr('action', response.action);
				formData.attr('target', windowName);		

				// ���֥ߥå�
				formData.submit();
			}
			else {
				alert('URL�������Ǥ�');
				return false;
			}
		}	
	}).fail(function (xhr,textStatus,errorThrown) {
		alert('�̿����顼');
		return false;
	}).always(function () {
		$('.addElements').remove();
	});
}

function fileDownload() {
	var sessionID = $('input[name="strSessionID"]').val();
	var productCode = $('input[name="productCode"]').val();
	var reviseCode = $('input[name="reviseCode"]').val();
	var revisionNo = $('#btnSelected').val();

	var actionUrl = "/estimate/preview/download.php";

	var formData = $("<form>", {
		method: 'post',
		action: actionUrl,
	})

	// �ե�����˽����⡼�ɤ��ɲ�
	formData.append($("<input>", {
		type: 'hidden',
		name: 'strSessionID',
		value: sessionID
	}));

	formData.append($("<input>", {
		type: 'hidden',
		name: 'productCode',
		value: productCode
	}));

	formData.append($("<input>", {
		type: 'hidden',
		name: 'reviseCode',
		value: reviseCode
	}));

	formData.append($("<input>", {
		type: 'hidden',
		name: 'revisionNo',
		value: revisionNo
	}));
	
	$.ajax({
		url: actionUrl,
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
				// �ե���������
				formData.append('<input type="hidden" class="addMessage" name="message" value="' + response.message + '">');
				formData.attr('action', response.action);
				formData.attr('target', windowName);		

				// ���֥ߥå�
				formData.submit();
			}
			else {
				alert('URL�������Ǥ�');
			}
		}	
	}).fail(function (xhr,textStatus,errorThrown) {
		alert('�̿����顼');
	}).always(function () {
		
	});

	document.formAction.submit();
}

function sheetPrint() {
	//�ץ��Ȥ��������ꥢ�μ���
	var printPage = $('#grid0').html();

		//�ץ����Ѥ����ǡ�#print�פ����
		$('body').append('<div id="print1"></div>');
		$('#print1').append(printPage);

		//��#print�װʳ������Ǥ���ɽ���Ѥ�class��print-off�פ����
		$('body > :not(#print1)').addClass('print-off');
		window.print();

	//window.print()�μ¹Ը塢����������#print�פȡ���ɽ���Ѥ�class��print-off�פ���
	$('#print1').remove();
	$('.print-off').removeClass('print-off');
}

function addPostData(name, value, formElement) {
	var element = document.createElement('input');
	// �ǡ���������
	element.setAttribute('type', 'hidden');
	element.setAttribute('name', name);
	element.setAttribute('value', value);
	
	// ���Ǥ��ɲ�
	formElement.appendChild(element);

	return;
}
// function fncAlphaOff( obj )
// {
// 	obj.style.filter = 'alpha(opacity=100)' ;
// }

// function fncRegistButton( strMode , obj )
// {
// 	switch( strMode )
// 	{
// 		case 'offJ':
// 			obj.src = registJ1;
// 			break;

// 		case 'onJ':
// 			obj.src = registJ2;
// 			break;

// 		case 'downJ':
// 			obj.src = registJ3;
// 			break;

// 		default:
// 			break;
// 	}
// }

// function fncCloseButton( strMode , obj )
// {
// 	switch( strMode )
// 	{
// 		case 'offJ':
// 			obj.src = closeJ1;
// 			break;

// 		case 'onJ':
// 			obj.src = closeJ2;
// 			break;

// 		case 'downJ':
// 			obj.src = closeJ3;
// 			break;

// 		default:
// 			break;
// 	}
// }
// // ��Ͽgif
// var registJ1 = '/img/type01/estimate/regist/regist/regist_off_ja_bt.gif';
// var registJ2 = '/img/type01/estimate/regist/regist/regist_off_on_ja_bt.gif';
// var registJ3 = '/img/type01/estimate/regist/regist/regist_on_ja_bt.gif';


// // �Ĥ���gif
// var closeJ1 = '/img/type01/estimate/regist/close/close_off_ja_bt.gif';
// var closeJ2 = '/img/type01/estimate/regist/close/close_off_on_ja_bt.gif';
// var closeJ3 = '/img/type01/estimate/regist/close/close_on_ja_bt.gif';