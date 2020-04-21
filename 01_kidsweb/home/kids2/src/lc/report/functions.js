

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
var phpData;
var reportResult;
var nextReport = "";
var nextIndex = 0;
var displayLst = [];

//---------------------------------------------------
// 画面初期処理
//---------------------------------------------------
function lcInit(json_obj) {
	phpData = JSON.parse(json_obj);
	session_id = phpData.session_id;

	//初期値設定
	// $("#openYm").val(phpData.openDate);
	// $("#objectYm").val(phpData.openDate);

	//セレクトフォーム取得
	$("#masking_loader").css("display", "block");
	$.ajax({
		url: '../lcModel/lcreport_ajax.php',
		type: 'POST',
		data: {
			'method': 'getSelLcReport',
			'sessionid': phpData["session_id"]
		}
	})
		// Ajaxリクエストが成功した時発動
		.done(function (data) {
			console.log(data);
			var data = JSON.parse(data);
			//銀行セレクト生成
			for (var i = 0; i < data.bankinfo.length; i++) {
				if (data.bankinfo[i].bankcd == "0000") {
					$('select[name="bankcd"]').append("<option value='" + data.bankinfo[i].bankcd + "' selected>" + $.trim(data.bankinfo[i].bankomitname) + "</option>");
				} else {
					$('select[name="bankcd"]').append("<option value='" + data.bankinfo[i].bankcd + "'>" + $.trim(data.bankinfo[i].bankomitname) + "</option>");

				}
			}
			//荷揚地セレクト生成
			for (var i = 0; i < data.portplace.length; i++) {
				if (data.portplace[i].portplace == "ALL") {
					$('select[name="portplace"]').append("<option value='" + $.trim(data.portplace[i].portplace) + "' selected>" + $.trim(data.portplace[i].portplace) + "</option>");
				} else {
					$('select[name="portplace"]').append("<option value='" + $.trim(data.portplace[i].portplace) + "'>" + $.trim(data.portplace[i].portplace) + "</option>");
				}

			}

		})
		// Ajaxリクエストが失敗した時発動
		.fail(function (data) {
		})
		// Ajaxリクエストが成功・失敗どちらでも発動
		.always(function (data) {
			$("#masking_loader").css("display", "none");
		});
}

//---------------------------------------------------
// 戻るボタン処理
//---------------------------------------------------
function closeBtn() {
	// location.href = '/lc/info/index.php?strSessionID=' + phpData["session_id"] + '&reSearchFlg=true';
	window.opener.location.href = '/lc/info/index.php?strSessionID=' + phpData["session_id"] + '&reSearchFlg=true';
	window.close();
}

