<!--


//------------------------------------------------------------
// ���� : ���ܸ졦�Ѹ����شؿ�
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// �Ѹ�
	if ( lngSelfCode == 0 )
	{
		// �ܥ���񤭽Ф�
		ProcessBt.innerHTML = blownProcessBtE1;
		RivivalBt.innerHTML = blownRivivalBtE1;

		Column0.innerText = 'Date';
		Column1.innerText = 'Target';

	}

	// ���ܸ�
	else if ( lngSelfCode == 1 )
	{
		// �ܥ���񤭽Ф�
		ProcessBt.innerHTML = blownProcessBtJ1;
		RivivalBt.innerHTML = blownRivivalBtJ1;

		Column0.innerText = '�׾���';
		Column1.innerText = '�о�';

	}

	return false;

}


//-->