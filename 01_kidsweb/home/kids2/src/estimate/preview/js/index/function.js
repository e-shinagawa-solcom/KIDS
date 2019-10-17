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

// ���ɻ�������Ĵ��
$(window).on('load',function(){
	var height = $('#header').height();
	var width = $('#sideMenu').width();
	$('.header').height(height);
	$('.grid').css({'margin-left': width + 'px'});
});

// URL���ԡ�
function urlCopy() {
	$('body').append('<textarea id="currentURL" style="position:fixed;left:-100%;">' + location.href + '</textarea>');
	$('#currentURL').select();
	document.execCommand('copy');
	$('#currentURL').remove();
	alert("URL�򥳥ԡ����ޤ�����");
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

// �Խ��⡼�ɤؤΰܹ�
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

	// �ƥ��ȥ�����(�����������)
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

// 	// �ե�����˽����⡼�ɤ��ɲ�
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
// 				// �ե���������
// 				formData.append('<input type="hidden" class="addMessage" name="message" value="' + response.message + '">');
// 				formData.attr('action', response.action);
// 				formData.attr('target', windowName);		

// 				// ���֥ߥå�
// 				formData.submit();
// 			}
// 			else {
// 				alert('URL�������Ǥ�');
// 			}
// 		}	
// 	}).fail(function (xhr,textStatus,errorThrown) {
// 		alert('�̿����顼');
// 	}).always(function () {
		
// 	});

// 	document.formAction.submit();
// }

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


// ��������ɥܥ��󤬲����줿
function OnClickDownload(obj, lngSlipNo, strSlipCode, lngRevisionNo){
    // --------------------------------------------------------------------------
    // ��������ɤΤ������Ʊ��POST
    // 
    // ���͡�jQuery��$.ajax��POST�Ǥϥե������������ɤ����ޤ������ʤ��餷���Τ�
    //      �Ǥ�javascript��Ȥ�
    // --------------------------------------------------------------------------
    // POST�ѥ�᡼�������ꡣ���å����ID�ϱ����ե�����ɤ������
    var postParams = "strMode=download"
                    + "&lngSlipNo=" + lngSlipNo
                    + "&strSlipCode=" + strSlipCode
                    + "&lngRevisionNo=" + lngRevisionNo
                    + "&strSessionID=" + document.getElementById("strSessionID").value
                    ;

    // ��������ɥե�����̾
    var fileName = "KWG" + strSlipCode + ".xlsx";

����// ��Ʊ���ꥯ�����Ȥ�����
    var url = "preview.php"
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
    xhr.responseType = 'blob'; //blob���Υ쥹�ݥ󥹤�����դ���

    // ������Хå����
    xhr.onload = function (e) {
        // �������ν���
        if (this.status == 200) {
            var blob = this.response;//�쥹�ݥ�
            //IE�Ȥ���¾�ǽ������ڤ�ʬ��
            if (navigator.appVersion.toString().indexOf('.NET') > 0) {
                //IE 10+
                window.navigator.msSaveBlob(blob, fileName);
            } else {
                //a����������
                var a = document.createElement("a");
                //�쥹�ݥ󥹤���Blob���֥������ȡ�URL������
                var blobUrl = window.URL.createObjectURL(new Blob([blob], {
                    type: blob.type
                }));
                //�����������a�����򥢥ڥ��
                document.body.appendChild(a);
                a.style = "display: none";
                //Blob���֥�������URL�򥻥å�
                a.href = blobUrl;
                //��������ɤ�����ե�����̾������
                a.download = fileName;
                //����å����٥��ȯ��
                a.click();
            }
        }
    };

    // ��Ʊ���ꥯ�����Ȥ�����
    xhr.send(postParams);
}
