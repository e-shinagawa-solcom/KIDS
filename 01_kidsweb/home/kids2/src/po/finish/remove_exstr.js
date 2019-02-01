<!--



// 帳票出力ボタン
var ListoutBt1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownPreviewButton( \'on\' , this );" onmouseout="fncBlownPreviewButton( \'off\' , this );fncAlphaOff( this );" src="' + blownpreviewbt1 + '" width="72" height="20" border="0" alt="PREVIEW"></a>';



// 閉じるボタン
var CloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownCloseJOn(this);" onmouseout="BlownCloseJOff(this);" src="' + blownclose1J + '" width="72" height="20" border="0" alt="閉じる"></a>';
var CloseBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownCloseEOn(this);" onmouseout="BlownCloseEOff(this);fncAlphaOff( this );" src="' + blownclose1E + '" width="72" height="20" border="0" alt="CLOSE"></a>';



	// デバッグ
	//var g_lngCode = 1;



function fncChgEtoJ()
{

	if ( g_lngCode == 0 )
	{

		FinishRegist.innerText      = 'DELETE COMPLETED';
		CloseBt.innerHTML           = CloseBtE1;

		ColumnProductsNo.innerText   = 'P.O. No.';

		if( typeof(ColumnListout) != 'undefined' )
		{
			ColumnListout.innerText      = 'LIST PREVIEW';     
			ListOutputBt.innerHTML       = ListoutBt1;
		}

	}

	else if ( g_lngCode == 1 )
	{

		FinishRegist.innerText      = '削除完了';
		CloseBt.innerHTML           = CloseBtJ1;


		ColumnProductsNo.innerText   = '発注ＮＯ.';

		if( typeof(ColumnListout) != 'undefined' )
		{
			ColumnListout.innerText      = '帳票プレビュー';     
			ListOutputBt.innerHTML       = ListoutBt1;
		}

	}

	return false;

}


//-->