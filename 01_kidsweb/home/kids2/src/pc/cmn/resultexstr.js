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
		StockAppDate.innerText		=	'Order date';
		PStockCode.innerText		=	'P control No.';
		POrderCode.innerText		=	'P order No.';
		PSlipCode.innerText			=	'Shipping No.';
		InputUser.innerText			=	'Input person';
		Customer.innerText			=	'Supplier';
		InChargeGroup.innerText		=	'Dept';
		InChargeUser.innerText		=	'In charge name';
		DeliveryPlace.innerText		=	'Location';
		MonetaryUnit.innerText		=	'Currency';
		MonetaryRate.innerText		=	'Rate type';
		ConversionRate.innerText	=	'Rate';
		StockStatus.innerText		=	'Status';
		PayCondition.innerText		=	'Pay condition';
		ExpirationDate.innerText	=	'Arrival date';
		Remark.innerText			=	'Remark';
		TotalPrice.innerText		=	'Total';


	}

	// ���ܸ�����
	else if( g_lngCode == 1 )
	{

		// �����ѥơ��֥�񤭽Ф�
		fncProcessingOutputModule( strMode , 1 );


		CreationDate.innerText		=	'��Ͽ��';
		StockAppDate.innerText		=	'�׾���';
		PStockCode.innerText		=	'����NO.';
		POrderCode.innerText		=	'ȯ��NO.';
		PSlipCode.innerText			=	'Ǽ�ʽ�Σ�.';
		InputUser.innerText			=	'���ϼ�';
		Customer.innerText			=	'������';
		InChargeGroup.innerText		=	'����';
		InChargeUser.innerText		=	'ô����';
		DeliveryPlace.innerText		=	'Ǽ�ʾ��';
		MonetaryUnit.innerText		=	'�̲�';
		MonetaryRate.innerText		=	'�졼�ȥ�����';
		ConversionRate.innerText	=	'�����졼��';
		StockStatus.innerText		=	'����';
		PayCondition.innerText		=	'��ʧ���';
		ExpirationDate.innerText	=	'����������';
		Remark.innerText			=	'����';
		TotalPrice.innerText		=	'��׶��';


	}

	return false;
}


//-->