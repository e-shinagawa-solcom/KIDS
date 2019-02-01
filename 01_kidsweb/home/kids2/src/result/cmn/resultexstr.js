

// クエリーボタンテーブル書き出しモジュール
function fncTitleOutput( lngCode )
{

	// クローズボタン(日本語)
	var closebtJ = '<a href="#" onclick="window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>';
	// クローズボタン(英語)
	var closebtE = '<a href="#" onclick="window.close();"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>';


	if( lngCode == 0 )
	{
		objQuery.innerHTML = closebtE;
	}
	else if( lngCode == 1 )
	{
		objQuery.innerHTML = closebtJ;
	}

	return false;
}





// 日本語英語切替
function fncChgEtoJ()
{

	// [lngLanguageCode]値取得
	var g_lngCode = window.lngLangCode.value;

	// 英語切替
	if( g_lngCode == 0 )
	{

		fncTitleOutput( 0 );

//		strError.innerText = 'SORRY: There is nothing corresponding.';

	}

	// 日本語切替
	else if( g_lngCode == 1 )
	{

		fncTitleOutput( 1 );

//		strError.innerText = 'SORRY: 該当するものがありません。';

	}

	return false;
}