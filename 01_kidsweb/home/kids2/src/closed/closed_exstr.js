<!--


//------------------------------------------------------------
// 解説 : 日本語・英語切替関数
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// 英語
	if ( lngSelfCode == 0 )
	{
		// ボタン書き出し
		ProcessBt.innerHTML = blownProcessBtE1;
		RivivalBt.innerHTML = blownRivivalBtE1;

		Column0.innerText = 'Date';
		Column1.innerText = 'Target';

	}

	// 日本語
	else if ( lngSelfCode == 1 )
	{
		// ボタン書き出し
		ProcessBt.innerHTML = blownProcessBtJ1;
		RivivalBt.innerHTML = blownRivivalBtJ1;

		Column0.innerText = '計上日';
		Column1.innerText = '対象';

	}

	return false;

}


//-->