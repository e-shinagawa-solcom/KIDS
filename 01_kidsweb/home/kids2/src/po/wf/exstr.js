<!--


function fncChgEtoJ( lngLanguageCode )
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngLanguageCode == 0 )
	{

		Column0.innerText = 'Order name';
		Column1.innerText = 'Order group name';
		Column2.innerText = 'Order';
		Column3.innerText = 'In charge name';
		Column4.innerText = 'Limit date';

	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngLanguageCode == 1 )
	{

		Column0.innerText = '順序名称';
		Column1.innerText = '順序グループ名称';
		Column2.innerText = '順序';
		Column3.innerText = '担当者名';
		Column4.innerText = '期限日';

	}

	return false;

}


//-->