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

		Column0.innerText = '���ʥ�����';
		Column1.innerText = '����̾��';
		Column2.innerText = '���ϼ�';
		Column3.innerText = '����';
		Column4.innerText = 'ô����';
		Column5.innerText = 'COPY �ץ�ӥ塼';
		Column6.innerText = '�ץ�ӥ塼';

	}

	return false;

}


//-->