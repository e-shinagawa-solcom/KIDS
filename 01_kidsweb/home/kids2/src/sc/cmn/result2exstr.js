
var DeleteBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="DeleteJOn(this);" onmouseout="DeleteJOff(this);" src="' + delete1J + '" width="72" height="20" border="0" alt="削除"></a>';
var CloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseJOn(this);" onmouseout="CloseJOff(this);" src="' + close1J + '" width="72" height="20" border="0" alt="閉じる"></a>';

// 日本語英語切替
function fncChgEtoJ( strMode )
{
	if (strMode == 'detail'){
		// -----------------
		//   詳細画面
		// -----------------
		// ダイアログタイトル
		ControlTitle.innerText = '詳細確認';
		// メッセージタイトル→なし
		document.getElementById("MessageTitle").style.display ="none";
		// 閉じるボタン追加
		CloseBt.innerHTML = CloseBtJ1;
	}else if (strMode == 'delete'){
		// -----------------
		//   削除画面
		// -----------------
		// ダイアログタイトル
		ControlTitle.innerText = '削除確認';
		// メッセージタイトル
		document.getElementById("MessageTitle").style.display ="block";
		MessageTitle.innerText = '削除しますか？';
		// 閉じるボタン追加
		CloseBt.innerHTML = CloseBtJ1;
		// 削除ボタン追加
		DeleteBt.innerHTML = DeleteBtJ1;
	}

	return false;
}
