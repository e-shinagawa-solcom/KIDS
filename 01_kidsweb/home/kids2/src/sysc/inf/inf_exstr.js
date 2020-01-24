<!--


//------------------------------------------------------------
// 解説 : 日本語・英語切替関数
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// ログボタン書き出し
	LogBt.innerHTML = logBt;

	// 英語
	if ( lngSelfCode == 0 )
	{
		// 登録ボタン書き出し
		RegistBt.innerHTML = blownRegiBtE1;

		Column0.innerText = 'Title';
		Column1.innerText = 'Message';
	}

	// 日本語
	else if ( lngSelfCode == 1 )
	{
		// 登録ボタン書き出し
		RegistBt.innerHTML = blownRegiBtJ1;

		Column0.innerText = 'タイトル';
		Column1.innerText = 'メッセージ';
	}

	return false;

}


//-->