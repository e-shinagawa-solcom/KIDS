<!--



//------------------------------------------------------------
// ���� : �إå������᡼�������
//------------------------------------------------------------
var headerAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="�ǡ�������">';
var headerAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="DATA FILE SEARCH">';





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
		ListName.innerText    = 'Data file name';

	}

	// ���ܸ�
	else if ( lngSelfCode == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		SltList.innerText     = '����';
		ControlName.innerText = '����̾��';
		ListName.innerText    = '�ǡ���̾��';

	}

	return false;

}


//-->