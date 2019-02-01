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

		ExStrDL02.innerText = '製品';
		ExStrDL01.innerText = '売上区分';
		ExStrDL03.innerText = '単価';
		ExStrDL04.innerText = '単位';
		ExStrDL05.innerText = '数量';
		ExStrDL06.innerText = '税抜金額';
		ExStrDL07.innerText = '納品日';
		ExStrDL08.innerText = '備考';

		if( typeof( ExStrDL09 ) != 'undefined' )
		{
			ExStrDL09.innerText = '対象';
		}

		if( typeof( ExStrDL10 ) != 'undefined' )
		{
			ExStrDL10.innerText = '元数量';
		}
	}

	return false;

}


//-->