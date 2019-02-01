<!--


function fncChgEtoJ( lngLanguageCode )
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngLanguageCode == 0 )
	{

		Column0.innerText = 'Products code';
		Column1.innerText = 'Products name';
		Column2.innerText = 'Input person';
		Column3.innerText = 'Dept';
		Column4.innerText = 'In charge name';
		Column5.innerText = 'COPY Preview';
		Column6.innerText = 'Preview';

	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngLanguageCode == 1 )
	{

		Column0.innerText = '製品コード';
		Column1.innerText = '製品名称';
		Column2.innerText = '入力者';
		Column3.innerText = '部門';
		Column4.innerText = '担当者';
		Column5.innerText = 'COPY プレビュー';
		Column6.innerText = 'プレビュー';

	}

	return false;

}


//-->