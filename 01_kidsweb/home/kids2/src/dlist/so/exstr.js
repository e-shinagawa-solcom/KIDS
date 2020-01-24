<!--


function ChgEtoJ( dlCount )
{

	if ( dlCount == 0 )
	{
		/* DETAIL LIST E */

		ExStrDL02.innerText = 'Products';
		ExStrDL01.innerText = 'Goods set code';
		ExStrDL03.innerText = 'Price';
		ExStrDL04.innerText = 'Unit';
		ExStrDL05.innerText = 'Quantity';
		ExStrDL06.innerText = 'Amt Bfr tax';
		ExStrDL07.innerText = 'Delivery date';
		ExStrDL08.innerText = 'Remark';

		if( typeof( ExStrDL09 ) != 'undefined' )
		{
			ExStrDL09.innerText = 'Target';
		}

		if( typeof( ExStrDL10 ) != 'undefined' )
		{
			ExStrDL10.innerText = 'Org Quantity';
		}
	}
	else if ( dlCount == 1 )
	{
		/* DETAIL LIST J */

		ExStrDL01.innerText = '選択';
		ExStrDL02.innerText = 'No';
		ExStrDL03.innerText = '顧客受注番号';
		ExStrDL04.innerText = '受注番号';
		ExStrDL05.innerText = '顧客品番';
		ExStrDL06.innerText = '製品コード';
		ExStrDL07.innerText = '製品名';
		ExStrDL08.innerText = '製品名（英語）';
		ExStrDL09.innerText = '営業部署';
		ExStrDL10.innerText = '売上区分';
		ExStrDL11.innerText = '納期';
		ExStrDL12.innerText = '単価';
		ExStrDL13.innerText = '単位';
		ExStrDL14.innerText = '数量';
		ExStrDL15.innerText = '税抜金額';
		ExStrDL16.innerText = '計上単位';
		// if( typeof( ExStrDL09 ) != 'undefined' )
		// {
		// 	ExStrDL09.innerText = '対象';
		// }

		// if( typeof( ExStrDL10 ) != 'undefined' )
		// {
		// 	ExStrDL10.innerText = '元数量';
		// }
	}

	return false;

}


//-->