//---------------------------------------------------
// 印刷ボタン処理
//---------------------------------------------------
function printBtn() {
	console.log($('input[name="impletterChk"]').prop("checked"));
	if (!$('input[name="impletterChk"]').prop("checked")
		&& !$('input[name="setChk"]').prop("checked")
		&& !$('input[name="unsetChk"]').prop("checked")) {
		alert("印刷する帳票を選択してください。");
		return false;
	}
	//入力チェック
	if ($('input[name="impletterChk"]').prop("checked")) {
		if ($('input[name="openYm"]').val() == "") {
			alert("オープン月が空です。");
			return false;
		}
		if (!fncCheckDate($('input[name="openYm"]').val(), 'yyyy/mm')) {
			alert("オープン月の形式が不正です。例：2019/01");
			return false;
		}
		// if ($('input[name="shipYm"]').val() == "") {
		// 	alert("船積月が空です。");
		// 	return false;
		// }
		if (!fncCheckDate($('input[name="shipYm"]').val(), 'yyyy/mm')) {
			alert("船積月の形式が不正です。例：2019/01");
			return false;
		}
		if ($('input[name="payfCode"]').val() == "") {
			alert("支払先コードが空です。");
			return false;
		}
	}

	if ($('input[name="setChk"]').prop("checked")) {
		if ($('input[name="objectYm"]').val() == "") {
			alert("対象年月が空です。");
			return false;
		}
		if (!fncCheckDate($('input[name="objectYm"]').val(), 'yyyy/mm')) {
			alert("対象年月の形式が不正です。例：2019/01");
			return false;
		}
	}

	if ($('input[name="unsetChk"]').prop("checked")) {
		if ($('input[name="startDate"]').val() == "") {
			alert("開始年月が空です。");
			return false;
		}
		if (!fncCheckDate($('input[name="startDate"]').val(), 'yyyy/mm/dd')) {
			alert("開始年月の形式が不正です。例：2019/01/01");
			return false;
		}
		if ($('input[name="endDate"]').val() == "") {
			alert("終了年月が空です。");
			return false;
		}
		if (!fncCheckDate($('input[name="endDate"]').val(), 'yyyy/mm/dd')) {
			alert("終了年月の形式が不正です。例：2019/01/01");
			return false;
		}
		if ($('input[name="rate"]').val() == "") {
			alert("円価換算額が空です。");
			return false;
		}
		if (!$.isNumeric($('input[name="rate"]').val())) {
			alert("円価換算額は半角数字で入力してください。");
			return false;
		}
	}
	//出力処理
	$("#masking_loader").css("display", "block");

	$("#masking_loader").find('div').text("ダウンロード中...");

	var bankname = $('select[name="bankcd"] option:selected').text();

	var formData = $('form').serialize();

	// 非同期リクエストの設定
	var url = '/lc/report/download.php?bankname=' + bankname;
	var xhr = new XMLHttpRequest();
	xhr.open('POST', url, true);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.responseType = 'blob'; //blob型のレスポンスを受け付ける

	var fileName = "";
	// コールバック定義
	xhr.onload = function (e) {
		// 成功時の処理
		if (this.status == 200) {
			var blob = this.response;//レスポンス
			var disposition = xhr.getResponseHeader('Content-Disposition');
			if (disposition && disposition.indexOf('attachment') !== -1) {
				var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
				var matches = filenameRegex.exec(disposition);
				if (matches != null && matches[1]) { 
					fileName = matches[1].replace(/['"]/g, '');
				}
			}
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

				//ローダー非表示
				$("#masking_loader").css("display", "none");
			}
		}
	};

	// 非同期リクエストの送信
	xhr.send(formData);
}


function isDate(d, format) {
	if (d == "") { return false; }
	if (!isDateFormat(d, format)) { return false; }
	if (!isValidDate(d, format)) { return false; }
	return true;
}
function isDateFormat(d, format) {
	if (format == 'yyyy/mm') {
		return /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])/.test(d);
	} else {
		return /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])\/([0-2][0-9]|3[0-1])/.test(d);
	}
}
function isValidDate(d, format) {
	if (format == 'yyyy/mm') {
		d = d + '/01';
	}
	console.log(d);
	console.log(format);
	var date = new Date(d);
	if (date.getFullYear() != d.split("/")[0]
		|| date.getMonth() != d.split("/")[1] - 1
		|| date.getDate() != d.split("/")[2]
	) {
		return false;
	}
	return true;
}
function fncCheckDate(date, format) {
	if (date && !isDate(date, format)) {
		console.log("日付チェックエラー：[" + date + "]");
		return false;
	} else {
		console.log("日付チェックOK");
		return true;
	}
}
//---------------------------------------------------
// クリアボタン処理
//---------------------------------------------------
function clearBtn() {
	$(".form-box__contents")
		.find("input, select")
		.val("")
		.prop("checked", false)
		.prop("selected", false);
}

