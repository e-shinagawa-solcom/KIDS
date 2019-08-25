
var DeleteBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="DeleteJOn(this);" onmouseout="DeleteJOff(this);" src="' + delete1J + '" width="72" height="20" border="0" alt="削除"></a>';
var CloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseJOn(this);" onmouseout="CloseJOff(this);" src="' + close1J + '" width="72" height="20" border="0" alt="閉じる"></a>';

// 日本語英語切替
function fncChgEtoJ( strMode )
{
	//TODO:削除と詳細で分岐
	ControlTitle.innerText = '詳細確認';

	// 削除ボタン設定
	DeleteBt.innerHTML = DeleteBtJ1;

	// 閉じるボタン設定
	CloseBt.innerHTML = CloseBtJ1;

	return false;
}
