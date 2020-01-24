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


		strSelectFunctions.innerText = 'SELECT FUNCTIONS';


		///// REGISTRATION /////
		if ( typeof(RegistNaviBt1) != 'undefined' )
		{
			RegistNaviBt1.innerHTML = reginaviE1;
		}

		if ( typeof(RegistNaviBt3) != 'undefined' )
		{
			RegistNaviBt3.innerHTML = reginaviE3;
		}


		///// SEARCH /////
		if ( typeof(SearchNaviBt1) != 'undefined' )
		{
			SearchNaviBt1.innerHTML = schnaviE1;
		}

		if ( typeof(SearchNaviBt3) != 'undefined' )
		{
			SearchNaviBt3.innerHTML = schnaviE3;
		}


		///// LIST OUTPUT /////
		if ( typeof(ListExNaviBt1) != 'undefined' )
		{
			ListExNaviBt1.innerHTML = listoutnaviE1;
		}

		if ( typeof(ListExNaviBt3) != 'undefined' )
		{
			ListExNaviBt3.innerHTML = listoutnaviE3;
		}


		///// DATA EXPORT /////
		if ( typeof(DataExNaviBt1) != 'undefined' )
		{
			DataExNaviBt1.innerHTML = dataexnaviE1;
		}

		if ( typeof(DataExNaviBt3) != 'undefined' )
		{
			DataExNaviBt3.innerHTML = dataexnaviE3;
		}


		///// MASTER /////
		if ( typeof(MasterNaviBt1) != 'undefined' )
		{
			MasterNaviBt1.innerHTML = mstnaviE1;
		}

		if ( typeof(MasterNaviBt3) != 'undefined' )
		{
			MasterNaviBt3.innerHTML = mstnaviE3;
		}


		lngLanguageCode = 0;
		lngClickCode = 1;


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


		strSelectFunctions.innerText = '機能選択';


		///// REGISTRATION /////
		if ( typeof(RegistNaviBt1) != 'undefined' )
		{
			RegistNaviBt1.innerHTML = reginaviJ1;
		}

		if ( typeof(RegistNaviBt3) != 'undefined' )
		{
			RegistNaviBt3.innerHTML = reginaviJ3;
		}


		///// SEARCH /////
		if ( typeof(SearchNaviBt1) != 'undefined' )
		{
			SearchNaviBt1.innerHTML = schnaviJ1;
		}

		if ( typeof(SearchNaviBt3) != 'undefined' )
		{
			SearchNaviBt3.innerHTML = schnaviJ3;
		}


		///// LIST OUTPUT /////
		if ( typeof(ListExNaviBt1) != 'undefined' )
		{
			ListExNaviBt1.innerHTML = listoutnaviJ1;
		}

		if ( typeof(ListExNaviBt3) != 'undefined' )
		{
			ListExNaviBt3.innerHTML = listoutnaviJ3;
		}


		///// DATA EXPORT /////
		if ( typeof(DataExNaviBt1) != 'undefined' )
		{
			DataExNaviBt1.innerHTML = dataexnaviJ1;
		}

		if ( typeof(DataExNaviBt3) != 'undefined' )
		{
			DataExNaviBt3.innerHTML = dataexnaviJ3;
		}


		///// MASTER /////
		if ( typeof(MasterNaviBt1) != 'undefined' )
		{
			MasterNaviBt1.innerHTML = mstnaviJ1;
		}

		if ( typeof(MasterNaviBt3) != 'undefined' )
		{
			MasterNaviBt3.innerHTML = mstnaviJ3;
		}


		lngLanguageCode = 1;
		lngClickCode = 0;

	}

	return false;

}


//-->