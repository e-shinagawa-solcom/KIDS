<!--


function fncChgEtoJ( lngCode )
{

	if ( lngCode == 0 )
	{

		// �����ѥơ��֥�񤭽Ф�
		fncProcessingOutputModule( 'use' , 0 );

		strComments.innerHTML = 'Selected data is used in the following item.';
		Column0.innerHTML     = 'Control name';
		Column1.innerHTML     = 'No.';

	}

	else if ( lngCode == 1 )
	{

		// �����ѥơ��֥�񤭽Ф�
		fncProcessingOutputModule( 'use' , 1 );

		strComments.innerHTML = '���򤵤줿�ǡ����ϲ����ι��ܤˤƻ��Ѥ���Ƥ��ޤ���';
		Column0.innerHTML     = '����̾��';
		Column1.innerHTML     = '�Σ�.';

	}

	return false;

}


//-->