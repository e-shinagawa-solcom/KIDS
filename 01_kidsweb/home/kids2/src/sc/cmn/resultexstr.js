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
		SalesAppDate.innerText		=	'Receive date';
		PSalesCode.innerText		=	'P control No.';
		PReceiveCode.innerText		=	'Customer receive code';
		PSlipCode.innerText			=	'Shipping No.';
		InputUser.innerText			=	'Input person';
		Customer.innerText			=	'Supplier';

		if( typeof( InChargeGroup ) != 'undefined' )
		{
			InChargeGroup.innerText	=	'Dept';
			InChargeUser.innerText	=	'In charge name';
		}

		MonetaryUnit.innerText		=	'Currency';
		MonetaryRate.innerText		=	'Rate type';
		ConversionRate.innerText	=	'Rate';
		SalesStatus.innerText		=	'Status';
		Remark.innerText			=	'Remark';
		TotalPrice.innerText		=	'Total';


	}

	// ���ܸ�����
	else if( g_lngCode == 1 )
	{

		// �����ѥơ��֥�񤭽Ф�
		fncProcessingOutputModule( strMode , 1 );


		CreationDate.innerText		=	'��Ͽ��';
		SalesAppDate.innerText		=	'�׾���';
		PSalesCode.innerText		=	'���NO.';
		PReceiveCode.innerText		=	'�ܵҼ����ֹ�';
		PSlipCode.innerText			=	'Ǽ�ʽ�Σ�.';
		InputUser.innerText			=	'���ϼ�';
		Customer.innerText			=	'�����';

		if( typeof( InChargeGroup ) != 'undefined' )
		{
			InChargeGroup.innerText	=	'����';
			InChargeUser.innerText	=	'ô����';
		}

		MonetaryUnit.innerText		=	'�̲�';
		MonetaryRate.innerText		=	'�졼�ȥ�����';
		ConversionRate.innerText	=	'�����졼��';
		SalesStatus.innerText		=	'����';
		Remark.innerText			=	'����';
		TotalPrice.innerText		=	'��׶��';


	}

	return false;
}


//-->