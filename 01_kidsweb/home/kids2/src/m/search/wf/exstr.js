<!--



//////////////////////////////////////////////////////////////////
////////// ���֥������ȤΥ�����ɽ����ؿ� //////////
function fncMasterSearchOnload()
{
	ChgEtoJ( window.top.lngLanguageCode );
}






///// HEADER IMAGE /////
var headerAJ = '<img src="' + mwforderJ + '" width="949" height="30" border="0" alt="����ե�����ޥ�������">';
var headerAE = '<img src="' + mwforderE + '" width="949" height="30" border="0" alt="WORK FLOW ORDER MASTER SEARCH">';




function ChgEtoJ( lngCount )
{

	parent.schSchButton.style.visibility = 'visible';
	parent.schClrButton.style.visibility = 'visible';

////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		SegA01.innerText = 'Order name';
		SegA02.innerText = 'Group name';
		SegA03.innerText = 'In charge name';

		if( typeof(parent.schSchButton) != 'undefined' )
		{
			parent.schSchButton.innerHTML = parent.schSchBtE1;
		}

		if( typeof(parent.schClrButton) != 'undefined' )
		{
			parent.schClrButton.innerHTML = parent.schClrBtE1;
		}

	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngCount == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		SegA01.innerText = '���̾��';
		SegA02.innerText = '���롼��̾��';
		SegA03.innerText = 'ô����';

		if( typeof(parent.schSchButton) != 'undefined' )
		{
			parent.schSchButton.innerHTML = parent.schSchBtJ1;
		}

		if( typeof(parent.schClrButton) != 'undefined' )
		{
			parent.schClrButton.innerHTML = parent.schClrBtJ1;
		}

	}

	return false;

}


//-->