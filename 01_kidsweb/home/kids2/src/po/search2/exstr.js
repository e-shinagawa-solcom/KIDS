<!--


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
		SegAHeader.innerHTML = headerAE;

		if( typeof(schSchButton) != 'undefined' )
		{
			schSchButton.innerHTML = schSchBtE1;
		}

		if( typeof(schClrButton) != 'undefined' )
		{
			schClrButton.innerHTML = schClrBtE1;
		}

		lngLanguageCode = 0;
		lngClickCode = 1;

		window.Pwin.ChgEtoJ( 0 );
		window.NAVIwin.ChgEtoJ( 0 );

		window.SUPwin.Msw8ChgEtoJ( 0 );
		window.MDwin.Msw2ChgEtoJ( 0 );
		window.MGwin.Msw3ChgEtoJ( 0 );
		window.INPUT2win.Msw9ChgEtoJ( 0 );

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
		SegAHeader.innerHTML = headerAJ;


		if( typeof(schSchButton) != 'undefined' )
		{
			schSchButton.innerHTML = schSchBtJ1;
		}

		if( typeof(schClrButton) != 'undefined' )
		{
			schClrButton.innerHTML = schClrBtJ1;
		}


		lngLanguageCode = 1;
		lngClickCode = 0;

		window.Pwin.ChgEtoJ( 1 );
		window.NAVIwin.ChgEtoJ( 1 );

		window.SUPwin.Msw8ChgEtoJ( 1 );
		window.MDwin.Msw2ChgEtoJ( 1 );
		window.MGwin.Msw3ChgEtoJ( 1 );
		window.INPUT2win.Msw9ChgEtoJ( 1 );


	}

	return false;

}


//-->