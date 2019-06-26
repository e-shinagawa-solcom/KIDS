

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
window.onerror = false;
//---------------------------------------------------
// ���̽������
//---------------------------------------------------
function lcInit( json_obj )
{
	phpData = JSON.parse(json_obj);
	if(phpData.lgoutymd == null && phpData.userAuth == 1){
		//ȿ�ǥܥ�����Ѳ�
		$("#reflectionBtn").prop("disabled",false);
	} else {
		//ȿ�ǥܥ�������Բ�
		$("#reflectionBtn").prop("disabled",true);
	}

	//���̾������
	getInfo();
}

//---------------------------------------------------
// ���̾������
//---------------------------------------------------
function getInfo()
{
	//LC�������
	$("#masking_loader").css("display","block");
	$.ajax({
		url:'../lcModel/lcset_ajax.php',
		type:'POST',
		data:{
			'method': 'getLcSetting',
			'sessionid': phpData["session_id"]
		}
	})
	.done(function(data) {
		// Ajax�ꥯ�����Ȥ�����
		var data = JSON.parse(data);
		settingData = data;//�ǡ����γ���

		//�����
		$("#baseOpenDateTxt").val(data.base_open_date);

		//������Ծ�������
		setBankInfo(data.bank_info);

		//���������������
		setPayfInfo(data.payf_info);

		//���������
		$("#masking_loader").css("display","none");
	})
	.fail(function() {  
		// Ajax�ꥯ�����Ȥ�����
	});
}

//---------------------------------------------------
//������Ծ�������
//---------------------------------------------------
function setBankInfo(bank_info){
	//��¸�ǡ����κ��
	$("#bank_body").empty();
	for(var i = 0; i < bank_info.length; i++){
		var data = bank_info[i];
	
		var invalidflag = "";
		if(data.invalidflag == "t"){
			invalidflag = "checked";
		}
		var html = "<tr id='bank_data_"+ data.bankcd +"'>" + 
			"<td><input type='text' class='form-control form-control-sm' name='bankcd' value='" + data.bankcd + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='bankomitname' value='" + data.bankomitname + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='bankformalname' value='" + data.bankformalname + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='bankdivrate' value='" + data.bankdivrate + "' ></td>" + 
			"<td><input type='checkbox' name='invalidflag' value='1' " + invalidflag + " style='width: 11px;margin-top: 10px;margin-left: 25px;'></td>" + 
			"</tr>";
		$("#bank_body").append(html);
	}
}

//---------------------------------------------------
//���������������
//---------------------------------------------------
function setPayfInfo(payf_info){
	//��¸�ǡ����κ��
	$("#payf_body").empty();
	for(var i = 0; i < payf_info.length; i++){
		var data = payf_info[i];
	
		var invalidflag = "";
		if(data.invalidflag == "t"){
			invalidflag = "checked";
		}
		var html = "<tr id='payf_data_"+ data.payfcd +"'>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfcd' value='" + data.payfcd + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfomitname' value='" + data.payfomitname + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfformalname' value='" + data.payfformalname + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfsendname' value='" + data.payfsendname + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfsendfax' value='" + data.payfsendfax + "' ></td>" + 
			"<td><input type='checkbox' name='invalidflag' value='1' " + invalidflag + "  style='width: 11px;margin-top: 10px;margin-left: 35px;'></td>" + 
			"</tr>";
		$("#payf_body").append(html);
	}
}

//---------------------------------------------------
//�ɲåܥ��󥤥٥��
//---------------------------------------------------
function addPayfInfo(){
	try {
		//���ϥ����å�
		var payfcd = $("#payfcdTxt").val();
		exp = "^[0-9]{4}$";
		if(payfcd == "") {
			alert("��ʧ�襳���ɤ����Ϥ��Ƥ���������");
			throw new Error();
		}
		if(!payfcd.match(exp)) {
			alert("��ʧ�襳���ɤ�4���Ⱦ�ѿ��������Ϥ��Ƥ���������");
			throw new Error();
		}
		for(var i = 0; i < settingData.payf_info.length; i++){
			if(settingData.payf_info[i].payfcd == payfcd){
				alert("���Ϥ��줿��ʧ�襳���ɤϤ��Ǥ���Ͽ����Ƥ��ޤ���");
				throw new Error();
			}
		}

		//�����Ԥ��ɲ�
		var html = "<tr id='payf_data_"+ payfcd +"'>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfcd' value='" + payfcd + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfomitname' value='' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfformalname' value='' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfsendname' value='' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfsendfax' value='' ></td>" + 
			"<td><input type='checkbox' name='invalidflag' value='1'></td>" + 
			"</tr>";
		$("#payf_body").append(html);

		//�ޥ����ǡ������ɲ�
		var new_data = {
			payfcd: payfcd,
			payfformalname: "",
			payfno: "",
			payfomitname: "",
			payfsendfax: "",
			payfsendname: "",
			invalidflag: ""
		};
		settingData.payf_info.push(new_data);

		//���ֲ��ޤǥ�������
		$('#payf_body').animate({scrollTop: $('#payf_body')[0].scrollHeight}, 'fast');

		//�ɲä����Ԥλ�ʧ�����ά̾�˥ե�������
		$("#payf_body #payf_data_"+ payfcd + " input[name='payfomitname']").focus();

		//��ʧ�襳����������򥯥ꥢ
		$("#payfcdTxt").val("");
	 
	} catch (e) {
		//��ʧ�襳���ɤ˥ե�������
		$("#payfcdTxt").focus();
	}
}

