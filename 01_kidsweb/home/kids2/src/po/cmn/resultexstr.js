<!--


// �ץ�ӥ塼�ܥ����Ф�
function fncPreview( strURL )
{
	if( strURL != '' )
	{
		var previews = '<a href="#"><img onclick="fncListOutput(\'' +  strURL + '\');return false;" onmouseover="fncGrayPreviewButton( \'on\' , this );" onmouseout="fncGrayPreviewButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/cmn/querybt/graypreview_off_bt.gif" width="72" height="20" border="0" alt="PREVIEW"></a>';

		PreviewBt.innerHTML = previews;

	}
	else
	{
		PreviewBt.style.visibility = 'hidden';
	}

	return false;
}



// ���ܸ�Ѹ�����
function fncChgEtoJ( strMode )
{
	
	
	// �Ѹ�����
	if( g_lngCode == 0 )
	{

		// �����ѥơ��֥�񤭽Ф�
		fncProcessingOutputModule( strMode , 0 );

		if( typeof(WFMessage) != 'undefined' )
		{
			WFMessage.innerText = 'Message';
		}

		CreationDate.innerText		=	'Creation date';
		OrderAppDate.innerText		=	'Order date';
		POrderCode.innerText		=	'P order No.';
		InputUser.innerText			=	'Input person';
		Customer.innerText			=	'Supplier';
		InChargeGroup.innerText		=	'Dept';
		InChargeUser.innerText		=	'In charge name';
		DeliveryPlace.innerText		=	'Location';
		MonetaryUnit.innerText		=	'Currency';
		MonetaryRate.innerText		=	'Rate type';
		ConversionRate.innerText	=	'Rate';
		OrderStatus.innerText		=	'Status';
		PayCondition.innerText		=	'Pay condition';
		ExpirationDate.innerText	=	'PO limit date';
		Remark.innerText			=	'Remark';
		TotalPrice.innerText		=	'Total';


	}

	// ���ܸ�����
	else if( g_lngCode == 1 )
	{

		// �����ѥơ��֥�񤭽Ф�
		fncProcessingOutputModule( strMode , 1 );

		if( typeof(WFMessage) != 'undefined' )
		{
			WFMessage.innerText = '��å�����';
		}

		CreationDate.innerText		=	'��Ͽ��';
		OrderAppDate.innerText		=	'�׾���';
		POrderCode.innerText		=	'ȯ��NO.';
		InputUser.innerText			=	'���ϼ�';
		Customer.innerText			=	'������';
		InChargeGroup.innerText		=	'����';
		InChargeUser.innerText		=	'ô����';
		DeliveryPlace.innerText		=	'Ǽ�ʾ��';
		MonetaryUnit.innerText		=	'�̲�';
		MonetaryRate.innerText		=	'�졼�ȥ�����';
		ConversionRate.innerText	=	'�����졼��';
		OrderStatus.innerText		=	'����';
		PayCondition.innerText		=	'��ʧ���';
		ExpirationDate.innerText	=	'ȯ��ͭ��������';
		Remark.innerText			=	'����';
		TotalPrice.innerText		=	'��׶��';


	}

	return false;
}




function fncSetWFMessage( obj , objHdn )
{
	objHdn.value = obj.value;

	// �ǥХå�
	// alert( objHdn.value );
}



//2007.08.10 matsuki update start
function fncPayConditionConfirm( strPayMode , lngPayConditionCode , lngPayConditionCodeCrt )
{
	var strMessage;
	var strMessageArray_J = new Array(
									"���ꤵ��Ƥ����ʧ����ȯ�����Ƥ���侩������ʧ���Ȱ��פ��Ƥ��ޤ���\n�侩������ʧ�������ѹ����ޤ�����",
									"ʣ��¸�ߤ������٤��Ф��ơ���ʧ���ϰ������ꤵ��ޤ���\n��ʧ�����ǧ���Ʋ�������",
									"ʣ��¸�ߤ������٤��Ф��ơ���ʧ���ϰ������ꤵ��ޤ���\n�侩������ʧ�������ѹ����ޤ�����",
									"��ʧ�����ǧ���Ʋ�������");
	
	var strMessageArray_E = new Array(
									"'Pay Condition' you selected doesn't correspond with that recommend.\nChange 'Pay Condition' to recommend?",
									"There are several dital datas you have registed. On the other hand 'Pay Condition' is setted to those datas at one time.\nConfirm 'Pay Condition'.",
									"There are several dital datas you have registed. On the other hand 'Pay Condition' is setted to those datas at one time.\nChange 'Pay Condition' to recommend?",
									"Confirm 'Pay Condition'.");
	
	var lngPayMode = eval(strPayMode);
	window.lngPayConditionCode.selectedIndex = eval(lngPayConditionCode);//�桼���������ꤷ���ͤ򥻥å�
	//objHdn.value = obj.value;

	// �Ѹ�����
	if( g_lngCode == 0 ){
		strConfirm = strMessageArray_E[lngPayMode];
		strAlert = strMessageArray_E[3];
		
		
		
	}
	else {
		strConfirm = strMessageArray_J[lngPayMode];
		strAlert = strMessageArray_J[3];
	}
	
	if( lngPayMode != 1 ){//1�ξ��Τ�confirm�ǤϤʤ�alert�ˤ���
		
		result = confirm(strConfirm);
		if (result == true ){
			window.lngPayConditionCode.selectedIndex = eval(lngPayConditionCodeCrt);//�������ͤ�select
			window.form1.lngPayConditionCode.value = lngPayConditionCodeCrt;
			//���������ͤˤ��������ͤ򥻥å�
		}
		
		else{
		 alert(strAlert);
		 //window.frmPayConditionCode.lngPayConditionCode.disabled = true;
		 window.strRecommendPayCondition.innerHTML = '<marquee behavior="scroll" scrolldelay="150">�侩������ʧ��' + window.lngPayConditionCode.options[lngPayConditionCodeCrt].text + '</marquee>';
		}
	}
	
	else{
		alert(strConfirm);
	}

	return false;
}


function fncPayConditionFrmChanged(){
	//���򤵤줿�ͤ������ե�����ˤ�ȿ��
	window.form1.lngPayConditionCode.value = window.lngPayConditionCode.selectedIndex;
}


//2007.08.10 matsuki update end
//-->