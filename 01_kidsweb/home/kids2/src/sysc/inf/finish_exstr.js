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

		Column0.innerText = 'Title';
		Column1.innerText = 'Message';
	}

	// ���ܸ�
	else if ( lngSelfCode == 1 )
	{
		// ���ܥ���񤭽Ф�
		BackBt.innerHTML = BackBtJ1;

		FinishRegist.innerText = '��Ͽ��λ';

		Column0.innerText = '�����ȥ�';
		Column1.innerText = '��å�����';
	}

	return false;

}


//-->