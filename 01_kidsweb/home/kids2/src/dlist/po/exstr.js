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

		ExStrDL01.innerText = '����';
		ExStrDL02.innerText = '��������';
		ExStrDL03.innerText = '��������';
		ExStrDL04.innerText = 'ñ��';
		ExStrDL05.innerText = 'ñ��';
		ExStrDL06.innerText = '����';
		ExStrDL07.innerText = '��ȴ���';
		ExStrDL08.innerText = 'Ǽ��';
		ExStrDL09.innerText = '����';

		if( typeof( ExStrDL10 ) != 'undefined' )
		{
			ExStrDL10.innerText = '�о�';
		}

		if( typeof( ExStrDL11 ) != 'undefined' )
		{
			ExStrDL11.innerText = '������';
		}
	}

	return false;

}


//-->