

// �Ĥ���ܥ���
var CloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownCloseJOn(this);" onmouseout="BlownCloseJOff(this);" src="' + blownclose1J + '" width="72" height="20" border="0" alt="�Ĥ���"></a>';
var CloseBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownCloseEOn(this);" onmouseout="BlownCloseEOff(this);fncAlphaOff( this );" src="' + blownclose1E + '" width="72" height="20" border="0" alt="CLOSE"></a>';



	// �ǥХå�
	//var g_lngCode = 1;



function fncChgEtoJ()
{

	if ( g_lngCode == 0 )
	{

		FinishRegist.innerText = 'REGISTRAITION COMPLETED';
		CloseBt.innerHTML      = CloseBtE1;


	}

	else if ( g_lngCode == 1 )
	{

		FinishRegist.innerText = '��Ͽ��λ';
		CloseBt.innerHTML      = CloseBtJ1;

	}

	return false;

}