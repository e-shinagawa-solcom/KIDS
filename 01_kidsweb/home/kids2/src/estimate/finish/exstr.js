


// Ģɼ���ϥܥ���
var ListoutBt1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownPreviewButton( \'on\' , this );" onmouseout="fncBlownPreviewButton( \'off\' , this );fncAlphaOff( this );" src="' + blownpreviewbt1 + '" width="72" height="20" border="0" alt="PREVIEW"></a>';



// �Ĥ���ܥ���
var CloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownCloseJOn(this);" onmouseout="BlownCloseJOff(this);" src="' + blownclose1J + '" width="72" height="20" border="0" alt="�Ĥ���"></a>';
var CloseBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownCloseEOn(this);" onmouseout="BlownCloseEOff(this);fncAlphaOff( this );" src="' + blownclose1E + '" width="72" height="20" border="0" alt="CLOSE"></a>';



	// �ǥХå�
	//var g_lngCode = 1;



function fncChgEtoJ( lngCode )
{
	var txt = '';

	if( lngCode == 0 )
	{
		if( document.all.lngSaveType.value == 0 )
		{
			txt = 'REGISTRAITION COMPLETED';
		}
		else
		{
			txt = 'PRE SAVE COMPLETED';
		}

		FinishRegist.innerText = txt;
		CloseBt.innerHTML      = CloseBtE1;

		if( typeof(ColumnListout) != 'undefined' )
		{
			ColumnListout.innerText = 'LIST PREVIEW';     
			ListOutputBt.innerHTML  = ListoutBt1;
		}
	}

	else if( lngCode == 1 )
	{
		if( document.all.lngSaveType.value == 0 )
		{
			txt = '��Ͽ��λ';
		}
		else
		{
			txt = '����¸��λ';
		}

		FinishRegist.innerText = txt;
		CloseBt.innerHTML      = CloseBtJ1;

		if( typeof(ColumnListout) != 'undefined' )
		{
			ColumnListout.innerText = 'Ģɼ�ץ�ӥ塼';     
			ListOutputBt.innerHTML  = ListoutBt1;
		}
	}


	return false;
}