function lcreportInit(json_header_obj, json_main_obj) {
	alert(json_header_obj);
	var reportHeader = JSON.parse(json_header_obj);
	var reportMain = JSON.parse(json_main_obj);
	alert(reportHeader);
	alert(reportHeader.P9);
	// reportData = json_obj;
	alert(reportMain[0].pono);
}
function lcreport6Init(json_obj) {
	console.log(json_obj);
	reportResult = JSON.parse(json_obj);
	var num = 0;
	if (reportResult.report_6.length > 0) {
		for (var i = 0; i < reportResult.report_6.length; i++) {
			var reportCount = reportResult.report_6[i].report_main.length;
			for (var j = 0; j < reportCount; j = j + 15) {
				var startIndex = j;
				var endIndex = j + 14;
				if (endIndex > reportCount) {
					endIndex = reportCount - 1;
				}
				displayLst[num] = ["report_6", "/lc/report/html/6.html", i, startIndex, endIndex];
				num += 1;
			}
		}
	}

	if (reportResult.report_1_open.length > 0) {
		for (var i = 0; i < reportResult.report_1_open.length; i++) {
			var reportCount = reportResult.report_1_open[i].report_main.length;
			for (var j = 0; j < reportCount; j = j + 19) {
				var startIndex = j;
				var endIndex = j + 18;
				if (endIndex > reportCount) {
					endIndex = reportCount - 1;
				}
				displayLst[num] = ["report_1_open", "/lc/report/html/1.html", i, startIndex, endIndex];
				num += 1;
			}
		}
	}

	if (reportResult.report_1_ship.length > 0) {
		for (var i = 0; i < reportResult.report_1_ship.length; i++) {
			var reportCount = reportResult.report_1_ship[i].report_main.length;
			for (var j = 0; j < reportCount; j = j + 19) {
				var startIndex = j;
				var endIndex = j + 18;
				if (endIndex > reportCount) {
					endIndex = reportCount - 1;
				}
				displayLst[num] = ["report_1_ship", "/lc/report/html/1.html", i, startIndex, endIndex];
				num += 1;
			}
		}
	}

	if (reportResult.report_2.length > 0) {
		for (var i = 0; i < reportResult.report_2.length; i++) {
			var reportCount = reportResult.report_2[i].report_main.length;
			for (var j = 0; j < reportCount; j = j + 36) {
				var startIndex = j;
				var endIndex = j + 35;
				if (endIndex > reportCount) {
					endIndex = reportCount - 1;
				}
				displayLst[num] = ["report_2", "/lc/report/html/2.html", i, startIndex, endIndex];
				num += 1;
			}
		}
	}

	if (reportResult.report_3.length > 0) {
		for (var i = 0; i < reportResult.report_3.length; i++) {
			var reportCount = reportResult.report_3[i].report_main.length;
			for (var j = 0; j < reportCount; j = j + 45) {
				var startIndex = j;
				var endIndex = j + 44;
				if (endIndex > reportCount) {
					endIndex = reportCount - 1;
				}
				displayLst[num] = ["report_3", "/lc/report/html/3.html", i, startIndex, endIndex];
				num += 1;
			}
			// displayLst[num] = ["report_3", "/lc/report/html/3.html", i];
			// num += 1;
		}
	}



	if (reportResult.report_4_open.length > 0) {
		for (var i = 0; i < reportResult.report_4_open.length; i++) {
			var reportCount = reportResult.report_4_open[i].report_main.length;
			for (var j = 0; j < reportCount; j = j + 19) {
				var startIndex = j;
				var endIndex = j + 18;
				if (endIndex > reportCount) {
					endIndex = reportCount - 1;
				}
				displayLst[num] = ["report_4_open", "/lc/report/html/4.html", i, startIndex, endIndex];
				num += 1;
			}
		}
	}

	if (reportResult.report_4_ship.length > 0) {
		for (var i = 0; i < reportResult.report_4_ship.length; i++) {
			var reportCount = reportResult.report_4_ship[i].report_main.length;
			for (var j = 0; j < reportCount; j = j + 19) {
				var startIndex = j;
				var endIndex = j + 18;
				if (endIndex > reportCount) {
					endIndex = reportCount - 1;
				}
				displayLst[num] = ["report_4_ship", "/lc/report/html/4.html", i, startIndex, endIndex];
				num += 1;
			}
		}
	}

	if (reportResult.report_5.length > 0) {
		for (var i = 0; i < reportResult.report_5.length; i++) {
			var reportCount = reportResult.report_5[i].report_main.length;
			for (var j = 0; j < reportCount; j = j + 45) {
				var startIndex = j;
				var endIndex = j + 44;
				if (endIndex > reportCount) {
					endIndex = reportCount - 1;
				}
				displayLst[num] = ["report_5", "/lc/report/html/5.html", i, startIndex, endIndex];
				num += 1;
			}
		}
	}
	if (num == 0) {
		this.report.location.href = "/lc/report/html/error.html";
		return;
	}
	console.log(displayLst[0]);
	this.report.location.href = displayLst[0][1];
}

function lcreport() {
	if (displayLst.length > 0 && nextIndex < displayLst.length) {
		var index = displayLst[nextIndex][2];
		switch (displayLst[nextIndex][0]) {
			case "report_6":
				setReport6Data(reportResult.report_6[index], displayLst[nextIndex][3], displayLst[nextIndex][4]);
				break;
			case "report_1_open":
				setReport1Data(reportResult.report_1_open[index], displayLst[nextIndex][3], displayLst[nextIndex][4]);
				break;
			case "report_1_ship":
				setReport1Data(reportResult.report_1_ship[index], displayLst[nextIndex][3], displayLst[nextIndex][4]);
				break;
			case "report_2":
				setReport2Data(reportResult.report_2[index], displayLst[nextIndex][3], displayLst[nextIndex][4]);
				break;
			case "report_3":
				setReport3Data(reportResult.report_3[index], displayLst[nextIndex][3], displayLst[nextIndex][4]);
				break;
			case "report_4_open":
				setReport4Data(reportResult.report_4_open[index], displayLst[nextIndex][3], displayLst[nextIndex][4]);
				break;
			case "report_4_ship":
				setReport4Data(reportResult.report_4_ship[index], displayLst[nextIndex][3], displayLst[nextIndex][4]);
				break;
			case "report_5":
				setReport5Data(reportResult.report_5[index], displayLst[nextIndex][3], displayLst[nextIndex][4]);
				break;
		}


		if (nextIndex + 1 < displayLst.length) {
			parent.button.location.href = "button.php?strSessionID=" + reportResult.sessionid + "&printObj=report&nextUrl=" + displayLst[nextIndex + 1][1];
		} else {
			parent.button.location.href = "button.php?strSessionID=" + reportResult.sessionid + "&printObj=report&nextUrl=";
		}

		nextIndex += 1;
	}

}

