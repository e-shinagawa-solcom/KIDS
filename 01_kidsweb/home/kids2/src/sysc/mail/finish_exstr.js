<!--


//------------------------------------------------------------
// 解説 : 日本語・英語切替関数
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// 英語
	if ( lngSelfCode == 0 )
	{
		// ログボタン書き出し
		BackBt.innerHTML = BackBtE1;

		FinishRegist.innerText = 'REGISTRAITION COMPLETED';

		Column0.innerText = 'Admin email address';
	}

	// 日本語
	else if ( lngSelfCode == 1 )
	{
		// ログボタン書き出し
		BackBt.innerHTML = BackBtJ1;

		FinishRegist.innerText = '登録完了';

		Column0.innerText = '管理者メールアドレス';
	}

	return false;

}


//-->