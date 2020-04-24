

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
var getDataModeFlg = 0; //0:全データ 1:有効データ
var phpData;// PHPからの取得データ
var lcInfoHit;
var getInfoFlg;//抽出フラグ
var tableData;//テーブルデータ確保配列

//---------------------------------------------------
// 画面初期処理
//---------------------------------------------------
function lcInit(json_obj) {
	phpData = $.parseJSON(json_obj);
	console.log(phpData);
	if (phpData.reSearchFlg == "true" && $.cookie("lcInfoSearchConditions") != undefined) {
		//他画面からの戻りなどで、移動前の検索条件で再検索する場合
		//抽出条件をcookieから取得
		getSearchConditions();

		//抽出フラグが来てる場合は抽出処理
		getLcInfo(1, 2);
	} else {
		//抽出条件をcookieから削除
		delSearchConditions();
		//全件検索
		getLcInfo(0);
	}


}

//---------------------------------------------------
// LC情報テーブル適応
//---------------------------------------------------
function setLcInfoTable(data, phpData) {
	//既存データの削除
	$("#lc_table_body").empty();
	$("#lc_table_radio").empty();

	for (var i = 0; i < data.length; i++) {
		//行データ
		var row = data[i];

		//L/C情報の状態 = 0/4/8の場合、上記で取得したstrclrstatus = 0 の背景色を設定する
		//L/C情報の状態 = 1の場合、上記で取得したstrclrstatus = 1 の背景色を設定する
		//L/C情報の状態 = 2/5/10の場合、上記で取得したstrclrstatus = 2 の背景色を設定する
		//L/C情報の状態 = 3の場合、上記で取得したstrclrstatus = 3 の背景色を設定する
		//L/C情報の状態 = 6の場合、上記で取得したstrclrstatus = 6 の背景色を設定する
		//L/C情報の状態 = 7の場合、上記で取得したstrclrstatus = 7 の背景色を設定する
		//L/C情報の状態 = 9の場合、上記で取得したstrclrstatus = 9 の背景色を設定する
		var background_color = "255,255,255";//デフォルト
		var background_color_data;
		switch (row.lcstate) {
			case "0":
			case "4":
			case "8":
				background_color_data = phpData["background_color"][0];
				break;
			case "1":
				background_color_data = phpData["background_color"][1];
				break;
			case "2":
			case "5":
			case "10":
				background_color_data = phpData["background_color"][2];
				break;
			case "3":
				background_color_data = phpData["background_color"][3];
				break;
			case "6":
				background_color_data = phpData["background_color"][6];
				break;
			case "7":
				background_color_data = phpData["background_color"][7];
				break;
			case "9":
				background_color_data = phpData["background_color"][9];
				break;
		}
		if (background_color_data != null) {
			//背景色生成
			background_color = background_color_data["lngcolorred"] + "," + background_color_data["lngcolorgreen"] + "," + background_color_data["lngcolorblue"];
		}

		var lc_table_radio = '<tr style="height: 20px;">' +
			'<td style="padding-left: 8px;"><input type="radio" name="selectRow" value="' + (i + 1) + '" class="form-control form-control-sm" style="width:85%;height: 15px;background-color: #f0f0f6;"></td>' +
			'</tr>';
		$("#lc_table_radio").append(lc_table_radio);
		var balance = row.moneyprice;
		if (row.bldetail1money != null) {
			balance -= row.bldetail1money;
		}
		if (row.bldetail2money != null) {
			balance -= row.bldetail2money;
		}
		if (row.bldetail3money != null) {
			balance -= row.bldetail3money;
		}
		var lc_table_body = '<tr id="' + i + '" before-click-bgcolor="rgb(' + background_color + ')" style="height: 20px;">' +
			// '<td style="text-align: left;"><input type="radio" name="selectRow" value="' + i + '" class="form-control form-control-sm"></td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.payfnameomit) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + strIns(row.opendate, 4, '/') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.portplace) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.pono) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.polineno) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.poreviseno) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.postate) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.payfcd) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.productcd) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.productrevisecd) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.productname) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');text-align:right;">' + convertNumberByClass(row.productnumber, "", 0) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.unitname) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');text-align:right;">' + convertNumberByClass(row.unitprice, "", 4) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');text-align:right;">' + convertNumberByClass(row.moneyprice, row.currencyclass, 0) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.currencyclass) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.shipstartdate, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.shipenddate, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.sumdate, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.poupdatedate, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.deliveryplace, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.lcnote) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.shipterm, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.validterm, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.bankname) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.bankreqdate, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.lcno) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.lcamopen, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.validmonth, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');text-align:right;">' + convertNumberByClass(balance.toString(), row.currencyclass, 0) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.bldetail1date, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');text-align:right;">' + convertNumberByClass(row.bldetail1money, row.currencyclass, 0) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.bldetail2date, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');text-align:right;">' + convertNumberByClass(row.bldetail2money, row.currencyclass, 0) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.bldetail3date, 'yy/mm/dd') + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');text-align:right;">' + convertNumberByClass(row.bldetail3money, row.currencyclass, 0) + '</td>' +
			'<td style="display:none;">' + row.lcstate + '</td>' +
			'</tr>';
		$("#lc_table_body").append(lc_table_body);
	}

	var width = 0;
	for (var i = 1; i <= 36; i++) {
		var head_width = $("#lc_table_head tr th:nth-child(" + i + ")").width();
		var body_width = $("#lc_table_body tr td:nth-child(" + i + ")").width();
		if (head_width > body_width) {
			$("#lc_table_body tr td:nth-child(" + i + ")").width(head_width);
			$("#lc_table_head tr th:nth-child(" + i + ")").width(head_width);
			width += head_width;
		} else {
			$("#lc_table_body tr td:nth-child(" + i + ")").width(body_width);
			$("#lc_table_head tr th:nth-child(" + i + ")").width(body_width);
			width += body_width;
		}
		$("[name='T']").width(width + 250);
	}


	$("#lc_head").trigger("update");
	$("#lc_table").trigger("update");
	trClickSelectRow();

	//0件の場合はエラー
	lcInfoHit = true;
	if (data == false) {
		lcInfoHit = false;
		alert("条件にヒットするL/C情報は見つかりません。");
	}

	//ローダー解除
	$("#masking_loader").css("display", "none");
}

