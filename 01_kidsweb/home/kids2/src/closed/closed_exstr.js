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


	}

	// 日本語
	else if ( lngSelfCode == 1 )
	{
		// ボタン書き出し
		ProcessBt.innerHTML = blownProcessBtJ1;
		RivivalBt.innerHTML = blownRivivalBtJ1;

		
	}

	return false;

}


//-->