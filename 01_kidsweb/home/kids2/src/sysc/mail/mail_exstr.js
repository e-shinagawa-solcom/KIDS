<!--


//------------------------------------------------------------
// ���� : ���ܸ졦�Ѹ����شؿ�
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// �Ѹ�
	if ( lngSelfCode == 0 )
	{
		// ��Ͽ�ܥ���񤭽Ф�
		RegistBt.innerHTML = blownRegiBtE1;

		Column0.innerText = 'Admin email address';
	}

	// ���ܸ�
	else if ( lngSelfCode == 1 )
	{
		// ��Ͽ�ܥ���񤭽Ф�
		RegistBt.innerHTML = blownRegiBtJ1;

		Column0.innerText = '�����ԥ᡼�륢�ɥ쥹';
	}

	return false;

}


//-->