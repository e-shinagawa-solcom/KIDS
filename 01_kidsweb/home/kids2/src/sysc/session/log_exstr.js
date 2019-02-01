<!--


//------------------------------------------------------------
// 解説 : 日本語・英語切替関数
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// ログボタン書き出し
	BackBt.innerHTML = backsmallBt;

	// 英語
	if ( lngSelfCode == 0 )
	{

		ControlTitle.innerText = 'LOG';
		Column0.innerText      = 'No';
		Column1.innerText      = 'Session ID';
		Column2.innerText      = 'User Code';
		Column3.innerText      = 'User ID';
		Column4.innerText      = 'Password';
		Column5.innerText      = 'Login Time';
		Column6.innerText      = 'Status';

	}

	// 日本語
	else if ( lngSelfCode == 1 )
	{

		ControlTitle.innerText = 'ログ';
		Column0.innerText      = 'No';
		Column1.innerText      = 'セッションＩＤ';
		Column2.innerText      = 'ユーザーコード';
		Column3.innerText      = 'ユーザーＩＤ';
		Column4.innerText      = 'パスワード';
		Column5.innerText      = 'ログイン日時';
		Column6.innerText      = '状態';

	}

	return false;

}


//-->