

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
var phpData;// PHP����μ����ǡ���
var settingData;
var lc_data;
window.onerror = false;
//---------------------------------------------------
// ���̽������
//---------------------------------------------------
function lcInit( json_obj )
{
	phpData = JSON.parse(json_obj);
	//LC�������
	$("#masking_loader").css("display","block");
	$.ajax({
		url:'../lcModel/lcedit_ajax.php',
		type:'POST',
		data:{
			'method': 'getLcEdit',
			'pono': phpData.pono,
			'poreviseno': phpData.poreviseno,
			'polineno': phpData.polineno,
			'sessionid': phpData["session_id"]
		}
	})
	.done(function(data) {
		var data = JSON.parse(data);
		if(data != false){
			//LC����
			lc_data = data.lc_data;
			//��ԥꥹ��
			var bank_list = data.bank_list;
			for(var i = 0; i < bank_list.length; i++){
				$option = $('<option>')
					.val(bank_list[i]["bankcd"])
					.text(bank_list[i]["bankomitname"])
				$('#bankname').append($option);
			}
			//�����ϥꥹ��
			var portplace_list = data.portplace_list;
			for(var i = 0; i < portplace_list.length; i++){
				$option = $('<option>')
					.val(portplace_list[i]["portplace"])
					.text(portplace_list[i]["portplace"])
				$('#portplace').append($option);
			}

			//�������������ե������ȿ��
			$("#pono").val(lc_data.pono);
			$("#payfcd").val(lc_data.payfcd);
			$("#payfnameformal").val(lc_data.payfnameformal);
			$("#polineno").val(lc_data.polineno);
			$("#productcd").val(lc_data.productcd);
			$("#productrevisecd").val(lc_data.productrevisecd);
			$("#productname").val(lc_data.productname);
			$("#opendate").val(lc_data.opendate);
			$("#moneyprice").val(lc_data.moneyprice);
			//�ơ��֥�
			$('#portplace option').filter(function(index){
				return $(this).text() === lc_data.portplace;
			}).prop('selected', true);
			$('#bankname option').filter(function(index){
				return $(this).val() === lc_data.bankcd;
			}).prop('selected', true);

			$("#bankreqdate").val(convertDate(lc_data.bankreqdate));
			$("#lcno").val(lc_data.lcno);
			$("#lcamopen").val(convertDate(lc_data.lcamopen));
			$("#validmonth").val(convertDate(lc_data.validmonth));
			$("#bldetail1date").val(convertDate(lc_data.bldetail1date));
			$("#bldetail1money").val(lc_data.bldetail1money);
			$("#bldetail2date").val(convertDate(lc_data.bldetail2date));
			$("#bldetail2money").val(lc_data.bldetail2money);
			$("#bldetail3date").val(convertDate(lc_data.bldetail3date));
			$("#bldetail3money").val(lc_data.bldetail3money);

			//���������
			$("#masking_loader").css("display","none");
		} else {
			alert("L/C���󤬼����Ǥ��ޤ���Ǥ�����L/C������̤����ޤ���");
			returnBtn();
		}
	})
	.fail(function() {  
		alert("tsts");
		// Ajax�ꥯ�����Ȥ�����
	});
}

//---------------------------------------------------
// ���ܥ������
//---------------------------------------------------
function returnBtn()
{
	location.href='/lc/info/index.php?strSessionID='+phpData["session_id"]+'&reSearchFlg=true';
}
																			
