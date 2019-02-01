<!--



////////////////////////////////////////////////////////////////////
////////// 追加ボタンを隠す //////////
if( typeof(MasterAddBt) != 'undefined' )
{
	MasterAddBt.style.visibility = 'hidden';
}






function ChgEtoJ()
{

////////////////////////////////// ENGLISH /////////////////////////////////////


	if ( lngClickCode == 0 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		EtoJ.innerHTML = etojJbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML = maintitleE;

		///// INPUT A /////
		//SegAHeader.innerHTML = headerAE;

		lngClickCode = 1;
		lngLanguageCode = 0;

		window.Pwin.ChgEtoJ( 0 );
		window.NAVIwin.ChgEtoJ( 0 );

	}



////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngClickCode == 1 ) 
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		EtoJ.innerHTML = etojEbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML = maintitleJ;

		///// INPUT A /////
		//SegAHeader.innerHTML = headerAJ;

		lngClickCode = 0;
		lngLanguageCode = 1;

		window.Pwin.ChgEtoJ( 1 );
		window.NAVIwin.ChgEtoJ( 1 );

	}


	return false;

}


//-->