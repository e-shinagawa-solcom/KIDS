$(function() {

	// $('button[class*="btnRecord"]').on('click', function() {
	// 	var estimateNo = $(this).parent().parent().attr('id');
	// 	var form = $('#displayColumns');
	// 	var strSort = 'strSort';
	// 	var sort = function (strSort) {
	// 		var url = window.location.href;
	// 		strSort = strSort.replace(/[\[\]]/g, "\\$&");
	// 		var regex = new RegExp("[?&]" + strSort + "(=([^&#]*)|&|#|$)"),
	// 			results = regex.exec(url);
	// 		if (!results) return null;
	// 		if (!results[2]) return '';
	// 		return decodeURIComponent(results[2].replace(/\+/g, " "));
	// 	};

    //     var numberForm = $("<input>", {
	// 		type: 'hidden',
	// 		name: 'estimateNo',
	// 		value: estimateNo
	// 	});

	// 	var numberForm = $("<input>", {
	// 		type: 'hidden',
	// 		name: 'estimateNo',
	// 		value: estimateNo
	// 	});

	// 	numberForm.appendTo(form);

	// 	$.ajax({
	// 		url: "/estimate/result/searchRecordPreview.php",
	// 		type: "post",
	// 		dataType: "json",
	// 		async: false,
	// 		data: form.serialize(),
	// 		timeout: 10000,  // 単位はミリ秒
 
	// 		// 送信前
	// 		beforeSend: function(xhr, settings) {
	// 			// ボタンを無効化し、二重送信を防止
	// 			$(this).attr('disabled', true);
	// 		},
		
	// 	}).done(function (response) {
		

	// 	}).fail(function (xhr,textStatus,errorThrown) {
	// 		alert('DBエラー');
			
	// 	}).always(function(jqXHR, textStatus) {
	// 		$(this).attr('disabled', false);
	// 	});
	// });
	
	$('button[class*="btnDetail"]').on('click', function() {
		var url = $(this).attr('action');
		var value = $(this).val();

		var windowName = 'workSheetView';

		var formData = $("<form>", {
			method: 'post',
			action: url,
			target: windowName
		});

		if (value) {		
			// フォームに処理モードを追加
			formData.append($("<input>", {
				type: 'hidden',
				name: 'revisionNo',
				value: value
			}));
		}
		
		var windowResult = open('about:blank', windowName, 'scrollbars=yes, width=985, height=700, resizable=0 location=0');

		// formの追加
		formData.appendTo($('body'));

		// サブミット
		formData.submit();

		// formの削除
		formData.remove();
	});

});