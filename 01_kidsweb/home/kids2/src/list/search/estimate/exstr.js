<!--








//------------------------------------------------------------
// ���� : �إå������᡼�������
//------------------------------------------------------------
var poheaderAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="���Ѹ����񸡺�">';
var poheaderAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="ESTIMATE COST LIST SEARCH">';






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

		SegA15.innerHTML = 'Creation date';
		SegA01.innerText = 'Products code';
		SegA02.innerText = 'Products name(ja)';
		SegA18.innerText = 'Input person';
		SegA04.innerText = 'Dept';
		SegA05.innerText = 'In charge name';
		SegA03.innerText = 'Delivery date';

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

		SegA15.innerHTML = '��������';
		SegA01.innerText = '���ʥ�����';
		SegA02.innerText = '����̾��(���ܸ�)';
		SegA18.innerText = '���ϼ�';
		SegA04.innerText = '����';
		SegA05.innerText = 'ô����';
		SegA03.innerText = 'Ǽ��';


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