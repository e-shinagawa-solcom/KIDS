<!--



//////////////////////////////////////////////////////////////////
////////// オブジェクトのオンロード処理関数 //////////
function fncMasterSearchOnload()
{
	ChgEtoJ( window.top.lngLanguageCode );
}







///// HEADER IMAGE /////
var headerAJ = '<img src="' + mgroupJ + '" width="949" height="30" border="0" alt="グループマスタ検索">';
var headerAE = '<img src="' + mgroupE + '" width="949" height="30" border="0" alt="GROUP MASTER SEARCH">';




function ChgEtoJ( lngCount )
{

	parent.schSchButton.style.visibility = 'visible';
	parent.schClrButton.style.visibility = 'visible';

////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		SegA01.innerText = 'Group name';

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

		SegA01.innerText = 'グループ名称';

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