

//@-------------------------------------------------------------------------------------------------------------------
/**
* ファイル概要 : LC関連処理
*
*
*
* @package k.i.d.s.
* @license http://www.wiseknot.co.jp/
* @copyright Copyright &copy; 2004, Wiseknot, Inc.
* @author Ryosuke Tomita <r-tomita@wiseknot.co.jp>
* @access public
* @version 0.1
*/
//--------------------------------------------------------------------------------------------------------------------

//共通変数
var session_id;
var phpData;// PHPからの取得データ
var settingData;
var lc_data;
window.onerror = false;
//---------------------------------------------------
// 画面初期処理
//---------------------------------------------------
function lcInit(json_obj) {
	phpData = JSON.parse(json_obj);
	//LC情報取得
	$("#masking_loader").css("display", "block");
	$.ajax({
		url: '../lcModel/lcedit_ajax.php',
		type: 'POST',
		data: {
			'method': 'getLcEdit',
			'pono': phpData.pono,
			'poreviseno': phpData.poreviseno,
			'polineno': phpData.polineno,
			'sessionid': phpData["session_id"]
		}
	})
		.done(function (data) {
			var data = JSON.parse(data);
			if (data != false) {
				//LC情報
				lc_data = data.lc_data;
				//銀行リスト
				var bank_list = data.bank_list;
				for (var i = 0; i < bank_list.length; i++) {
					$option = $('<option>')
						.val(bank_list[i]["bankcd"])
						.text(bank_list[i]["bankomitname"])
					$('#bankname').append($option);
				}
				//荷揚地リスト
				var portplace_list = data.portplace_list;
				for (var i = 0; i < portplace_list.length; i++) {
					$option = $('<option>')
						.val(portplace_list[i]["portplace"])
						.text(portplace_list[i]["portplace"])
					$('#portplace').append($option);
				}

				//取得した情報をフォームに反映
				$("#pono").val(lc_data.pono);
				$("#payfcd").val(lc_data.payfcd);
				$("#payfnameformal").val(lc_data.payfnameformal);
				$("#polineno").val(lc_data.polineno);
				$("#productcd").val(lc_data.productcd);
				$("#productrevisecd").val(lc_data.productrevisecd);
				$("#productname").val(lc_data.productname);
				$("#opendate").val(lc_data.opendate);
				$("#moneyprice").val(convertNumberByClass(lc_data.moneyprice, lc_data.currencyclass, 0));
				//テーブル
				$('#portplace option').filter(function (index) {
					return $(this).text() === lc_data.portplace;
				}).prop('selected', true);
				$('#bankname option').filter(function (index) {
					return $(this).val() === lc_data.bankcd;
				}).prop('selected', true);

				$("#bankreqdate").val(convertDate(lc_data.bankreqdate));
				$("#lcno").val(lc_data.lcno);
				$("#lcamopen").val(convertDate(lc_data.lcamopen));
				$("#validmonth").val(convertDate(lc_data.validmonth));
				$("#bldetail1date").val(convertDate(lc_data.bldetail1date));
				$("#bldetail1money").val(convertNumberByClass(lc_data.bldetail1money, lc_data.currencyclass, 0));
				$("#bldetail2date").val(convertDate(lc_data.bldetail2date));
				$("#bldetail2money").val(convertNumberByClass(lc_data.bldetail2money, lc_data.currencyclass, 0));
				$("#bldetail3date").val(convertDate(lc_data.bldetail3date));
				$("#bldetail3money").val(convertNumberByClass(lc_data.bldetail3money, lc_data.currencyclass, 0));

				if (lc_data.lcstate == 3) {
					$("#opendate").prop("disabled", true);
					$("#portplace").prop("disabled", true);
					$("#bankname").prop("disabled", true);
					$("#bankreqdate").prop("disabled", true);
					$("#lcno").prop("disabled", true);
					$("#lcamopen").prop("disabled", true);
					$("#validmonth").prop("disabled", true);
					$("#bldetail1date").prop("disabled", true);
					$("#bldetail1money").prop("disabled", true);
					$("#bldetail2date").prop("disabled", true);
					$("#bldetail2money").prop("disabled", true);
					$("#bldetail3date").prop("disabled", true);
					$("#bldetail3money").prop("disabled", true);
					$("#bldetail3money").prop("disabled", true);
					$("#updateBtn").prop("disabled", true);
					$("#releaseBtn").prop("disabled", false);
				} else {
					$("#opendate").prop("disabled", false);
					$("#portplace").prop("disabled", false);
					$("#bankname").prop("disabled", false);
					$("#bankreqdate").prop("disabled", false);
					$("#lcno").prop("disabled", false);
					$("#lcamopen").prop("disabled", false);
					$("#validmonth").prop("disabled", false);
					$("#bldetail1date").prop("disabled", false);
					$("#bldetail1money").prop("disabled", false);
					$("#bldetail2date").prop("disabled", false);
					$("#bldetail2money").prop("disabled", false);
					$("#bldetail3date").prop("disabled", false);
					$("#bldetail3money").prop("disabled", false);
					$("#bldetail3money").prop("disabled", false);
					$("#updateBtn").prop("disabled", false);
					$("#releaseBtn").prop("disabled", true);
				}
				// $("#bankreqdate").val(lc_data.bankreqdate);
				// $("#lcno").val(lc_data.lcno);
				// $("#lcamopen").val(lc_data.lcamopen);
				// $("#validmonth").val(lc_data.validmonth);
				// $("#bldetail1date").val(lc_data.bldetail1date);
				// $("#bldetail1money").val(lc_data.bldetail1money);
				// $("#bldetail2date").val(lc_data.bldetail2date);
				// $("#bldetail2money").val(lc_data.bldetail2money);
				// $("#bldetail3date").val(lc_data.bldetail3date);
				// $("#bldetail3money").val(lc_data.bldetail3money);

				// 開始日時フォーカスを失ったときの処理
				$('#bldetail1money, #bldetail2money, #bldetail3money').on('blur', function () {
					var val = $(this).val();
					console.log(val);
					$(this).val(convertNumberByClass(val, lc_data.currencyclass, 0));
				});

				$('#bldetail1money, #bldetail2money, #bldetail3money').on('focus', function () {
					var val = $(this).val();
					console.log(val);
					$(this).val(val.replace(/,/g, ''));
				});

				//ローダー解除
				$("#masking_loader").css("display", "none");
			} else {
				alert("L/C情報が取得できませんでした。L/C情報画面に戻ります。");
				closeBtn();
			}
		})
		.fail(function () {
			alert("tsts");
			// Ajaxリクエストが失敗
		});
}

