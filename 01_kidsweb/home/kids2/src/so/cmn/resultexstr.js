<!--


// ���ܸ�Ѹ�����
function fncChgEtoJ( strMode )
{

	// �Ѹ�����
	if( g_lngCode == 0 )
	{

		// �����ѥơ��֥�񤭽Ф�
		fncProcessingOutputModule( strMode , 0 );


		CreationDate.innerText		=	'Creation date';
		ReceiveAppDate.innerText		=	'Receive date';
		PReceiveCode.innerText		=	'P order No.';
		InputUser.innerText			=	'Input person';
		Customer.innerText			=	'Vender';
		InChargeGroup.innerText		=	'Dept';
		InChargeUser.innerText		=	'In charge name';
		MonetaryUnit.innerText		=	'Currency';
		MonetaryRate.innerText		=	'Rate type';
		ConversionRate.innerText	=	'Rate';
		ReceiveStatus.innerText		=	'Status';
		Remark.innerText			=	'Remark';
		TotalPrice.innerText		=	'Total';


	}

	// ���ܸ�����
	else if( g_lngCode == 1 )
	{

		// �����ѥơ��֥�񤭽Ф�
		fncProcessingOutputModule( strMode , 1 );


		CreationDate.innerText		=	'��Ͽ��';
		ReceiveAppDate.innerText	=	'�׾���';
		PReceiveCode.innerText		=	'����NO.';
		InputUser.innerText			=	'���ϼ�';
		Customer.innerText			=	'�ܵ�';
		InChargeGroup.innerText		=	'����';
		InChargeUser.innerText		=	'ô����';
		MonetaryUnit.innerText		=	'�̲�';
		MonetaryRate.innerText		=	'�졼�ȥ�����';
		ConversionRate.innerText	=	'�����졼��';
		ReceiveStatus.innerText		=	'����';
		Remark.innerText			=	'����';
		TotalPrice.innerText		=	'��׶��';


	}

	return false;
}


//-->