<!--


//------------------------------------------------------------
// ���� : ���ܸ졦�Ѹ����شؿ�
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// ���ܥ���񤭽Ф�
	LogBt.innerHTML = logBt;

	// �Ѹ�
	if ( lngSelfCode == 0 )
	{
		// ��Ͽ�ܥ���񤭽Ф�
		RegistBt.innerHTML = blownRegiBtE1;

		Column0.innerText = 'Title';
		Column1.innerText = 'Message';
	}

	// ���ܸ�
	else if ( lngSelfCode == 1 )
	{
		// ��Ͽ�ܥ���񤭽Ф�
		RegistBt.innerHTML = blownRegiBtJ1;

		Column0.innerText = '�����ȥ�';
		Column1.innerText = '��å�����';
	}

	return false;

}


//-->