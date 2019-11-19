$(function () {

	// $('button[class*="btnHistory"]').on('click', function() {
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
	// 		url: "/estimate/result/searchHistoryPreview.php",
	// 		type: "post",
	// 		dataType: "json",
	// 		async: false,
	// 		data: form.serialize(),
	// 		timeout: 10000,  // ñ�̤ϥߥ���

	// 		// ������
	// 		beforeSend: function(xhr, settings) {
	// 			// �ܥ����̵������������������ɻ�
	// 			$(this).attr('disabled', true);
	// 		},

	// 	}).done(function (response) {


	// 	}).fail(function (xhr,textStatus,errorThrown) {
	// 		alert('DB���顼');

	// 	}).always(function(jqXHR, textStatus) {
	// 		$(this).attr('disabled', false);
	// 	});
	// });

	$('button[class*="btnDetail"]').on('click', function () {
		var url = $(this).attr('action');
		var value = $(this).val();

		var windowName = 'workSheetView';

		var formData = $("<form>", {
			method: 'post',
			action: url,
			target: windowName
		});

		if (value) {
			// �ե�����˽����⡼�ɤ��ɲ�
			formData.append($("<input>", {
				type: 'hidden',
				name: 'revisionNo',
				value: value
			}));
		}

		var windowResult = open('about:blank', windowName, 'scrollbars=yes, width=985, height=700, resizable=0 location=0');

		// form���ɲ�
		formData.appendTo($('body'));

		// ���֥ߥå�
		formData.submit();

		// form�κ��
		formData.remove();

	});

	$('button[class*="btnDelete"]').on('click', function () {
		var url = $(this).attr('action');
		var value = $(this).val();
		var estimateNo = $(this).parent().parent().attr('id');
		var sessionID = $('input[name="strSessionID"]').val();

		var windowName = 'workSheetView';

		var formData = $("<form>", {
			method: 'post',
			action: url,
			target: windowName
		});

		// ���Ѹ����ֹ���ɲ�
		formData.append($("<input>", {
			type: 'hidden',
			name: 'strSessionID',
			value: sessionID
		}));

		// ���Ѹ����ֹ���ɲ�
		formData.append($("<input>", {
			type: 'hidden',
			name: 'estimateNo',
			value: estimateNo
		}));

		// ��ӥ�����ֹ���ɲ�
		formData.append($("<input>", {
			type: 'hidden',
			name: 'revisionNo',
			value: value
		}));

		var windowResult = open('about:blank', windowName, 'scrollbars=yes, width=985, height=700, resizable=0 location=0');

		// form���ɲ�
		formData.appendTo($('body'));

		// ���֥ߥå�
		formData.submit();

		// form�κ��
		formData.remove();
	});

	$('.sortColumns').on('click', function () {
		var sortKey = $(this).attr('data-value');
		var form = $('#displayColumns');

		var baseUrl = "/estimate/result/index.php";
		var sessionID = $('input[name="strSessionID"]').val();

		var actionUrl = baseUrl + '?strSessionID=' + sessionID + '&strSort=' + sortKey;

		form.attr('action', actionUrl);

		form.submit();
	});


	// ����ܥ���Υ��٥��
	$('button[class*="btnHistory"]').on('click', function () {
		var lngEstimateNo = $(this).attr('estimateNo');
		var lngRevisionNo = $(this).attr('revisionNo');
		var displayColumns = $('input[name="displayColumns"]').val().split(',');
		var rownum = $(this).attr('rownum');
		var sessionID = $('input[name="strSessionID"]').val();
		if ($('tr[id^="' + lngEstimateNo + '_"]').length) {
			$('tr[id^="' + lngEstimateNo + '_"]').remove();
		} else {
			// �ꥯ����������
			$.ajax({
				url: '/estimate/result/searchHistoryPreview.php',
				type: 'post',
				data: {
					'strSessionID': sessionID,
					'lngEstimateNo': lngEstimateNo,
					'lngRevisionNo': lngRevisionNo,
					'displayColumns': displayColumns,
					'rownum': rownum,
				}
			})
				.done(function (response) {
					console.log(response);
					$('tr[id="' + lngEstimateNo + '"]').after(response);
				})
				.fail(function (response) {
					console.log(response);
					alert("fail");
				})
		}
	});
});