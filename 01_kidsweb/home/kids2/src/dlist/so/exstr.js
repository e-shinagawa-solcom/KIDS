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

		ExStrDL01.innerText = '����';
		ExStrDL02.innerText = 'No';
		ExStrDL03.innerText = '�ܵҼ����ֹ�';
		ExStrDL04.innerText = '�����ֹ�';
		ExStrDL05.innerText = '�ܵ�����';
		ExStrDL06.innerText = '���ʥ�����';
		ExStrDL07.innerText = '����̾';
		ExStrDL08.innerText = '����̾�ʱѸ��';
		ExStrDL09.innerText = '�Ķ�����';
		ExStrDL10.innerText = '����ʬ';
		ExStrDL11.innerText = 'Ǽ��';
		ExStrDL12.innerText = 'ñ��';
		ExStrDL13.innerText = 'ñ��';
		ExStrDL14.innerText = '����';
		ExStrDL15.innerText = '��ȴ���';
		ExStrDL16.innerText = '�׾�ñ��';
		// if( typeof( ExStrDL09 ) != 'undefined' )
		// {
		// 	ExStrDL09.innerText = '�о�';
		// }

		// if( typeof( ExStrDL10 ) != 'undefined' )
		// {
		// 	ExStrDL10.innerText = '������';
		// }
	}

	return false;

}


//-->