<!--


//------------------------------------------------------------
// ���� : ���ܸ졦�Ѹ����شؿ�
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// �Ѹ�
	if ( lngSelfCode == 0 )
	{
		// ���ܥ���񤭽Ф�
		BackBt.innerHTML = BackBtE1;

		FinishRegist.innerText = 'REGISTRAITION COMPLETED';

		Column0.innerText = 'Admin email address';
	}

	// ���ܸ�
	else if ( lngSelfCode == 1 )
	{
		// ���ܥ���񤭽Ф�
		BackBt.innerHTML = BackBtJ1;

		FinishRegist.innerText = '��Ͽ��λ';

		Column0.innerText = '�����ԥ᡼�륢�ɥ쥹';
	}

	return false;

}


//-->