//---------------------------------------------------
//����ܥ��󥤥٥��
//---------------------------------------------------
function delPayfInfo(){
	try {
		//���ϥ����å�
		var payfcd = $("#payfcdTxt").val();
		exp = "^[0-9]{4}$";
		if(payfcd == "") {
			alert("��ʧ�襳���ɤ����Ϥ��Ƥ���������");
			throw new Error();
		}
		if(!payfcd.match(exp)) {
			alert("��ʧ�襳���ɤ�4���Ⱦ�ѿ��������Ϥ��Ƥ���������");
			throw new Error();
		}
		var hit = false;
		for(var i = 0; i < settingData.payf_info.length; i++){
			if(settingData.payf_info[i].payfcd == payfcd){
				hit = true;

				//�ޥ����ǡ����κ���ե饰��Ω�Ƥ�
				settingData.payf_info[i].del_flg = true;

				//�ơ��֥뤫��ǡ�������payf_data_1234
				$("#payf_data_"+ payfcd).remove();
			}
		}

		if(!hit){
			alert("�оݤλ�ʧ�褬����ޤ���");
			throw new Error();
		}
	 
	} catch (e) {
	}
}

//---------------------------------------------------
//���ɤ߹���
//---------------------------------------------------
function reloadInfo(){
	if( res = confirm("���ɹ��򤷤ޤ�����") )
	{
		getInfo();
	}
}

//---------------------------------------------------
//���ɤ߹���
//---------------------------------------------------
function reloadInfo(){
	if( res = confirm("���ɹ��򤷤ޤ�����") )
	{
		getInfo();
	}
}

