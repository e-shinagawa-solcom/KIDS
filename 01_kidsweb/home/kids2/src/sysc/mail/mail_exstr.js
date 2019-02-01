<!--


//------------------------------------------------------------
// 解説 : 日本語・英語切替関数
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// 英語
	if ( lngSelfCode == 0 )
	{
		// 登録ボタン書き出し
		RegistBt.innerHTML = blownRegiBtE1;

		Column0.innerText = 'Admin email address';
	}

	// 日本語
	else if ( lngSelfCode == 1 )
	{
		// 登録ボタン書き出し
		RegistBt.innerHTML = blownRegiBtJ1;

		Column0.innerText = '管理者メールアドレス';
	}

	return false;

}


//-->