// ���ɻ���ɽ�ΰ���Ĵ��
$(window).on('load',function(){
	var height = $('#header').height();
	var width = $('#sideMenu').width();
	$('.header').height(height);
	$('.grid').css({'margin-left': width + 'px'});
});

// �Խ����
function cancelEdit() {
	var text = '�Խ����Ƥ��˴����Ʊ����⡼�ɤ����ޤ���\n������Ǥ�����';
	var result = confirm(text);
	  
	if (result) {
		// �ե�����˽����⡼�ɤ��ɲ�
		$("<input>", {
			type: 'hidden',
			class: 'addElements',
			name: 'processMode',
			value: 'cancel'
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
				previewModeTransition(response.action);
			} else {
				var baseURI = formData[0].baseURI;
			
				if (baseURI.indexOf('/estimate/preview/') > 0) {
					windowName = 'estimateError';
					winWidth = 700;
					winHeight = 500;
					var x = (screen.width - winWidth) / 2;
					var y = (screen.height - winHeight) / 2; 
					var windowResult = open(
						'about:blank',
						windowName,
						'scrollbars=yes, width=' + winWidth + ', height=' + winHeight +', top=' + y + ', left=' + x + 'resizable=0 location=0'
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
			alert('DB���顼');
		});
	} else {
		return false;
	}
}

// �Խ��⡼�ɤ���Υ���Ȥ��ν���
window.onbeforeunload = function() {
    // ��¾�ơ��֥�Υ쥳���ɺ��
	$("<input>", {
		type: 'hidden',
		class: 'addElements',
		name: 'processMode',
		value: 'close'
	}).appendTo('#formData');
	
	var formData = $('#formData');
	
	$.ajax({
		url: "/estimate/preview/editExclusive.php",
		type: "post",
		dataType: "json",
		async: false,
		data: formData.serialize()
	});
}

// �����⡼�ɤؤ����ܽ���
function previewModeTransition (actionUrl) {
	var formData = $('#formData');
	formData.attr('action', actionUrl);
	formData.attr('target', '');
	formData.submit();
}