//---------------------------------------------------
// �����ܥ������
//---------------------------------------------------
function updateBtn()
{
	try {
		//���ϥ����å�
		//�����ץ�ǯ����ξ��
		if($("#opendate").val() == "" ){
			alert("ȯ�Է�����Ϥ����Ǥ���");
			throw new Error();
		}
		//�����ץ�ǯ��η��������շ�����yyyy/mm�ˤˤʤäƤʤ� �����ͽ�̷�⤷�Ƥ���YYYYMM���������Ȼפ���
		if(!$("#opendate").val().match(/^\d{4}\d{1,2}$/)){
			alert("ȯ�Է�η������ǧ���Ƥ���������");
			throw new Error();
		}

		//�������η��������շ�����yyyy/mm/dd�ˤˤʤäƤʤ�
		if(!$("#bankreqdate").val().match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)){
			alert("�������η������ǧ���Ƥ���������");
			$("#bankreqdate").focus();
			throw new Error();
		}

		//ȯ�����η��������շ�����yyyy/mm/dd�ˤˤʤäƤʤ�
		if(!$("#lcamopen").val().match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)){
			alert("ȯ�����η������ǧ���Ƥ���������");
			$("#lcamopen").focus();
			throw new Error();
		}
		
		//ͭ�����η��������շ�����yyyy/mm/dd�ˤˤʤäƤʤ�
		if(!$("#validmonth").val().match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)){
			alert("ͭ�����η������ǧ���Ƥ���������");
			$("#validmonth").focus();
			throw new Error();
		}

		//ȯ���������ǤϤʤ��ơ�ȯ������ǯ < �������դ�ǯ�ξ��
		var lcamopen_d = new Date($("#lcamopen").val());
		var now = new Date();
		if(lcamopen_d < now){
			alert("ȯ�����ϸ���������̤������ꤷ�Ƥ���������");
			$("#lcamopen").focus();
			throw new Error();
		}

		//ͭ��ǯ����ǤϤʤ��ơ�ͭ��ǯ���ǯ < �������դ�ǯ�ξ��
		var validmonth_d = new Date($("#validmonth").val());
		var now = new Date();
		if(validmonth_d < now){
			alert("ͭ�����ϸ���������̤������ꤷ�Ƥ���������");
			$("#validmonth").focus();
			throw new Error();
		}

		//��������ȯ������ͭ�����Τ����줬���ǤϤʤ��ơ�ȯ�Զ�Ԥ����ξ��
		if(($("#opendate").val() != "" || $("#validmonth").val() != "" || $("#lcamopen").val() != "") &&
			$("#bankname").val() == ""
		){
			alert("ȯ�Զ�Ԥ����򤷤Ƥ���������");
			$("#bankname").focus();
			throw new Error();
		}

		//ȯ���������ǤϤʤ������İ����������ξ��
		if($("#lcamopen").val() != "" && $("#bankreqdate").val() == ""){
			alert("�����������Ϥ��Ƥ���������");
			$("#bankreqdate").focus();
			throw new Error();
		}

		//ͭ������ȯ���������ǤϤʤ��ơ�ȯ���� > ͭ�����ξ��
		if($("#validmonth").val() == "" && $("#lcamopen").val() == ""){
			var validmonth_d = new Date($("#validmonth").val());
			var lcamopen_d = new Date($("#lcamopen").val());
			if(validmonth_d < lcamopen_d){
				alert("ͭ������ȯ�������̤������ꤷ�Ƥ���������");
				$("#validmonth_d").focus();
			}
		}
		// ���֤����ξ�硢
		if (lc_data.lcstate == 7) {
			alert("�����ɤ������ޤ���");
		}
		//��������
		$("#masking_loader").css("display","block");
		$.ajax({    
			url:'../lcModel/lcedit_ajax.php',
			type:'POST',
			data:{
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
				'lcstate': lc_data.lcstate,
				'sessionid': phpData["session_id"]
			}
		})
		.done(function(data) {
			// Ajax�ꥯ�����Ȥ�����
			var data = JSON.parse(data);

			//��������ɽ��
			$("#masking_loader").css("display","none");
		})
		.fail(function() {  
			// Ajax�ꥯ�����Ȥ�����
		});
	} catch (e) {
		console.log(e);
	}

}

//---------------------------------------------------
// ����ܥ������
//---------------------------------------------------
function releaseBtn()
{
	if( res = confirm("������ꤷ�ޤ�����") )
	{
		//��������
		$("#masking_loader").css("display","block");
		$.ajax({
			url:'../lcModel/lcedit_ajax.php',
			type:'POST',
			data:{
				'method': 'releaseLcEdit',
				'lc_data': lc_data,
				'sessionid': phpData["session_id"]
			}
		})
		.done(function(data) {
			// Ajax�ꥯ�����Ȥ�����
			var data = JSON.parse(data);

			//LC������̤�����
			returnBtn();

			//��������ɽ��
			$("#masking_loader").css("display","none");
		})
		.fail(function() {  
			// Ajax�ꥯ�����Ȥ�����
		});
	}
	
}
