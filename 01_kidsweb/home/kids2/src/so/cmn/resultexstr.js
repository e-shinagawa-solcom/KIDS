<!--


// 日本語英語切替
function fncChgEtoJ( strMode )
{

	// 英語切替
	if( g_lngCode == 0 )
	{

		// 処理用テーブル書き出し
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

	// 日本語切替
	else if( g_lngCode == 1 )
	{

		// 処理用テーブル書き出し
		fncProcessingOutputModule( strMode , 1 );


		CreationDate.innerText		=	'登録日';
		ReceiveAppDate.innerText	=	'計上日';
		PReceiveCode.innerText		=	'受注NO.';
		InputUser.innerText			=	'入力者';
		Customer.innerText			=	'顧客';
		InChargeGroup.innerText		=	'部門';
		InChargeUser.innerText		=	'担当者';
		MonetaryUnit.innerText		=	'通貨';
		MonetaryRate.innerText		=	'レートタイプ';
		ConversionRate.innerText	=	'換算レート';
		ReceiveStatus.innerText		=	'状態';
		Remark.innerText			=	'備考';
		TotalPrice.innerText		=	'合計金額';


	}

	return false;
}


//-->