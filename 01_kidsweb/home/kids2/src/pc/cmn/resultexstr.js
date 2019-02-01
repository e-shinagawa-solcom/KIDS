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

	// 日本語切替
	else if( g_lngCode == 1 )
	{

		// 処理用テーブル書き出し
		fncProcessingOutputModule( strMode , 1 );


		CreationDate.innerText		=	'登録日';
		StockAppDate.innerText		=	'計上日';
		PStockCode.innerText		=	'仕入NO.';
		POrderCode.innerText		=	'発注NO.';
		PSlipCode.innerText			=	'納品書ＮＯ.';
		InputUser.innerText			=	'入力者';
		Customer.innerText			=	'仕入先';
		InChargeGroup.innerText		=	'部門';
		InChargeUser.innerText		=	'担当者';
		DeliveryPlace.innerText		=	'納品場所';
		MonetaryUnit.innerText		=	'通貨';
		MonetaryRate.innerText		=	'レートタイプ';
		ConversionRate.innerText	=	'換算レート';
		StockStatus.innerText		=	'状態';
		PayCondition.innerText		=	'支払条件';
		ExpirationDate.innerText	=	'製品到着日';
		Remark.innerText			=	'備考';
		TotalPrice.innerText		=	'合計金額';


	}

	return false;
}


//-->