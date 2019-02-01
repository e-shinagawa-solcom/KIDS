<!--


//------------------------------------------------------------
// 解説 : 日本語・英語切替関数
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// ログボタン書き出し
	BackBt.innerHTML = backsmallBt;

	// PREV & NEXTボタン書き出し
	NextBt.innerHTML = blownnextBt;
	PrevBt.innerHTML = blownprevBt;

	// 英語
	if ( lngSelfCode == 0 )
	{

		ControlTitle.innerText = 'LOG';
		Column0.innerText      = 'No';
		Column1.innerText      = 'Title';
		Column2.innerText      = 'Registration date';
		Column3.innerText      = 'Detail';

	}

	// 日本語
	else if ( lngSelfCode == 1 )
	{

		ControlTitle.innerText = 'ログ';
		Column0.innerText      = 'No';
		Column1.innerText      = 'タイトル';
		Column2.innerText      = '登録日時';
		Column3.innerText      = '詳細';

	}

	return false;

}


//-->