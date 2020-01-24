<!--



//////////////////////////////////////////////////////////////////
////////// オブジェクトのオンロード処理関数 //////////
function fncMasterSearchOnload()
{
	ChgEtoJ( window.top.lngLanguageCode );
}





///// HEADER IMAGE /////
var headerAJ = '<img src="' + mcompanyJ + '" width="949" height="30" border="0" alt="会社マスタ検索">';
var headerAE = '<img src="' + mcompanyE + '" width="949" height="30" border="0" alt="COMPANY MASTER SEARCH">';




function ChgEtoJ( lngCount )
{

	parent.schSchButton.style.visibility = 'visible';
	parent.schClrButton.style.visibility = 'visible';


	window.top.SegAHeader.innerHTML = headerAJ;


		/*if( typeof(parent.schSchButton) != 'undefined' )
		{
			parent.schSchButton.innerHTML = parent.schSchBtJ1;
		}

		if( typeof(parent.schClrButton) != 'undefined' )
		{
			parent.schClrButton.innerHTML = parent.schClrBtJ1;
		}*/


	return false;

}


//-->