//---------------------------------------------------
// 抽出処理
//---------------------------------------------------
function getLcInfo(mode, type = 1) {

	var error = false;
	var error_msg = "";
	//mode：0 全件, 1 検索条件含む抽出処理
	if (mode == 1) {
		var from = $("#startYm").val().replace("/", "");
		var to = $("#endYm").val().replace("/", "");

		if (from == "" && to == "" && $("#payfCode").val() == "" && $("#payfName").val() == "" && type == 1) {
			error = true;
			error_msg += "条件を入力してください。\r\n";
		}

		//年月（FROM）日付ではない場合、エラー
		if (from != "" && !from.match(/(\d{4})(\d{2})/)) {
			error = true;
			error_msg += "FROMの形式が不正です。例：2019/01\r\n";
		}
		//年月（TO）日付ではない場合、エラー
		if (to != "" && !to.match(/(\d{4})(\d{2})/)) {
			error = true;
			error_msg += "TOの形式が不正です。例：2019/01\r\n";
		}
		//年月（FROM）＞年月（TO）の場合、エラー
		if (from != "" && to != "" && to < from) {
			error = true;
			error_msg += "TOはFROMより未来を設定してください。\r\n";
		}

		//抽出条件をcookieに確保
		saveSearchConditions();
		getInfoFlg = 1;
	} else if (mode == 0) {
		getInfoFlg = 0;
	}


	if (error == false) {
		//LC情報取得
		$("#masking_loader").css("display", "block");
		$.ajax({
			url: '/lc/lcModel/lcinfo_ajax.php',
			type: 'POST',
			data: {
				'method': 'getLcInfo',
				'mode': mode,
				'getDataModeFlg': getDataModeFlg,
				'from': from,
				'to': to,
				'payfcd': $("#payfCode").val(),
				'payfnameomit': $("#payfName").val(),
				'sessionid': phpData["session_id"]
			}
		})
			.done(function (data) {
				// console.log(data);
				// Ajaxリクエストが成功
				var data = JSON.parse(data);
				tableData = data;
				setLcInfoTable(data, phpData);
				setBtnControll();
			})
			.fail(function (data) {
				alert("fail");
				// Ajaxリクエストが失敗
			});
	} else {
		//検索フォームエラー出力
		alert(error_msg);
	}

}


//---------------------------------------------------
// 編集処理
//---------------------------------------------------
function openEdit() {
	//行が選択されているかどうか
	var sel = $('input[name=selectRow]:checked').val();
	if (sel == undefined) {
		alert("編集するL/C情報を選択してください。");
		return;
	}
	var row = $('#lc_table_body tr:nth-child(' + sel + ')');
	console.log(row.text());
	var pono = row.find('td:nth-child(4)').text();
	var polineno = row.find('td:nth-child(5)').text();
	var poreviseno = row.find('td:nth-child(6)').text();
	var lcstate = row.find('td:nth-child(37)').text();
	console.log(lcstate);
	if (lcstate != 0 &&
		lcstate != 3 &&
		lcstate != 4 &&
		lcstate != 7 &&
		lcstate != 8 &&
		lcstate != 9) {
		alert("選択されたL/C情報は編集することが出来ません。");
	} else {
		var strURL = '/lc/edit/index.php?strSessionID=' + phpData["session_id"] + '&pono=' + pono + '&polineno='
			+ polineno + '&poreviseno=' + poreviseno;
		window.open(strURL, 'LC EDIT', 'width=1000, height=550, resizable=yes, scrollbars=yes, menubar=no');
	}
}

