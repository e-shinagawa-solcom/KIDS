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

		ExStrDL02.innerText = '����';
		ExStrDL01.innerText = '����ʬ';
		ExStrDL03.innerText = 'ñ��';
		ExStrDL04.innerText = 'ñ��';
		ExStrDL05.innerText = '����';
		ExStrDL06.innerText = '��ȴ���';
		ExStrDL07.innerText = 'Ǽ����';
		ExStrDL08.innerText = '����';

		if( typeof( ExStrDL09 ) != 'undefined' )
		{
			ExStrDL09.innerText = '�о�';
		}

		if( typeof( ExStrDL10 ) != 'undefined' )
		{
			ExStrDL10.innerText = '������';
		}
	}

	return false;

}


//-->