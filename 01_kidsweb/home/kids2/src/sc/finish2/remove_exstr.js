// 罌冱請恘示正件
var ListoutBt1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownPreviewButton( \'on\' , this );" onmouseout="fncBlownPreviewButton( \'off\' , this );fncAlphaOff( this );" src="' + blownpreviewbt1 + '" width="72" height="20" border="0" alt="PREVIEW"></a>';

// 岉元月示正件
var CloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownCloseJOn(this);" onmouseout="BlownCloseJOff(this);" src="' + blownclose1J + '" width="72" height="20" border="0" alt="岉元月"></a>';
var CloseBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownCloseEOn(this);" onmouseout="BlownCloseEOff(this);fncAlphaOff( this );" src="' + blownclose1E + '" width="72" height="20" border="0" alt="CLOSE"></a>';
var CloseBtJ1Blue = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseJOn(this);" onmouseout="CloseJOff(this);" src="' + close1J + '" width="72" height="20" border="0" alt="岉元月"></a>';

function fncChgEtoJ()
{
	FinishRegist.innerText      = '綽輪敦弇';
	CloseBt.innerHTML           = CloseBtJ1;

	return false;
}