//---------------------------------------------------
// クリア処理
//---------------------------------------------------
function formClear() {
	$("#startYm").val("");
	$("#endYm").val("");
	$("#payfCode").val("");
	$("#payfName").val("");
}

//---------------------------------------------------
//特定の位置に文字挿入
//---------------------------------------------------
function strIns(str, idx, val) {
	if (str != "" && str != null && str != "null") {
		var res = str.slice(2, idx) + val + str.slice(idx);
		return res;
	} else {
		return "";
	}

};

/**
 * 文字変換（nullの場合、""に変換）
 * @param {} str 
 */
function convertNull(str) {
	if (str != "" && str != undefined && str != "null") {
		return str;
	} else {
		return "";
	}
}

//---------------------------------------------------
//有効・全データボタンイベント
//---------------------------------------------------
function switchGetModeBtn(flg, serch_flg) {
	$("#allBtn").removeClass("selected-btn");
	$("#validBtn").removeClass("selected-btn");
	getDataModeFlg = flg;
	if (getDataModeFlg == 1) {
		//有効データ
		$("#validBtn").addClass("selected-btn");
	} else {
		//全データ
		$("#allBtn").addClass("selected-btn");
	}

	//抽出実行
	if (serch_flg) {
		getLcInfo(1, 2);
	}
}



//---------------------------------------------------
//反映データボタンイベント
//---------------------------------------------------
function reflectLcInfo() {
	// 反映イベントの初期処理
	$.ajax({
		url: '../info/reflect.php',
		type: 'POST',
		data: {
			'strSessionID': phpData["session_id"],
			'mode': 3
		}
	})
		.done(function (data) {
			// Ajaxリクエストが成功
			var data = JSON.parse(data);
			// 取得したlgoutymdが空ではない場合、排他解除メッセージを表示し、処理終了
			if (data.lgoutymd != null) {
				alert("排他制御が解除されました。サーバーの情報を更新する場合はログインし直してください。");
				return;
			} else {
				// 取得したlgoutymdが空の場合、反映確認メッセージを表示する
				if (res = confirm("更新した情報をサーバに反映しますか。")) {
					console.log($("#masking_loader"));
					$("#masking_loader").css("display", "block");
					$.ajax({
						url: '../lcModel/lcinfo_ajax.php',
						type: 'POST',
						data: {
							'method': 'reflectLcInfo',
							'sessionid': phpData["session_id"]
						}
					})
						.done(function (data) {
							if (data == "true") {
								alert("反映が完了しました。");
							} else {
								alert("反映でエラーが発生しました。");
							}
						})
						.fail(function () {
							// Ajaxリクエストが失敗
						});
					//ローダー解除
					$("#masking_loader").css("display", "none");
				} else {
					return;
				}
			}
		})
		.fail(function () {
			alert("fail");
			// Ajaxリクエストが失敗
		});
}


//---------------------------------------------------
//各ボタン制御
//---------------------------------------------------
function setBtnControll() {
	//編集ボタン
	if (lcInfoHit) {
		$("#editBtn").prop("disabled", false);
	} else {
		$("#editBtn").prop("disabled", true);
	}
	//帳票出力ボタン
	if (lcInfoHit) {
		$("#reportOutputBtn").prop("disabled", false);
	} else {
		$("#reportOutputBtn").prop("disabled", true);
	}
	//反映ボタン
	if (lcInfoHit && phpData["userAuth"] == 1) {
		$("#reflectionBtn").prop("disabled", false);
	} else {
		$("#reflectionBtn").prop("disabled", true);
	}
}

