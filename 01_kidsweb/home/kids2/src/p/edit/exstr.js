
//-------------------------------------------------------
// 解説 : エディットデータ適用ボタン定義
//-------------------------------------------------------
var EditCloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="閉じる"></a>';

var EditCloseBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>';





//-------------------------------------------------------
// 解説 : 画像挿入ボタン定義
//-------------------------------------------------------
var EditInsertBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncInsertButton( \'onJ\' , this );" onmouseout="fncInsertButton( \'offJ\' , this );fncAlphaOff( this );" src="' + insertbtJ1 + '" width="72" height="20" border="0" alt="挿入"></a>';

var EditInsertBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncInsertButton( \'onE\' , this );" onmouseout="fncInsertButton( \'offE\' , this );fncAlphaOff( this );" src="' + insertbtE1 + '" width="72" height="20" border="0" alt="INSERT"></a>';





//-------------------------------------------------------
// 解説 : エディットデータ適用ボタン・画像挿入ボタン、日本語英語切替関数
//-------------------------------------------------------
function fncEditDataSet( lngLanguageCode )
{

	if( lngLanguageCode == 0 )
	{
		strImages.innerText    = 'Images';
		EditCloseBt.innerHTML  = EditCloseBtE1;
		EditInsertBt.innerHTML = EditInsertBtE1;
	}
	else if( lngLanguageCode == 1 )
	{
		strImages.innerText    = '画像';
		EditCloseBt.innerHTML  = EditCloseBtJ1;
		EditInsertBt.innerHTML = EditInsertBtJ1;
	}

	return false;
}