//---------------------------------------------------
// 戻るボタン処理
//---------------------------------------------------
function closeBtn() {
	window.opener.document.location.href = '/lc/info/index.php?strSessionID=' + phpData["session_id"] + '&reSearchFlg=true';
	window.close();
}

//---------------------------------------------------
// 更新ボタン処理
//---------------------------------------------------
function updateBtn() {
	//入力チェック
	//オープン年月が空の場合
	if ($("#opendate").val() == "") {
		alert("発行月の入力が空です。");
		return false;
	}
	//オープン年月の形式が日付形式（yyyy/mm）になってない ←仕様書が矛盾していてYYYYMMが正しいと思われる
	if (!$("#opendate").val().match(/^\d{4}\d{1,2}$/)) {
		alert("発行月の形式を確認してください。");
		return false;
	}

	//依頼日の形式が日付形式（yyyy/mm/dd）になってない
	if ($("#bankreqdate").val() != "" && !$("#bankreqdate").val().match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)) {
		alert("依頼日の形式を確認してください。");
		$("#bankreqdate").focus();
		return false;
	}

	//発行日の形式が日付形式（yyyy/mm/dd）になってない
	if ($("#lcamopen").val() != "" && !$("#lcamopen").val().match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)) {
		alert("発行日の形式を確認してください。");
		$("#lcamopen").focus();
		return false;
	}

	//有効日の形式が日付形式（yyyy/mm/dd）になってない
	if ($("#validmonth").val() != "" && !$("#validmonth").val().match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)) {
		alert("有効日の形式を確認してください。");
		$("#validmonth").focus();
		return false;
	}

	//決済1日付の形式が日付形式（yyyy/mm/dd）になってない
	if ($("#bldetail1date").val() != "" && !$("#bldetail1date").val().match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)) {
		alert("決済1日付の形式を確認してください。");
		$("#bldetail1date").focus();
		return false;
	}

	//決済2日付の形式が日付形式（yyyy/mm/dd）になってない
	if ($("#bldetail2date").val() != "" && !$("#bldetail2date").val().match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)) {
		alert("決済2日付の形式を確認してください。");
		$("#bldetail2date").focus();
		return false;
	}

	//決済3日付の形式が日付形式（yyyy/mm/dd）になってない
	if ($("#bldetail3date").val() != "" && !$("#bldetail3date").val().match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)) {
		alert("決済3日付の形式を確認してください。");
		$("#bldetail3date").focus();
		return false;
	}

	if ($("#bldetail1money").val() != "" && !$.isNumeric($("#bldetail1money").val().replace(/,/g, ''))) {
		alert("決済1金額の形式を確認してください。");
		$("#bldetail1money").focus();
		return false;
	}

	if ($("#bldetail2money").val() != "" && !$.isNumeric($("#bldetail2money").val().replace(/,/g, ''))) {
		alert("決済2金額の形式を確認してください。");
		$("#bldetail2money").focus();
		return false;
	}

	if ($("#bldetail2money").val() != "" && !$.isNumeric($("#bldetail3money").val().replace(/,/g, ''))) {
		alert("決済3金額の形式を確認してください。");
		$("#bldetail3money").focus();
		return false;
	}

	//発行日が空ではなくて、発行日の年 < 現在日付の年の場合
	var lcamopen_d = new Date($("#lcamopen").val());
	console.log(lcamopen_d);
	var now = new Date();
	now.setMonth(now.getMonth() - 12);
	if (lcamopen_d.getFullYear() < now.getFullYear()) {
		alert("過去年が設定されています。");
		$("#lcamopen").focus();
		return false;
	}

	//有効年月が空ではなくて、有効年月の年 < 現在日付の年の場合
	var validmonth_d = new Date($("#validmonth").val());
	if (validmonth_d.getFullYear() < now.getFullYear()) {
		alert("過去年が設定されています。");
		$("#validmonth").focus();
		return false;
	}

	//依頼日、発行日、有効日のいずれが空ではなくて、発行銀行が空の場合
	if (($("#opendate").val() != "" || $("#validmonth").val() != "" || $("#lcamopen").val() != "") &&
		$("#bankname").val() == ""
	) {
		alert("発行銀行を選択してください。");
		$("#bankname").focus();
		return false;
	}

	//発行日が空ではない、かつ依頼日が空の場合
	if ($("#lcamopen").val() != "" && $("#bankreqdate").val() == "") {
		alert("依頼日を入力してください。");
		$("#bankreqdate").focus();
		return false;
	}

	//有効日、発行日が空ではなくて、発行日 > 有効日の場合
	if ($("#validmonth").val() == "" && $("#lcamopen").val() == "") {
		var validmonth_d = new Date($("#validmonth").val());
		var lcamopen_d = new Date($("#lcamopen").val());
		if (validmonth_d < lcamopen_d) {
			alert("有効日は発行日より未来に設定してください。");
			$("#validmonth_d").focus();
			return false;
		}
	}
	// 状態が７の場合、
	if (lc_data.lcstate == 7) {
		alert("アメンドを解除します。");
	}
	//更新処理
	$("#masking_loader").css("display", "block");
	$.ajax({
		url: '../lcModel/lcedit_ajax.php',
		type: 'POST',
		data: {
			'method': 'updateLcEdit',
			'pono': $("#pono").val(),
			'poreviseno': phpData.poreviseno,
			'polineno': $("#polineno").val(),
			'opendate': $("#opendate").val(),
			'portplace': $("#portplace").val(),
			'bankcd': $("#bankname").val(),
			'bankname': $("#bankname option:selected").text(),
			'bankreqchk': $("#bankreqchk").prop('checked'),
			'bankreqdate': $("#bankreqdate").val(),
			'lcno': $("#lcno").val(),
			'lcamopen': $("#lcamopen").val(),
			'validmonth': $("#validmonth").val(),
			'bldetail1date': $("#bldetail1date").val(),
			'bldetail1money': $("#bldetail1money").val(),
			'bldetail2date': $("#bldetail2date").val(),
			'bldetail2money': $("#bldetail2money").val(),
			'bldetail3date': $("#bldetail3date").val(),
			'bldetail3money': $("#bldetail3money").val(),
			'lcstate': lc_data.lcstate,
			'sessionid': phpData["session_id"]
		}
	})
		.done(function (data) {
			console.log(data);
			// Ajaxリクエストが成功
			var data = JSON.parse(data);


			alert("更新処理が完了しました。");

			//ローダー非表示
			$("#masking_loader").css("display", "none");
		})
		.fail(function (data) {
			// Ajaxリクエストが失敗
			console.log(data);
			alert("更新処理が失敗しました。");
		});

}

//---------------------------------------------------
// 解除ボタン処理
//---------------------------------------------------
function releaseBtn() {
	if (res = confirm("解除設定しますか。")) {
		//更新処理
		$("#masking_loader").css("display", "block");
		$.ajax({
			url: '../lcModel/lcedit_ajax.php',
			type: 'POST',
			data: {
				'method': 'releaseLcEdit',
				'lc_data': lc_data,
				'sessionid': phpData["session_id"]
			}
		})
			.done(function (data) {
				// Ajaxリクエストが成功
				var data = JSON.parse(data);

				//編集画面リロード
				window.location.reload();

				//ローダー非表示
				$("#masking_loader").css("display", "none");
			})
			.fail(function () {
				// Ajaxリクエストが失敗
			});
	}

}

(function () {
	$(window).on("beforeunload", function (e) {
		window.opener.location.href = '/lc/info/index.php?strSessionID=' + phpData["session_id"] + '&reSearchFlg=true';
	});
})();