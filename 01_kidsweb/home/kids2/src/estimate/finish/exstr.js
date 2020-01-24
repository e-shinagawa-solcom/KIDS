


// 帳票出力ボタン
var ListoutBt1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownPreviewButton( \'on\' , this );" onmouseout="fncBlownPreviewButton( \'off\' , this );fncAlphaOff( this );" src="' + blownpreviewbt1 + '" width="72" height="20" border="0" alt="PREVIEW"></a>';



// 閉じるボタン
var CloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownCloseJOn(this);" onmouseout="BlownCloseJOff(this);" src="' + blownclose1J + '" width="72" height="20" border="0" alt="閉じる"></a>';
var CloseBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownCloseEOn(this);" onmouseout="BlownCloseEOff(this);fncAlphaOff( this );" src="' + blownclose1E + '" width="72" height="20" border="0" alt="CLOSE"></a>';



	// デバッグ
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
			txt = '登録完了';
		}
		else
		{
			txt = '仮保存完了';
		}

		FinishRegist.innerText = txt;
		CloseBt.innerHTML      = CloseBtJ1;

		if( typeof(ColumnListout) != 'undefined' )
		{
			ColumnListout.innerText = '帳票プレビュー';     
			ListOutputBt.innerHTML  = ListoutBt1;
		}
	}


	return false;
}
