<!--








//------------------------------------------------------------
// ���� : �إå������᡼�������
//------------------------------------------------------------
var poheaderAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="ȯ��񸡺�">';
var poheaderAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="PO SEARCH">';






//------------------------------------------------------------
// ���� : ���ܸ졦�Ѹ����شؿ�
//------------------------------------------------------------
function ChgEtoJ( lngCount )
{


	//------------------------------------------------------------
	// ���� : [����][���ꥢ]�ܥ����ɽ��
	//------------------------------------------------------------
	if( typeof(window.top.schSchButton) != 'undefined' )
	{
		window.top.schSchButton.style.visibility = 'visible';
	}

	if( typeof(window.top.schClrButton) != 'undefined' )
	{
		window.top.schClrButton.style.visibility = 'visible';
	}


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		window.top.SegAHeader.innerHTML = poheaderAE;

		SegA01.innerText='Date';
		SegA02.innerText='P order No.';
		SegA03.innerText='Vendor';
		SegA04.innerText='Dept';
		SegA05.innerText='In charge name';

		SegPCode.innerText='Products code';

		SegA15.innerText='Regist date';

		SegA18.innerText='Input person';

		//SegB01.innerText='Products code/name';

		if( typeof(InvalidSegs) != 'undefined' )
		{
			InvalidSegs.innerText='Invalid';
		}

		if( typeof(AdminSet) != 'undefined' )
		{
			AdminMode.innerText='Administrator mode';
		}


		ViewSearch1.innerHTML= vishImgE;



	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngCount == 1 )
	{

		window.top.SegAHeader.innerHTML = poheaderAJ;

		SegA01.innerText='�׾���';
		SegA02.innerText='ȯ��Σ�.';
		SegA03.innerText='������';
		SegA04.innerText='����';
		SegA05.innerText='ô����';

		SegPCode.innerText='���ʥ�����';

		SegA15.innerText='��Ͽ��';

		SegA18.innerText='���ϼ�';

		//SegB01.innerText='���ʥ����ɡ�̾��';

		if( typeof(InvalidSegs) != 'undefined' )
		{
			InvalidSegs.innerText='̵��';
		}

		if( typeof(AdminSet) != 'undefined' )
		{
			AdminMode.innerText='�����ԥ⡼��';
		}


		ViewSearch1.innerHTML= vishImgJ;


	}

	return false;

}


//-->