/**
 * 帳票_1_表示内容を設定する
 * @param {*} reportData 
 */
function setReport1Data(reportData, startIndex, endIndex) {
	document.getElementById('report').contentWindow.document.title = reportData.report_header.header;
	var tblTbody = document.getElementById('report').contentWindow.document.getElementById('report_tbody');
	var tblCaption = document.getElementById('report').contentWindow.document.getElementById('report_caption');
	// tblCaption.innerHTML = reportData.report_header.header;
	var currencyclass = reportData.report_header.currencyclass;
	tblTbody.rows[3].cells[0].innerHTML = convertNull(reportData.report_header.A4);
	tblTbody.rows[4].cells[0].innerHTML = convertNull(reportData.report_header.F5);
	tblTbody.rows[6].cells[0].innerHTML = convertNull(reportData.report_header.B7);
	tblTbody.rows[6].cells[1].innerHTML = convertNull(reportData.report_header.C7);
	tblTbody.rows[6].cells[2].innerHTML = convertNull(reportData.report_header.D7);
	tblTbody.rows[6].cells[3].innerHTML = convertNull(reportData.report_header.E7);

	var reportmain = reportData.report_main;
	var row = 7;
	var bank1total = 0;
	var bank2total = 0;
	var bank3total = 0;
	var bank4total = 0;
	var total = 0;
	for (var i = startIndex; i < endIndex + 1; i++) {
		tblTbody.rows[row].cells[0].innerHTML = convertNull(reportmain[i].beneficiary);
		tblTbody.rows[row].cells[1].innerHTML = convertNumberByClass(reportmain[i].bank1, currencyclass);
		tblTbody.rows[row].cells[2].innerHTML = convertNumberByClass(reportmain[i].bank2, currencyclass);
		tblTbody.rows[row].cells[3].innerHTML = convertNumberByClass(reportmain[i].bank3, currencyclass);
		tblTbody.rows[row].cells[4].innerHTML = convertNumberByClass(reportmain[i].bank4, currencyclass);
		tblTbody.rows[row].cells[5].innerHTML = convertNumberByClass(reportmain[i].total, currencyclass);
		row += 1;
		bank1total += Number(reportmain[i].bank1);
		bank2total += Number(reportmain[i].bank2);
		bank3total += Number(reportmain[i].bank3);
		bank4total += Number(reportmain[i].bank4);
		total += Number(reportmain[i].total);
	}


	tblTbody.rows[26].cells[1].innerHTML = convertNumberByClass(bank1total, currencyclass);
	tblTbody.rows[26].cells[2].innerHTML = convertNumberByClass(bank2total, currencyclass);
	tblTbody.rows[26].cells[3].innerHTML = convertNumberByClass(bank3total, currencyclass);
	tblTbody.rows[26].cells[4].innerHTML = convertNumberByClass(bank4total, currencyclass);
	tblTbody.rows[26].cells[5].innerHTML = convertNumberByClass(total, currencyclass);
}


/**
 * 帳票_2_表示内容を設定する
 * @param {*} reportData 
 */
