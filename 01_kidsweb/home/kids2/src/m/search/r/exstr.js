<!--



//////////////////////////////////////////////////////////////////
////////// オブジェクトのオンロード処理関数 //////////
function fncMasterSearchOnload()
{
	ChgEtoJ( window.top.lngLanguageCode );
}






///// HEADER IMAGE /////
var headerAJ = '<img src="' + mmoneyrateJ + '" width="949" height="30" border="0" alt="通貨レートマスタ検索">';
var headerAE = '<img src="' + mmoneyrateE + '" width="949" height="30" border="0" alt="MONETARY RATE MASTER SEARCH">';




function ChgEtoJ( lngCount )
{

	parent.schSchButton.style.visibility = 'visible';
	parent.schClrButton.style.visibility = 'visible';

////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		/*if( typeof(parent.schSchButton) != 'undefined' )
		{
			parent.schSchButton.innerHTML = parent.schSchBtE1;
		}

		if( typeof(parent.schClrButton) != 'undefined' )
		{
			parent.schClrButton.innerHTML = parent.schClrBtE1;
		}*/

	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngCount == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

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