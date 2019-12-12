

//@-------------------------------------------------------------------------------------------------------------------
/**
* �ե����복�� : LC��Ϣ����
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

//�����ѿ�
var session_id;
var getDataModeFlg = 0; //0:���ǡ��� 1:ͭ���ǡ���
var phpData;// PHP����μ����ǡ���
var lcInfoHit;
var getInfoFlg;//��Хե饰
var tableData;//�ơ��֥�ǡ�����������

//---------------------------------------------------
// ���̽������
//---------------------------------------------------
function lcInit(json_obj) {
	phpData = $.parseJSON(json_obj);
	if (phpData.reSearchFlg == "true" && $.cookie("lcInfoSearchConditions") != undefined) {
		//¾���̤�������ʤɤǡ���ư���θ������ǺƸ���������
		//��о���cookie�������
		getSearchConditions();

		//��Хե饰����Ƥ������н���
		getLcInfo(1, 2);
	} else {
		//��о���cookie������
		delSearchConditions();
		//���︡��
		getLcInfo(0);
	}


}

//---------------------------------------------------
// LC����ơ��֥�Ŭ��
//---------------------------------------------------
function setLcInfoTable(data, phpData) {
	//��¸�ǡ����κ��
	$("#lc_table_body").empty();
	$("#lc_table_radio").empty();

	for (var i = 0; i < data.length; i++) {
		//�ԥǡ���
		var row = data[i];

		//L/C����ξ��� = 0/4/8�ξ�硢�嵭�Ǽ�������strclrstatus = 0 ���طʿ������ꤹ��
		//L/C����ξ��� = 1�ξ�硢�嵭�Ǽ�������strclrstatus = 1 ���طʿ������ꤹ��
		//L/C����ξ��� = 2/5/10�ξ�硢�嵭�Ǽ�������strclrstatus = 2 ���طʿ������ꤹ��
		//L/C����ξ��� = 3�ξ�硢�嵭�Ǽ�������strclrstatus = 3 ���طʿ������ꤹ��
		//L/C����ξ��� = 6�ξ�硢�嵭�Ǽ�������strclrstatus = 6 ���طʿ������ꤹ��
		//L/C����ξ��� = 7�ξ�硢�嵭�Ǽ�������strclrstatus = 7 ���طʿ������ꤹ��
		//L/C����ξ��� = 9�ξ�硢�嵭�Ǽ�������strclrstatus = 9 ���طʿ������ꤹ��
		var background_color = "255,255,255";//�ǥե����
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
			//�طʿ�����
			background_color = background_color_data["lngcolorred"] + "," + background_color_data["lngcolorgreen"] + "," + background_color_data["lngcolorblue"];
		}

		var lc_table_radio = '<tr>' +
			'<td style="padding-left: 8px;"><input type="radio" name="selectRow" value="' + (i+1) + '" class="form-control form-control-sm" style="width:12px;height: 15px;background-color: #f0f0f6;"></td>' +
			'</tr>';
		$("#lc_table_radio").append(lc_table_radio);

		var lc_table_body = '<tr id="' + i + '">' +
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
			'<td style="background-color: rgb(' + background_color + ');text-align:right;">' + convertNumber(row.productnumber, "", 0) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.unitname) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNumber(row.unitprice, "", 4) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');text-align:right;">' + convertNumber(row.moneyprice, row.currencyclass, 0) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.currencyclass) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.shipstartdate) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.shipenddate) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.sumdate) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.poupdatedate) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.deliveryplace) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.lcnote) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.shipterm) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.validterm) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.bankname) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.bankreqdate) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNull(row.lcno) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.lcamopen) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.validmonth) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNumber(row.usancesettlement, row.currencyclass, 0) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.bldetail1date) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNumber(row.bldetail1money, row.currencyclass, 0) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.bldetail2date) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNumber(row.bldetail2money, row.currencyclass, 0) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertDate(row.bldetail3date) + '</td>' +
			'<td style="background-color: rgb(' + background_color + ');">' + convertNumber(row.bldetail3money, row.currencyclass, 0) + '</td>' +
			'<td style="display:none;">' + row.lcstate + '</td>' +
			'</tr>';
		$("#lc_table_body").append(lc_table_body);
	}
	console.log(data.length);
	for (var i = 0; i <= data.length; i++) {
		var height = $("#lc_table_radio tr:nth-child(" + i + ") td:nth-child(1)").height();
		$("#lc_table_body tr:nth-child(" + i + ") td:nth-child(1)").height(height);
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

		console.log(body_width);
		$("[name='T']").width(width + 250);
	}



	//0��ξ��ϥ��顼
	lcInfoHit = true;
	if (data == false) {
		lcInfoHit = false;
		alert("���˥ҥåȤ���L/C����ϸ��Ĥ���ޤ���");
	}

	//���������
	$("#masking_loader").css("display", "none");
}

//---------------------------------------------------
// ��н���
//---------------------------------------------------
function getLcInfo(mode, type = 1) {

	var error = false;
	var error_msg = "";
	//mode��0 ����, 1 �������ޤ���н���
	if (mode == 1) {
		var from = $("#startYm").val().replace("/", "");
		var to = $("#endYm").val().replace("/", "");

		if (from == "" && to == "" && $("#payfCode").val() == "" && $("#payfName").val() == "" && type == 1) {
			error = true;
			error_msg += "�������Ϥ��Ƥ���������\r\n";
		}

		//ǯ���FROM�����դǤϤʤ���硢���顼
		if (from != "" && !from.match(/(\d{4})(\d{2})/)) {
			error = true;
			error_msg += "FROM�η����������Ǥ����㡧2019/01\r\n";
		}
		//ǯ���TO�����դǤϤʤ���硢���顼
		if (to != "" && !to.match(/(\d{4})(\d{2})/)) {
			error = true;
			error_msg += "TO�η����������Ǥ����㡧2019/01\r\n";
		}
		//ǯ���FROM�ˡ�ǯ���TO�ˤξ�硢���顼
		if (from != "" && to != "" && to < from) {
			error = true;
			error_msg += "TO��FROM���̤������ꤷ�Ƥ���������\r\n";
		}

		//��о���cookie�˳���
		saveSearchConditions();
		getInfoFlg = 1;
	} else if (mode == 0) {
		getInfoFlg = 0;
	}


	if (error == false) {
		//LC�������
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
				// Ajax�ꥯ�����Ȥ�����
				var data = JSON.parse(data);
				tableData = data;
				setLcInfoTable(data, phpData);
				setBtnControll();
			})
			.fail(function (data) {
				alert("fail");
				// Ajax�ꥯ�����Ȥ�����
			});
	} else {
		//�����ե����२�顼����
		alert(error_msg);
	}

}

//---------------------------------------------------
// ���ߥ�졼�Ƚ���
//---------------------------------------------------
function getSimulateLcInfo() {

	var to = $("#simulateYm").val().replace("/", "");

	//ǯ����ξ�硢���顼
	var error = false;
	var error_msg = "";
	if (to == "") {
		error = true;
		error_msg += "ǯ����Ǥ���\r\n";
	}
	//ǯ�����դǤϤʤ���硢���顼
	if (to != "" && !to.match(/(\d{4})(\d{2})/)) {
		error = true;
		error_msg += "ǯ��η����������Ǥ����㡧2019/01\r\n";
	}

	if (error == false) {
		//LC�������
		$("#masking_loader").css("display", "block");
		$.ajax({
			url: '../lcModel/lcinfo_ajax.php',
			type: 'POST',
			data: {
				'method': 'getSimulateLcInfo',
				'mode': 2,
				'getDataModeFlg': getDataModeFlg,
				'to': to,
				'sessionid': phpData["session_id"]
			}
		})
			.done(function (data) {
				// Ajax�ꥯ�����Ȥ�����
				var data = JSON.parse(data);
				tableData = data;
				setLcInfoTable(data, phpData);
				setBtnControll();
			})
			.fail(function () {
				// Ajax�ꥯ�����Ȥ�����
			});
		$("#masking_loader").css("display", "none");
	} else {
		//�����ե����२�顼����
		alert(error_msg);
	}
}

//---------------------------------------------------
// �Խ�����
//---------------------------------------------------
function openEdit() {
	//�Ԥ����򤵤�Ƥ��뤫�ɤ���
	var sel = $('input[name=selectRow]:checked').val();
	if (sel == undefined) {
		alert("�Խ�����L/C��������򤷤Ƥ���������");
		return;
	}
	var row = $('#lc_table_body tr:nth-child(' + sel + ')');
	console.log(row.text());
	var pono = row.find('td:nth-child(4)').text();
	var polineno = row.find('td:nth-child(5)').text();
	var poreviseno = row.find('td:nth-child(6)').text();
	var lcstate = row.find('td:nth-child(37)').text();
	
	if (lcstate != 0 &&
		lcstate != 3 &&
		lcstate != 4 &&
		lcstate != 7 &&
		lcstate != 8 &&
		lcstate != 9) {
		alert("���򤵤줿L/C������Խ����뤳�Ȥ�����ޤ���");
	} else {
		location.href = '/lc/edit/index.php?strSessionID=' + phpData["session_id"] + '&pono=' + pono + '&polineno='
		 + polineno + '&poreviseno=' + poreviseno;
	}
}

//---------------------------------------------------
// ���ꥢ����
//---------------------------------------------------
function formClear() {
	$("#startYm").val("");
	$("#endYm").val("");
	$("#payfCode").val("");
	$("#payfName").val("");
}

//---------------------------------------------------
//����ΰ��֤�ʸ������
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
 * ʸ���Ѵ���null�ξ�硢""���Ѵ���
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
//ͭ�������ǡ����ܥ��󥤥٥��
//---------------------------------------------------
function switchGetModeBtn(flg, serch_flg) {
	$("#allBtn").removeClass("selected-btn");
	$("#validBtn").removeClass("selected-btn");
	getDataModeFlg = flg;
	if (getDataModeFlg == 1) {
		//ͭ���ǡ���
		$("#validBtn").addClass("selected-btn");
	} else {
		//���ǡ���
		$("#allBtn").addClass("selected-btn");
	}

	//��м¹�
	if (serch_flg) {
		getLcInfo(1, 2);
	}
}



//---------------------------------------------------
//ȿ�ǥǡ����ܥ��󥤥٥��
//---------------------------------------------------
function reflectLcInfo() {
	// ȿ�ǥ��٥�Ȥν������
	$.ajax({
		url: '../info/reflect.php',
		type: 'POST',
		data: {
			'strSessionID': phpData["session_id"],
			'mode': 3
		}
	})
		.done(function (data) {
			// Ajax�ꥯ�����Ȥ�����
			var data = JSON.parse(data);
			// ��������lgoutymd�����ǤϤʤ���硢��¾�����å�������ɽ������������λ
			if (data.lgoutymd != null) {
				alert("��¾���椬�������ޤ����������С��ξ���򹹿�������ϥ�����ľ���Ƥ���������");
				return;
			} else {
				// ��������lgoutymd�����ξ�硢ȿ�ǳ�ǧ��å�������ɽ������
				if (res = confirm("������������򥵡��Ф�ȿ�Ǥ��ޤ�����")) {
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
								alert("ȿ�Ǥ���λ���ޤ�����");
							} else {
								alert("ȿ�Ǥǥ��顼��ȯ�����ޤ�����");
							}
						})
						.fail(function () {
							// Ajax�ꥯ�����Ȥ�����
						});
					//���������
					$("#masking_loader").css("display", "none");
				} else {
					return;
				}
			}
		})
		.fail(function () {
			alert("fail");
			// Ajax�ꥯ�����Ȥ�����
		});
}


//---------------------------------------------------
//�ƥܥ�������
//---------------------------------------------------
function setBtnControll() {
	//���ߥ�졼�ȥܥ���
	if (!lcInfoHit && getInfoFlg == 1) {
		$("#simulateBtn").prop("disabled", true);
	} else {
		$("#simulateBtn").prop("disabled", false);
	}
	//�Խ��ܥ���
	if (lcInfoHit) {
		$("#editBtn").prop("disabled", false);
	} else {
		$("#editBtn").prop("disabled", true);
	}
	//�������ݡ��ȥܥ���
	if (lcInfoHit) {
		$("#exportBtn").prop("disabled", false);
	} else {
		$("#exportBtn").prop("disabled", true);
	}
	//Ģɼ���ϥܥ���
	if (lcInfoHit) {
		$("#reportOutputBtn").prop("disabled", false);
	} else {
		$("#reportOutputBtn").prop("disabled", true);
	}
	//ȿ�ǥܥ���
	if (lcInfoHit && phpData["userAuth"] == 1) {
		$("#reflectionBtn").prop("disabled", false);
	} else {
		$("#reflectionBtn").prop("disabled", true);
	}
}

//---------------------------------------------------
//���������¸
//---------------------------------------------------
function saveSearchConditions() {
	var search_conditions = {
		'from': $("#startYm").val(),
		'to': $("#endYm").val(),
		'payfcd': $("#payfCode").val(),
		'payfnameomit': $("#payfName").val(),
		'simulateYm': $("#simulateYm").val(),
		'getDataModeFlg': getDataModeFlg
	};
	$.cookie('lcInfoSearchConditions', JSON.stringify(search_conditions));
}

//---------------------------------------------------
//�������ȿ��
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
//���������
//---------------------------------------------------
function delSearchConditions() {
	$.removeCookie("lcInfoSearchConditions");
}

//---------------------------------------------------
// Ģɼ���ϥܥ������
//---------------------------------------------------
function reportOutputBtn() {
	location.href = '/lc/report/index.php?strSessionID=' + phpData["session_id"] + '&openDate=' + $("#simulateYm").val();
}

/**
 * ����ݡ��ȥܥ������
 */
