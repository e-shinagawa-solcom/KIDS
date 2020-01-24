// 帳票出力ボタン
var ListoutBt1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownPreviewButton( \'on\' , this );" onmouseout="fncBlownPreviewButton( \'off\' , this );fncAlphaOff( this );" src="' + blownpreviewbt1 + '" width="72" height="20" border="0" alt="PREVIEW"></a>';

// 閉じるボタン
var CloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownCloseJOn(this);" onmouseout="BlownCloseJOff(this);" src="' + blownclose1J + '" width="72" height="20" border="0" alt="閉じる"></a>';
var CloseBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownCloseEOn(this);" onmouseout="BlownCloseEOff(this);fncAlphaOff( this );" src="' + blownclose1E + '" width="72" height="20" border="0" alt="CLOSE"></a>';
var CloseBtJ1Blue = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseJOn(this);" onmouseout="CloseJOff(this);" src="' + close1J + '" width="72" height="20" border="0" alt="閉じる"></a>';

function fncChgEtoJ()
{
	FinishRegist.innerText      = '削除完了';
	CloseBt.innerHTML           = CloseBtJ1;

	return false;
}
