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

		ControlTitle.innerText = 'LOG';
		Column0.innerText      = 'No';
		Column1.innerText      = 'Session ID';
		Column2.innerText      = 'User Code';
		Column3.innerText      = 'User ID';
		Column4.innerText      = 'Password';
		Column5.innerText      = 'Login Time';
		Column6.innerText      = 'Status';

	}

	// ���ܸ�
	else if ( lngSelfCode == 1 )
	{

		ControlTitle.innerText = '��';
		Column0.innerText      = 'No';
		Column1.innerText      = '���å����ɣ�';
		Column2.innerText      = '�桼����������';
		Column3.innerText      = '�桼�����ɣ�';
		Column4.innerText      = '�ѥ����';
		Column5.innerText      = '����������';
		Column6.innerText      = '����';

	}

	return false;

}


//-->