function setReport2Data(reportData, startIndex, endIndex) {
	var tblTbody = document.getElementById('report').contentWindow.document.getElementById('report_tbody');
	var currencyclass = reportData.report_header.currencyclass;
	tblTbody.rows[0].cells[0].innerHTML = convertNull(reportData.report_header.A1);
	tblTbody.rows[1].cells[6].innerHTML = convertNull(reportData.report_header.H2);

	var reportmain = reportData.report_main;
	var row = 4;
	var total = 0;
	for (var i = startIndex; i < endIndex + 1; i++) {
		tblTbody.rows[row].cells[0].innerHTML = convertNull(reportmain[i].lcno);
		tblTbody.rows[row].cells[1].innerHTML = convertNull(reportmain[i].factoryname);
		tblTbody.rows[row].cells[2].innerHTML = convertNumberByClass(reportmain[i].price, currencyclass);
		tblTbody.rows[row].cells[3].innerHTML = convertNull(reportmain[i].shipterm);
		tblTbody.rows[row].cells[4].innerHTML = convertNull(reportmain[i].validterm);
		tblTbody.rows[row].cells[5].innerHTML = convertNull(reportmain[i].bankname);
		tblTbody.rows[row].cells[6].innerHTML = convertNull(reportmain[i].bankreqdate);
		tblTbody.rows[row].cells[7].innerHTML = convertNull(reportmain[i].lcamopen);
		row += 1;
		total += Number(reportmain[i].price);
	}

	tblTbody.rows[1].cells[2].innerHTML = convertNumberByClass(total, currencyclass);

}



/**
 * 帳票_3_表示内容を設定する
 * @param {*} reportData 
 */
function setReport3Data(reportData, startIndex, endIndex) {
	var tblThead = document.getElementById('report').contentWindow.document.getElementById('report_thead');
	var tblTbody = document.getElementById('report').contentWindow.document.getElementById('report_tbody');
	var currencyclass = reportData.report_header.currencyclass;
	tblThead.rows[0].cells[2].innerHTML = convertNull(reportData.report_header.C1);
	tblThead.rows[0].cells[13].innerHTML = convertNull(reportData.report_header.Q1);

	var reportmain = reportData.report_main;
	var row = 0;
	var total = 0;
	for (var i = startIndex; i < endIndex + 1; i++) {
		tblTbody.rows[row].cells[0].innerHTML = convertNull(reportmain[i].lcno);
		tblTbody.rows[row].cells[1].innerHTML = convertNull(reportmain[i].pono);
		tblTbody.rows[row].cells[2].innerHTML = convertNull(reportmain[i].factoryname);
		tblTbody.rows[row].cells[3].innerHTML = convertNull(reportmain[i].productcd);
		tblTbody.rows[row].cells[4].innerHTML = convertNull(reportmain[i].productname);
		tblTbody.rows[row].cells[5].innerHTML = convertNull(reportmain[i].productnumber);
		tblTbody.rows[row].cells[6].innerHTML = convertNull(reportmain[i].unitname);
		tblTbody.rows[row].cells[7].innerHTML = convertNull(reportmain[i].unitprice);
		tblTbody.rows[row].cells[8].innerHTML = convertNumberByClass(reportmain[i].moneyprice, currencyclass);
		tblTbody.rows[row].cells[9].innerHTML = convertNull(reportmain[i].shipstartdate);
		tblTbody.rows[row].cells[10].innerHTML = convertNull(reportmain[i].shipenddate);
		tblTbody.rows[row].cells[11].innerHTML = convertNull(reportmain[i].portplace);
		tblTbody.rows[row].cells[12].innerHTML = convertNull(reportmain[i].shipterm);
		tblTbody.rows[row].cells[13].innerHTML = convertNull(reportmain[i].validterm);
		tblTbody.rows[row].cells[14].innerHTML = convertNull(reportmain[i].bankname);
		tblTbody.rows[row].cells[15].innerHTML = convertNull(reportmain[i].bankreqdate);
		tblTbody.rows[row].cells[16].innerHTML = convertNull(reportmain[i].lcamopen);
		row += 1;
		total += Number(reportmain[i].moneyprice);
	}
	tblThead.rows[0].cells[7].innerHTML = convertNumberByClass(total, currencyclass);
}

/**
 * 帳票_4_表示内容を設定する
 * @param {*} reportData 
 */
