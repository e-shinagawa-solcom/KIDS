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

		Column0.innerText = 'ȯ�� No.';
		Column1.innerText = '���ϼ�';
		Column2.innerText = '������';
		Column3.innerText = '����';
		Column4.innerText = 'ô����';
		Column5.innerText = 'COPY �ץ�ӥ塼';
		Column6.innerText = '�ץ�ӥ塼';

	}

	return false;

}


//-->