<!--


function ChgEtoJ( dlCount )
{

	if ( dlCount == 0 )
	{
		/* DETAIL LIST E */

		ExStrDL01.innerText = 'Products';
		ExStrDL02.innerText = 'Goods set';
		ExStrDL03.innerText = 'Goods parts';
		ExStrDL04.innerText = 'Price';
		ExStrDL05.innerText = 'Unit';
		ExStrDL06.innerText = 'Quantity';
		ExStrDL07.innerText = 'Amt Bfr tax';
		ExStrDL08.innerText = 'Delivery date';
		ExStrDL09.innerText = 'Remark';

		if( typeof( ExStrDL10 ) != 'undefined' )
		{
			ExStrDL10.innerText = 'Target';
		}

		if( typeof( ExStrDL11 ) != 'undefined' )
		{
			ExStrDL11.innerText = 'Org Quantity';
		}
	}

	else if ( dlCount == 1 )
	{
		/* DETAIL LIST J */

		ExStrDL01.innerText = '製品';
		ExStrDL02.innerText = '仕入科目';
		ExStrDL03.innerText = '仕入部品';
		ExStrDL04.innerText = '単価';
		ExStrDL05.innerText = '単位';
		ExStrDL06.innerText = '数量';
		ExStrDL07.innerText = '税抜金額';
		ExStrDL08.innerText = '納期';
		ExStrDL09.innerText = '備考';

		if( typeof( ExStrDL10 ) != 'undefined' )
		{
			ExStrDL10.innerText = '対象';
		}

		if( typeof( ExStrDL11 ) != 'undefined' )
		{
			ExStrDL11.innerText = '元数量';
		}
	}

	return false;

}


//-->