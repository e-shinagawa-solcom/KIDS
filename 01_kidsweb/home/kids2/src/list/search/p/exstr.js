<!--





//------------------------------------------------------------
// ���� : �إå������᡼�������
//------------------------------------------------------------
var headerAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="���ʲ����񸡺�">';
var headerAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="GOODS PLAN SEARCH">';





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

		window.top.SegAHeader.innerHTML = headerAE;

		SegA01.innerText='Products code';
		SegA03.innerText='Dept';
		SegA04.innerText='In charge name';
		SegA05.innerText='Products name(ja)';
		SegA06.innerText='Products name(en)';

		//SegA15.innerText='Assembly Info';
		//SegA17.innerText='Assembly fact';
		CreateDate.innerText='Creation date';
		ProgressStatus.innerText='Plan status';

		InputSegs.innerText='Input person';

		//ReviseNumber.innerText='Revise No.';
		ReviseDate.innerText='Revise date';

		ViewSearch1.innerHTML= vishImgE;
		ViewSearch2.innerHTML= vishImgE;

		//SegB03.innerText='Creation Factory';
		//SegB04.innerText='Location';

	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngCount == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		SegA01.innerText='���ʥ�����';
		SegA03.innerText='����';
		SegA04.innerText='ô����';
		SegA05.innerText='����̾��(���ܸ�)';
		SegA06.innerText='����̾��(�Ѹ�)';

		//SegA15.innerText='���å���֥�����';
		//SegA17.innerText='���å���֥깩��';
		CreateDate.innerText='��������';
		ProgressStatus.innerText='���ʹԾ���';

		InputSegs.innerText='���ϼ�';

		//ReviseNumber.innerText='�����ֹ�';
		ReviseDate.innerText='��������';

		ViewSearch1.innerHTML= vishImgJ;
		ViewSearch2.innerHTML= vishImgJ;

		//SegB03.innerText='��������';
		//SegB04.innerText='Ǽ�ʾ��';

	}

	return false;

}


//-->