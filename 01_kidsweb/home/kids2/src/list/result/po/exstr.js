<!--


function fncChgEtoJ( lngLanguageCode )
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngLanguageCode == 0 )
	{

		Column0.innerText = 'P.O. No.';
		Column1.innerText = 'Input person';
		Column2.innerText = 'Supplier';
		Column3.innerText = 'Dept';
		Column4.innerText = 'In charge name';
		Column5.innerText = 'COPY Preview';
		Column6.innerText = 'Preview';

	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngLanguageCode == 1 )
	{

		Column0.innerText = '発注 No.';
		Column1.innerText = '入力者';
		Column2.innerText = '仕入先';
		Column3.innerText = '部門';
		Column4.innerText = '担当者';
		Column5.innerText = 'COPY プレビュー';
		Column6.innerText = 'プレビュー';

	}

	return false;

}


//-->