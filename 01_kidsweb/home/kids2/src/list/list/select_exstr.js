<!--



//------------------------------------------------------------
// ���� : �إå������᡼�������
//------------------------------------------------------------
var headerAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="Ģɼ����">';
var headerAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="LIST SEARCH">';





//------------------------------------------------------------
// ���� : ���ܸ졦�Ѹ����شؿ�
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// �Ѹ�
	if ( lngSelfCode == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		SltList.innerText     = 'Select';
		ControlName.innerText = 'Control name';
		ListName.innerText    = 'List name';

	}

	// ���ܸ�
	else if ( lngSelfCode == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		SltList.innerText     = '����';
		ControlName.innerText = '����̾��';
		ListName.innerText    = 'Ģɼ̾��';

	}

	return false;

}


//-->