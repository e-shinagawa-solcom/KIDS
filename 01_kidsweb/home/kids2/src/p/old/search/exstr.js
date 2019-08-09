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


		schSchButton.innerHTML = schSchBtE1;
		schClrButton.innerHTML = schClrBtE1;


		lngClickCode = 1;
		lngLanguageCode = 0;

		window.Pwin.ChgEtoJ( 0 );
		window.NAVIwin.ChgEtoJ( 0 );

		window.MVwin_2.Msw1_2ChgEtoJ( 0 );
		window.MVwin_3.Msw1_3ChgEtoJ( 0 );
		window.MDwin.Msw2ChgEtoJ( 0 );
		window.VIwin.Msw7ChgEtoJ( 0 );
		window.MLwin.Msw4ChgEtoJ( 0 );
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


		schSchButton.innerHTML = schSchBtJ1;
		schClrButton.innerHTML = schClrBtJ1;



		lngClickCode = 0;
		lngLanguageCode = 1;

		window.Pwin.ChgEtoJ( 1 );
		window.NAVIwin.ChgEtoJ( 1 );

		window.MVwin_2.Msw1_2ChgEtoJ( 1 );
		window.MVwin_3.Msw1_3ChgEtoJ( 1 );
		window.MDwin.Msw2ChgEtoJ( 1 );
		window.VIwin.Msw7ChgEtoJ( 1 );
		window.MLwin.Msw4ChgEtoJ( 1 );
		window.INPUT2win.Msw9ChgEtoJ( 1 );

	}

	return false;

}


//-->