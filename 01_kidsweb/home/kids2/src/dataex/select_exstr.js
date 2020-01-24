<!--



//------------------------------------------------------------
// 解説 : ヘッダーイメージの定義
//------------------------------------------------------------
var headerAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="データ検索">';
var headerAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="DATA FILE SEARCH">';





//------------------------------------------------------------
// 解説 : 日本語・英語切替関数
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// 英語
	if ( lngSelfCode == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		SltList.innerText     = 'Select';
		ControlName.innerText = 'Control name';
		ListName.innerText    = 'Data file name';

	}

	// 日本語
	else if ( lngSelfCode == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		SltList.innerText     = '選択';
		ControlName.innerText = '管理名称';
		ListName.innerText    = 'データ名称';

	}

	return false;

}


//-->