function setReport4Data(reportData, startIndex, endIndex) {
	document.getElementById('report').contentWindow.document.title = reportData.report_header.header;
	var tblTbody = document.getElementById('report').contentWindow.document.getElementById('report_tbody');
	var tblCaption = document.getElementById('report').contentWindow.document.getElementById('report_caption');
	// tblCaption.innerHTML = reportData.report_header.header;
	var currencyclass = reportData.report_header.currencyclass;
	tblTbody.rows[0].cells[11].innerHTML = convertNull(reportData.report_header.M1);
	tblTbody.rows[2].cells[1].innerHTML = convertNull(reportData.report_header.B3);
	tblTbody.rows[2].cells[2].innerHTML = convertNull(reportData.report_header.C3);
	tblTbody.rows[2].cells[3].innerHTML = convertNull(reportData.report_header.D3);
	tblTbody.rows[2].cells[4].innerHTML = convertNull(reportData.report_header.E3);
	tblTbody.rows[2].cells[5].innerHTML = convertNull(reportData.report_header.F3);
	tblTbody.rows[2].cells[6].innerHTML = convertNull(reportData.report_header.G3);
	tblTbody.rows[2].cells[7].innerHTML = convertNull(reportData.report_header.H3);
	tblTbody.rows[2].cells[8].innerHTML = convertNull(reportData.report_header.I3);
	tblTbody.rows[2].cells[9].innerHTML = convertNull(reportData.report_header.J3);
	tblTbody.rows[2].cells[10].innerHTML = convertNull(reportData.report_header.K3);
	tblTbody.rows[2].cells[11].innerHTML = convertNull(reportData.report_header.L3);

	var reportmain = reportData.report_main;
	var row = 3;
	var date1total = 0;
	var date2total = 0;
	var date3total = 0;
	var date4total = 0;
	var date5total = 0;
	var date6total = 0;
	var date7total = 0;
	var date8total = 0;
	var date9total = 0;
	var date10total = 0;
	var date11total = 0;
	var total = 0;
	for (var i = 0; i < reportmain.length; i++) {
		tblTbody.rows[row].cells[0].innerHTML = convertNull(reportmain[i].beneficiary);
		tblTbody.rows[row].cells[1].innerHTML = convertNumberByClass(reportmain[i].date1, currencyclass);
		tblTbody.rows[row].cells[2].innerHTML = convertNumberByClass(reportmain[i].date2, currencyclass);
		tblTbody.rows[row].cells[3].innerHTML = convertNumberByClass(reportmain[i].date3, currencyclass);
		tblTbody.rows[row].cells[4].innerHTML = convertNumberByClass(reportmain[i].date4, currencyclass);
		tblTbody.rows[row].cells[5].innerHTML = convertNumberByClass(reportmain[i].date5, currencyclass);
		tblTbody.rows[row].cells[6].innerHTML = convertNumberByClass(reportmain[i].date6, currencyclass);
		tblTbody.rows[row].cells[7].innerHTML = convertNumberByClass(reportmain[i].date7, currencyclass);
		tblTbody.rows[row].cells[8].innerHTML = convertNumberByClass(reportmain[i].date8, currencyclass);
		tblTbody.rows[row].cells[9].innerHTML = convertNumberByClass(reportmain[i].date9, currencyclass);
		tblTbody.rows[row].cells[10].innerHTML = convertNumberByClass(reportmain[i].date10, currencyclass);
		tblTbody.rows[row].cells[11].innerHTML = convertNumberByClass(reportmain[i].date11, currencyclass);
		tblTbody.rows[row].cells[12].innerHTML = convertNumberByClass(reportmain[i].total, currencyclass);
		row++;
		date1total += Number(reportmain[i].date1);
		date2total += Number(reportmain[i].date2);
		date3total += Number(reportmain[i].date3);
		date4total += Number(reportmain[i].date4);
		date5total += Number(reportmain[i].date5);
		date6total += Number(reportmain[i].date6);
		date7total += Number(reportmain[i].date7);
		date8total += Number(reportmain[i].date8);
		date9total += Number(reportmain[i].date9);
		date10total += Number(reportmain[i].date10);
		date11total += Number(reportmain[i].date11);
		total += Number(reportmain[i].total);
	}

	tblTbody.rows[22].cells[1].innerHTML = convertNumberByClass(date1total, currencyclass);
	tblTbody.rows[22].cells[2].innerHTML = convertNumberByClass(date2total, currencyclass);
	tblTbody.rows[22].cells[3].innerHTML = convertNumberByClass(date3total, currencyclass);
	tblTbody.rows[22].cells[4].innerHTML = convertNumberByClass(date4total, currencyclass);
	tblTbody.rows[22].cells[5].innerHTML = convertNumberByClass(date5total, currencyclass);
	tblTbody.rows[22].cells[6].innerHTML = convertNumberByClass(date6total, currencyclass);
	tblTbody.rows[22].cells[7].innerHTML = convertNumberByClass(date7total, currencyclass);
	tblTbody.rows[22].cells[8].innerHTML = convertNumberByClass(date8total, currencyclass);
	tblTbody.rows[22].cells[9].innerHTML = convertNumberByClass(date9total, currencyclass);
	tblTbody.rows[22].cells[10].innerHTML = convertNumberByClass(date10total, currencyclass);
	tblTbody.rows[22].cells[11].innerHTML = convertNumberByClass(date11total, currencyclass);
	tblTbody.rows[22].cells[12].innerHTML = convertNumberByClass(total, currencyclass);
}