//---------------------------------------------------
//ȿ�ǽ���
//---------------------------------------------------
function reflection(){
	try {
		if( res = confirm("�ǡ����򹹿����ޤ�����") )
		{
			//�����ǡ���
			var sendData = {};
			//ȿ�ǥե饰����
			var bankInfoChk = $("#bankInfoChk").prop('checked');
			var payfInfoChk = $("#payfInfoChk").prop('checked');
			var baseOpenDateChk = $("#baseOpenDateChk").prop('checked');
			sendData["bankInfoChk"] = bankInfoChk;
			sendData["payfInfoChk"] = payfInfoChk;
			sendData["baseOpenDateChk"] = baseOpenDateChk;

			if(!bankInfoChk && !payfInfoChk && !baseOpenDateChk){
				alert("ȿ�Ǥ����оݤ�����å����Ƥ���������");
				throw new Error();
			}

			//���ϥ����å�
			//�����
			if(baseOpenDateChk){
				//�������ξ��
				var val = $("#baseOpenDateTxt").val();
				if(val == ""){
					alert("����������Ϥ���Ƥ��ޤ���");
					$("#baseOpenDateTxt").focus();
					throw new Error();
				} else {
					//����30��Ķ�������
					if(val > 30){
						alert("�������30���ʲ������Ϥ��Ƥ���������");
						$("#baseOpenDateTxt").focus();
						throw new Error();
					}
				}
				sendData["baseOpenDate"] = val;
			}
			//��Ծ���
			if(bankInfoChk){
				//���ϥǡ����μ���
				var row_dom = $("#bank_body").find("tr");
				var maxbankdivrate = 0;

				for(var i=0; i < settingData.bank_info.length;i++){console.log("test22");
					var bankcd = settingData.bank_info[i].bankcd;
					//�Ԥ�DOM����
					var row = $("#bank_data_"+ bankcd);
					var bankcd = row.find("input[name='bankcd']").val();
					var bankomitname = row.find("input[name='bankomitname']").val();
					var bankformalname = row.find("input[name='bankformalname']").val();
					var bankdivrate = parseFloat(row.find("input[name='bankdivrate']").val());
					if(bankdivrate == NaN)bankdivrate = "";
					var invalidflag = row.find("input[name='invalidflag']").prop('checked');
					
					//���ϥ����å�
					//̵���ե饰��false�ξ�硢���ĳ俶Ψ��0�ξ��
					if(invalidflag == false && bankdivrate == 0){
						alert("ͭ���ʶ�Ծ���γ俶Ψ��0�ˤ��뤳�ȤϽ���ޤ���");
						row.find("input[name='bankdivrate']").focus();
						throw new Error();
					}
					//��ԥ�����,���̾��ά̾��,���̾����̾��,�俶Ψ�Τ����줬���ξ��
					if(bankcd === "" || bankomitname === "" || bankformalname === "" || bankdivrate === ""){
						alert("��Ծ����̤���Ϥι��ܤ�¸�ߤ��ޤ���");
						row.find("input[name='bankcd']").focus();
						throw new Error();
					}
					//����åɤη����3��Ķ�����ä���� ToDo �󶡤��줿DB�ˤ�4��Υǡ��������ä���3�路���������ʤ��Τ���
					
					//�ޥ����ǡ������ͤ򹹿�
					settingData.bank_info[i].bankcd = bankcd;
					settingData.bank_info[i].bankomitname = bankomitname;
					settingData.bank_info[i].bankformalname = bankformalname;
					settingData.bank_info[i].bankdivrate = bankdivrate;
					settingData.bank_info[i].invalidflag = invalidflag;

					maxbankdivrate += bankdivrate;
				}
				//����åɤ˳俶Ψ�ι�פϣ��ǤϤʤ����
				if(maxbankdivrate > 1){
					alert("�俶Ψ�ι�פ�1��Ķ���Ƥ��ޤ���");
					throw new Error();
				}
				
				//�����ǡ����Ȥ��ƥ��å�
				sendData["bank_info"] = settingData.bank_info;
			}

			//�����������
			if(payfInfoChk){
				//���ϥǡ����μ���
				var row_dom = $("#payf_body").find("tr");
				var maxbankdivrate = 0;

				for(var i=0; i < settingData.payf_info.length;i++){
					//����ե饰��Ω�äƤ��ʤ���������ͤ�ޥ����ǡ�����ȿ��
					if(settingData.payf_info[i].del_flg != true){
						var payfcd = settingData.payf_info[i].payfcd;
						//�Ԥ�DOM����
						var row = $("#payf_data_"+ payfcd);
						var payfcd = row.find("input[name='payfcd']").val();
						var payfomitname = row.find("input[name='payfomitname']").val();
						var payfformalname = row.find("input[name='payfformalname']").val();
						var payfsendname = row.find("input[name='payfsendname']").val();
						var payfsendfax = row.find("input[name='payfsendfax']").val();
						var invalidflag = row.find("input[name='invalidflag']").prop('checked');
						
						//���ϥ����å�
						//��ʧ�襳����,��ʧ���ά̾��,��ʧ������̾�ΤΤ����줬���ξ��
						if(payfcd === "" || payfomitname === "" || payfformalname === ""){
							alert("����������̤���Ϥι��ܤ�¸�ߤ��ޤ���");
							row.find("input[name='payfcd']").focus();
							throw new Error();
						}
						
						//�ޥ����ǡ������ͤ򹹿�
						settingData.payf_info[i].payfcd = payfcd;
						settingData.payf_info[i].payfomitname = payfomitname;
						settingData.payf_info[i].payfformalname = payfformalname;
						settingData.payf_info[i].payfsendname = payfsendname;
						settingData.payf_info[i].payfsendfax = payfsendfax;
						settingData.payf_info[i].invalidflag = invalidflag;
					}
				}
				
				//�����ǡ����Ȥ��ƥ��å�
				sendData["payf_info"] = settingData.payf_info;
			}

			//ȿ�ǥǡ�������
			$("#masking_loader").css("display","block");
			$.ajax({
				url:'../lcModel/lcset_ajax.php',
				type:'POST',
				data:{
					'method': 'updateLcSetting',
					'phpData': phpData,
					'send_data': sendData,
					'sessionid': phpData["session_id"]
				}
			})
			.done(function(data) {
				alert(data);
				// Ajax�ꥯ�����Ȥ�����
				var data = JSON.parse(data);
			
				alert("�������꤬��λ���ޤ�����");
			})
			.fail(function() {  
				// Ajax�ꥯ�����Ȥ�����
			});
			
			//���������
			$("#masking_loader").css("display","none");
		}
	} catch (e) {
		console.log(e);
	}
}