<!--


//------------------------------------------
// ���� : ���ܥ�������
//------------------------------------------
var CloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownBackJOn(this);" onmouseout="BlownBackJOff(this);" src="' + blownbackJ1 + '" width="72" height="20" border="0" alt="���"></a>';
var CloseBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownBackEOn(this);" onmouseout="BlownBackEOff(this);fncAlphaOff( this );" src="' + blownbackE1 + '" width="72" height="20" border="0" alt="BACK"></a>';


function ChgEtoJ()
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngClickCode == 0 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		//EtoJ.innerHTML = etojJbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML = maintitleE;


		///// INPUT A /////
		SegAHeader.innerHTML = headerAE;


		FinishRegist.innerText = 'REGISTRAITION COMPLETED';
		CloseBt.innerHTML      = CloseBtE1;


		lngLanguageCode = 0;
		lngClickCode = 1;

		//window.NAVIwin.ChgEtoJ( 0 );


	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngClickCode == 1 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		//EtoJ.innerHTML = etojEbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML = maintitleJ;


		///// INPUT A /////
		SegAHeader.innerHTML = headerAJ;

		FinishRegist.innerText = '��Ͽ��λ';
		CloseBt.innerHTML      = CloseBtJ1;


		lngLanguageCode = 1;
		lngClickCode = 0;

		//window.NAVIwin.ChgEtoJ( 1 );


	}

	return false;

}


//-->