/**
 * 帳票_5_表示内容を設定する
 * @param {*} reportData 
 */
function setReport5Data(reportData, startIndex, endIndex) {
	var tblTbody = document.getElementById('report').contentWindow.document.getElementById('report_tbody');
	var currencyclass = reportData.report_header.currencyclass;
	tblTbody.rows[1].cells[1].innerHTML = convertNull(reportData.report_header.B2);
	tblTbody.rows[2].cells[1].innerHTML = convertNull(reportData.report_header.B3);
	tblTbody.rows[2].cells[12].innerHTML = convertNull(reportData.report_header.M3);

	var A5 = reportData.report_header.A5;
	var row = 4;
	for (var i = 0; i < A5.length; i++) {
		tblTbody.rows[row].cells[0].innerHTML = convertNull(A5[i].Beneficiary);
		tblTbody.rows[row].cells[1].innerHTML = convertNumberByClass(A5[i].bank1, currencyclass);
		tblTbody.rows[row].cells[2].innerHTML = convertNumberByClass(A5[i].bank2, currencyclass);
		tblTbody.rows[row].cells[3].innerHTML = convertNumberByClass(A5[i].bank3, currencyclass);
		tblTbody.rows[row].cells[4].innerHTML = convertNumberByClass(A5[i].bank4, currencyclass);
		tblTbody.rows[row].cells[5].innerHTML = convertNumberByClass(A5[i].unapprovaltotal, currencyclass);
		tblTbody.rows[row].cells[6].innerHTML = convertNumberByClass(A5[i].benetotal, currencyclass);
		row++;
	}

	tblTbody.rows[22].cells[1].innerHTML = convertNumberByClass(reportData.report_header.B23, currencyclass);
	tblTbody.rows[22].cells[2].innerHTML = convertNumberByClass(reportData.report_header.C23, currencyclass);
	tblTbody.rows[22].cells[3].innerHTML = convertNumberByClass(reportData.report_header.D23, currencyclass);
	tblTbody.rows[22].cells[4].innerHTML = convertNumberByClass(reportData.report_header.E23, currencyclass);
	tblTbody.rows[22].cells[5].innerHTML = convertNumberByClass(reportData.report_header.F23, currencyclass);
	tblTbody.rows[22].cells[6].innerHTML = convertNumberByClass(reportData.report_header.G23, currencyclass);
	tblTbody.rows[25].cells[0].innerHTML = convertNumberByClass(reportData.report_header.A26, currencyclass);


	tblTbody.rows[3].cells[1].innerHTML = convertNull(reportData.report_header.B4);
	tblTbody.rows[3].cells[2].innerHTML = convertNull(reportData.report_header.C4);
	tblTbody.rows[3].cells[3].innerHTML = convertNull(reportData.report_header.D4);
	tblTbody.rows[3].cells[4].innerHTML = convertNull(reportData.report_header.E4);


	tblTbody.rows[25].cells[1].innerHTML = convertNumberByClass(reportData.report_header.B26, currencyclass);
	tblTbody.rows[25].cells[2].innerHTML = convertNumberByClass(reportData.report_header.C26, currencyclass);
	tblTbody.rows[25].cells[3].innerHTML = convertNumberByClass(reportData.report_header.D26, currencyclass);
	tblTbody.rows[25].cells[4].innerHTML = convertNumberByClass(reportData.report_header.E26, currencyclass);
	tblTbody.rows[25].cells[5].innerHTML = convertNumberByClass(reportData.report_header.F26, currencyclass);
	tblTbody.rows[25].cells[6].innerHTML = convertNumberByClass(reportData.report_header.G26, currencyclass);

	var reportmain = reportData.report_main;
	row = 4;
	for (var i = startIndex; i < endIndex + 1; i++) {
		tblTbody.rows[row].cells[8].innerHTML = convertNull(reportmain[i].bankname);
		tblTbody.rows[row].cells[9].innerHTML = convertNull(reportmain[i].payeeformalname);
		tblTbody.rows[row].cells[10].innerHTML = convertNull(reportmain[i].shipstartdate);
		tblTbody.rows[row].cells[11].innerHTML = convertNull(reportmain[i].lcno);
		tblTbody.rows[row].cells[12].innerHTML = convertNull(reportmain[i].productcode);
		tblTbody.rows[row].cells[13].innerHTML = convertNumberByClass(reportmain[i].usancesettlement, currencyclass);
		row += 1;
	}
}

/**
 * 帳票_6_表示内容を設定する
 * @param {*} reportData 
 */
