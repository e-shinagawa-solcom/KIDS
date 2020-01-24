// ロード時の表の位置調整
$(window).on('load',function(){
	var height = $('#header').height();
	var width = $('#sideMenu').width();
	$('.header').height(height);
	$('.grid').css({'margin-left': width + 'px'});
});

// 編集中止
function cancelEdit() {
	var text = '編集内容を破棄して閲覧モードに戻ります。\nよろしいですか？';
	var result = confirm(text);
	  
	if (result) {
		// フォームに処理モードを追加
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
			alert('DBエラー');
		});
	} else {
		return false;
	}
}

// 編集モードから離れるときの処理
window.onbeforeunload = function() {
    // 排他テーブルのレコード削除
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

// 閲覧モードへの遷移処理
function previewModeTransition (actionUrl) {
	var formData = $('#formData');
	formData.attr('action', actionUrl);
	formData.attr('target', '');
	formData.submit();
}