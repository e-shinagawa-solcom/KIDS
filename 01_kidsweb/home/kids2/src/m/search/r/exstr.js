<!--



//////////////////////////////////////////////////////////////////
////////// ���֥������ȤΥ�����ɽ����ؿ� //////////
function fncMasterSearchOnload()
{
	ChgEtoJ( window.top.lngLanguageCode );
}






///// HEADER IMAGE /////
var headerAJ = '<img src="' + mmoneyrateJ + '" width="949" height="30" border="0" alt="�̲ߥ졼�ȥޥ�������">';
var headerAE = '<img src="' + mmoneyrateE + '" width="949" height="30" border="0" alt="MONETARY RATE MASTER SEARCH">';




function ChgEtoJ( lngCount )
{

	parent.schSchButton.style.visibility = 'visible';
	parent.schClrButton.style.visibility = 'visible';

////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		SegA01.innerText = 'Rate type';
		SegA02.innerText = 'Rate';
		SegA03.innerText = 'Currency rate';

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

		SegA01.innerText = '�졼�ȥ�����';
		SegA02.innerText = '�̲�ñ��';
		SegA03.innerText = '���ߤ��̲ߥ졼��';

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