function setReport6Data(reportData, startIndex, endIndex) {
	var tblTbody = document.getElementById('report').contentWindow.document.getElementById('report_tbody');
	var currencyclass = reportData.report_header.currencyclass;
	tblTbody.rows[8].cells[14].innerHTML = convertNull(reportData.report_header.P9);
	tblTbody.rows[6].cells[1].innerHTML = convertNull(reportData.report_header.B7);
	tblTbody.rows[7].cells[7].innerHTML = convertNull(reportData.report_header.M8);
	tblTbody.rows[7].cells[2].innerHTML = convertNull(reportData.report_header.D8);
	tblTbody.rows[7].cells[4].innerHTML = convertNull(reportData.report_header.H8);
	tblTbody.rows[7].cells[6].innerHTML = convertNull(reportData.report_header.L8);
	tblTbody.rows[7].cells[9].innerHTML = convertNull(reportData.report_header.O8);


	tblTbody.rows[9].cells[14].innerHTML = convertNull(reportData.report_header.P10);
	tblTbody.rows[2].cells[1].innerHTML = convertNull(reportData.report_header.B3);
	tblTbody.rows[3].cells[1].innerHTML = convertNull(reportData.report_header.B4);
	tblTbody.rows[1].cells[2].innerHTML = convertNull(reportData.report_header.G2);
	tblTbody.rows[2].cells[2].innerHTML = convertNull(reportData.report_header.H3);
	tblTbody.rows[1].cells[1].innerHTML = convertNull(reportData.report_header.B2);
	tblTbody.rows[41].cells[7].innerHTML = convertNull(reportData.report_header.H42);
	if (reportData.report_main.length > 15) {
		tblTbody.rows[26].cells[3].innerHTML = "SUB TOTAL AMOUNT";
	}


	tblTbody.rows[7].cells[10].innerHTML = "（" + Math.ceil((endIndex + 1) / 15) + "／" + Math.ceil(reportData.report_main.length / 15) + "）";;

	var reportmain = reportData.report_main;
	var row = 11;
	var totalprice = 0
	for (var i = startIndex; i < endIndex + 1; i++) {
		tblTbody.rows[row].cells[0].innerHTML = convertNull(reportmain[i].bankreqdate);
		tblTbody.rows[row].cells[1].innerHTML = convertNull(reportmain[i].pono);
		tblTbody.rows[row].cells[2].innerHTML = convertNull(reportmain[i].productcd);
		tblTbody.rows[row].cells[3].innerHTML = convertNull(reportmain[i].productname);
		tblTbody.rows[row].cells[4].innerHTML = convertNull(reportmain[i].productnumber);
		tblTbody.rows[row].cells[5].innerHTML = convertNull(reportmain[i].unitname);
		tblTbody.rows[row].cells[6].innerHTML = convertNull(reportmain[i].unitprice);
		tblTbody.rows[row].cells[7].innerHTML = convertNumberByClass(reportmain[i].moneyprice, currencyclass);
		tblTbody.rows[row].cells[8].innerHTML = convertNull(reportmain[i].shipstartdate);
		tblTbody.rows[row].cells[9].innerHTML = convertNull(reportmain[i].shipenddate);
		tblTbody.rows[row].cells[10].innerHTML = convertNull(reportmain[i].shipterm);
		tblTbody.rows[row].cells[11].innerHTML = convertNull(reportmain[i].validterm);
		tblTbody.rows[row].cells[12].innerHTML = convertNull(reportmain[i].lcno);
		tblTbody.rows[row].cells[13].innerHTML = convertNull(reportmain[i].reckoninginitialdate);
		tblTbody.rows[row].cells[14].innerHTML = convertNull(reportmain[i].portplace);
		tblTbody.rows[row].cells[15].innerHTML = convertNull(reportmain[i].bankname);
		row += 1;
		totalprice += Number(reportmain[i].moneyprice);
	}
	tblTbody.rows[26].cells[7].innerHTML = convertNumberByClass(totalprice, currencyclass);

}

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

function convertNumberByClass(str, currencyclass) {
	if (str != "" && str != undefined && str != "null") {
		if (currencyclass == '円') {
			return Number(str).toLocaleString(undefined, {
				minimumFractionDigits: 0,
				maximumFractionDigits: 0
			});
		} else {
			return Number(str).toLocaleString(undefined, {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			});
		}
	} else {
		return "";
	}
}


(function () {
	$(window).on("beforeunload", function (e) {
		window.opener.location.href = '/lc/info/index.php?strSessionID=' + phpData["session_id"] + '&reSearchFlg=true';
	});
})();