<!--


//------------------------------------------------------------
// ���� : ���ܸ졦�Ѹ����شؿ�
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// ���ܥ���񤭽Ф�
	BackBt.innerHTML = backsmallBt;

	// PREV & NEXT�ܥ���񤭽Ф�
	NextBt.innerHTML = blownnextBt;
	PrevBt.innerHTML = blownprevBt;

	// �Ѹ�
	if ( lngSelfCode == 0 )
	{

		ControlTitle.innerText = 'LOG';
		Column0.innerText      = 'No';
		Column1.innerText      = 'Title';
		Column2.innerText      = 'Registration date';
		Column3.innerText      = 'Detail';

	}

	// ���ܸ�
	else if ( lngSelfCode == 1 )
	{

		ControlTitle.innerText = '��';
		Column0.innerText      = 'No';
		Column1.innerText      = '�����ȥ�';
		Column2.innerText      = '��Ͽ����';
		Column3.innerText      = '�ܺ�';

	}

	return false;

}


//-->