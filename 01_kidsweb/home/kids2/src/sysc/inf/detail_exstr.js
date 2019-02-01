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

		ControlTitle.innerText = 'DETAIL';
		Column0.innerText      = 'Title';
		Column1.innerText      = 'Message';
		Column2.innerText      = 'Registration date';

	}

	// 日本語
	else if ( lngSelfCode == 1 )
	{

		ControlTitle.innerText = '詳細';
		Column0.innerText      = 'タイトル';
		Column1.innerText      = 'メッセージ';
		Column2.innerText      = '登録日時';

	}

	return false;

}


//-->