function chooseFile() {
	$('#txtFile').click();
}

/**
 * ����ݡ��ȤǤ�����ե������ѹ����ν���
 */
function importFile() {
	var fd = new FormData();
	fd.append("txtfile", $("#txtFile").prop("files")[0]);
	fd.append("sessionid", phpData["session_id"]);
	$("#masking_loader").css("display", "block");
	$.ajax({
		url: '/lc/info/import.php',
		type: 'POST',
		data: fd,
		processData: false,
		contentType: false,

	})
		.done(function (data) {
			if (data == "success") {
				alert("����ݡ��Ȥ���λ���ޤ�����");
				getLcInfo(0);
			} else {
				alert("����ݡ��Ȥǥ��顼��ȯ�����ޤ�����");
			}
		})
		.fail(function () {
			alert("fail");
			// Ajax�ꥯ�����Ȥ�����
		});
	//���������
	$("#masking_loader").css("display", "none");
}

/**
 * �������ݡ��ȥܥ������
 */
function exportFile() {
	// �ե����륨�����ݡ��Ƚ�����Ԥ�
	location.href = '/lc/info/export.php?sessionid=' + phpData["session_id"] +
		'&mode=1' +
		'&from=' + $("#startYm").val() +
		'&to=' + $("#endYm").val() +
		'&payfcd=' + $("#payfCode").val() +
		'&getDataModeFlg=' + getDataModeFlg;
}

function convertNumber(str, currencyclass, fracctiondigits) {
	if (str != "" && str != undefined && str != "null") {
		if (currencyclass != "") {
			if (currencyclass == '��') {
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
			return Number(str).toLocaleString(undefined, {
				minimumFractionDigits: fracctiondigits,
				maximumFractionDigits: fracctiondigits
			});
		}
	} else {
		return "";
	}
}