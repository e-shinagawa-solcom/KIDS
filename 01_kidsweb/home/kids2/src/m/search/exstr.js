<!--


function ChgEtoJ()
{


	schSchButton.style.visibility = 'hidden';
	schClrButton.style.visibility = 'hidden';

////////////////////////////////// ENGLISH /////////////////////////////////////


	if ( lngClickCode == 0 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		//EtoJ.innerHTML = etojJbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML = maintitleE;

		///// INPUT A /////
		//SegAHeader.innerHTML = headerAE;

		/*if( typeof(schSchButton) != 'undefined' )
		{
			schSchButton.innerHTML = schSchBtE1;
		}

		if( typeof(schClrButton) != 'undefined' )
		{
			schClrButton.innerHTML = schClrBtE1;
		}*/


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
		//EtoJ.innerHTML = etojEbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML = maintitleJ;

		///// INPUT A /////
		//SegAHeader.innerHTML = headerAJ;

		/*if( typeof(schSchButton) != 'undefined' )
		{
			schSchButton.innerHTML = schSchBtJ1;
		}

		if( typeof(schClrButton) != 'undefined' )
		{
			schClrButton.innerHTML = schClrBtJ1;
		}*/


		lngClickCode = 0;
		lngLanguageCode = 1;

		window.Pwin.ChgEtoJ( 1 );
		window.NAVIwin.ChgEtoJ( 1 );

	}


	return false;

}


//-->