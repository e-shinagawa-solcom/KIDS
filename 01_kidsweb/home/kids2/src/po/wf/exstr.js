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

		Column0.innerText = '���̾��';
		Column1.innerText = '������롼��̾��';
		Column2.innerText = '���';
		Column3.innerText = 'ô����̾';
		Column4.innerText = '������';

	}

	return false;

}


//-->