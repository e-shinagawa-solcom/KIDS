<!--



//////////////////////////////////////////////////////////////////
////////// ���֥������ȤΥ�����ɽ����ؿ� //////////
function fncMasterSearchOnload()
{
	ChgEtoJ( window.top.lngLanguageCode );
}





///// HEADER IMAGE /////
var headerAJ = '<img src="' + mcompanyJ + '" width="949" height="30" border="0" alt="��ҥޥ�������">';
var headerAE = '<img src="' + mcompanyE + '" width="949" height="30" border="0" alt="COMPANY MASTER SEARCH">';




function ChgEtoJ( lngCount )
{

	parent.schSchButton.style.visibility = 'visible';
	parent.schClrButton.style.visibility = 'visible';

////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		SegA01.innerText = 'Company attribute';
		SegA02.innerText = 'Company name';

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

		SegA01.innerText = '���°��';
		SegA02.innerText = '���̾��';

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