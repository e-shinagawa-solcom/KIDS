<!--


//------------------------------------------------------------
// ���� : ���ܸ졦�Ѹ����شؿ�
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// ���ܥ���񤭽Ф�
	BackBt.innerHTML = backsmallBt;

	// �Ѹ�
	if ( lngSelfCode == 0 )
	{

		ControlTitle.innerText = 'DETAIL';
		Column0.innerText      = 'Title';
		Column1.innerText      = 'Message';
		Column2.innerText      = 'Registration date';

	}

	// ���ܸ�
	else if ( lngSelfCode == 1 )
	{

		ControlTitle.innerText = '�ܺ�';
		Column0.innerText      = '�����ȥ�';
		Column1.innerText      = '��å�����';
		Column2.innerText      = '��Ͽ����';

	}

	return false;

}


//-->