//---------------------------------------------------
//検索条件保存
//---------------------------------------------------
function saveSearchConditions() {
	var search_conditions = {
		'from': $("#startYm").val().replace(/\//g, ""),
		'to': $("#endYm").val().replace(/\//g, ""),
		'payfcd': $("#payfCode").val(),
		'payfnameomit': $("#payfName").val(),
		'simulateYm': $("#simulateYm").val(),
		'getDataModeFlg': getDataModeFlg
	};
	$.cookie('lcInfoSearchConditions', JSON.stringify(search_conditions));
}

//---------------------------------------------------
//検索条件反映
//---------------------------------------------------
function getSearchConditions() {
	var search_conditions = $.parseJSON($.cookie('lcInfoSearchConditions'));
	$("#startYm").val(search_conditions.from);
	$("#endYm").val(search_conditions.to);
	$("#payfcd").val(search_conditions.payfcd);
	$("#payfnameomit").val(search_conditions.payfnameomit);
	$("#simulateYm").val(search_conditions.simulateYm);
	switchGetModeBtn(search_conditions.getDataModeFlg, false);
}


//---------------------------------------------------
//検索条件削除
//---------------------------------------------------
function delSearchConditions() {
	$.removeCookie("lcInfoSearchConditions");
}

//---------------------------------------------------
// 帳票出力ボタン処理
//---------------------------------------------------
function reportOutputBtn() {
	var strURL = '/lc/report/index.php?strSessionID=' + phpData["session_id"];
	window.open(strURL, 'LC REPORT', 'width=1000, height=550, resizable=yes, scrollbars=yes, menubar=no');
}


function closeEvent() {
	$.ajax({
		url: '../lcModel/lcinfo_ajax.php',
		type: 'POST',
		data: {
			'method': 'getUnreflectedDataCount',
			'sessionid': phpData["session_id"]
		}
	})
		.done(function (data) {
			console.log(data);
			// Ajaxリクエストが成功
			var data = JSON.parse(data);
			if (data.count != "0") {
				if (confirm("変更が反映されていませんが終了しますか？")) {
					console.log(phpData);
					console.log(phpData.lngno);
					logout();
					window.close();
				}
			} else {
				logout();
				window.close();
			}
		})
		.fail(function () {
			// Ajaxリクエストが失敗
		});
}

// ウィンドウを閉じる前のイベント
$(window).on("beforeunload", function (e) {
	logout();
});

// ログアウト処理
function logout() {
	$.ajax({
		url: '../lcModel/lcinfo_ajax.php',
		type: 'POST',
		data: {
			'method': 'logoutState',
			'lgno': phpData.lgno,
			'sessionid': phpData["session_id"]
		}
	})
		// Ajaxリクエストが成功した時発動
		.done((data) => {
			console.log(data);
		})
		// Ajaxリクエストが失敗した時発動
		.fail((data) => {
			console.log(data);
		})
		// Ajaxリクエストが成功・失敗どちらでも発動
		.always((data) => {
		});
}
// function convertNumberByClass(str, currencyclass, fracctiondigits) {
// 	if (str != "" && str != undefined && str != "null") {
// 		if (currencyclass != "") {
// 			if (currencyclass == '円') {
// 				return Number(str).toLocaleString(undefined, {
// 					minimumFractionDigits: 0,
// 					maximumFractionDigits: 0
// 				});
// 			} else {
// 				return Number(str).toLocaleString(undefined, {
// 					minimumFractionDigits: 2,
// 					maximumFractionDigits: 2
// 				});
// 			}
// 		} else {
// 			return Number(str).toLocaleString(undefined, {
// 				minimumFractionDigits: fracctiondigits,
// 				maximumFractionDigits: fracctiondigits
// 			});
// 		}
// 	} else {
// 		return "";
// 	}
// }

function trClickSelectRow() {
	console.log($('#lc_table_body tr').length);
	$('#lc_table_body tr').on('click', function (e) {
		var index = $(this).index();
		var bgcolor = $(this).find('td').css("background-color");

		$('#lc_table_body tr').each(function (i, tr) {
			$(this).find('td').css("background-color", $(this).attr('before-click-bgcolor'));
		});

		var beforeClickBgcolor = $(this).attr('before-click-bgcolor');
		if (bgcolor != 'rgb(187, 187, 187)') {
			$(this).find('td').css("background-color", "#bbbbbb");
			$('#lc_table_radio tr:nth-child(' + (index + 1) + ')').find('input[type="radio"]').prop('checked', true);
		} else {
			$(this).find('td').css("background-color", beforeClickBgcolor);
			$('#lc_table_radio tr:nth-child(' + (index + 1) + ')').find('input[type="radio"]').prop('checked', false);
		}
	});


	$('input[type="radio"]').on('click', function (e) {
		var rowindex = $(this).parent().parent().index();

		$('#lc_table_body tr').each(function (i, tr) {
			$(this).find('td').css("background-color", $(this).attr('before-click-bgcolor'));
		});

		var beforeClickBgcolor = $("#lc_table_body tr:nth-child(" + (rowindex + 1) + ")").attr('before-click-bgcolor');
		if (this.checked) {
			$("#lc_table_body tr:nth-child(" + (rowindex + 1) + ")").find('td').css('background-color', '#bbbbbb');
			$("#lc_table_radio tr:nth-child(" + (rowindex + 1) + ")").find('td').find('input[type="radio"]').prop('checked', true);
		} else {
			$("#lc_table_body tr:nth-child(" + (rowindex + 1) + ")").find('td').css('background-color', beforeClickBgcolor);
			$("#lc_table_radio tr:nth-child(" + (rowindex + 1) + ")").find('td').find('input[type="radio"]').prop('checked', false);
		}
	});
}