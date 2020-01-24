


function ChgEtoJ()
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngClickCode == 0 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		EtoJ.innerHTML	= etojJbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML	= maintitleE;


		///// INPUT A /////
		SegAHeader.innerHTML	= headerAE;



		colExcel.innerText	= 'Excel file';


		lngLanguageCode	= 0;
		lngClickCode	= 1;
	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngClickCode == 1 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		EtoJ.innerHTML	= etojEbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML	= maintitleJ;


		///// INPUT A /////
		SegAHeader.innerHTML	= headerAJ;



		colExcel.innerText	= 'Excel ファイル';


		lngLanguageCode	= 1;
		lngClickCode	= 0;
	}

	return false;

}
