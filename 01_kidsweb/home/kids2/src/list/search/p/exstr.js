<!--





//------------------------------------------------------------
// 解説 : ヘッダーイメージの定義
//------------------------------------------------------------
var headerAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="商品化企画書検索">';
var headerAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="GOODS PLAN SEARCH">';





//------------------------------------------------------------
// 解説 : 日本語・英語切替関数
//------------------------------------------------------------
function ChgEtoJ( lngCount )
{


	//------------------------------------------------------------
	// 解説 : [検索][クリア]ボタンを表示
	//------------------------------------------------------------
	if( typeof(window.top.schSchButton) != 'undefined' )
	{
		window.top.schSchButton.style.visibility = 'visible';
	}

	if( typeof(window.top.schClrButton) != 'undefined' )
	{
		window.top.schClrButton.style.visibility = 'visible';
	}


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		SegA01.innerText='Products code';
		SegA03.innerText='Dept';
		SegA04.innerText='In charge name';
		SegA05.innerText='Products name(ja)';
		SegA06.innerText='Products name(en)';

		//SegA15.innerText='Assembly Info';
		//SegA17.innerText='Assembly fact';
		CreateDate.innerText='Creation date';
		ProgressStatus.innerText='Plan status';

		InputSegs.innerText='Input person';

		//ReviseNumber.innerText='Revise No.';
		ReviseDate.innerText='Revise date';

		ViewSearch1.innerHTML= vishImgE;
		ViewSearch2.innerHTML= vishImgE;

		//SegB03.innerText='Creation Factory';
		//SegB04.innerText='Location';

	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngCount == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		SegA01.innerText='製品コード';
		SegA03.innerText='部門';
		SegA04.innerText='担当者';
		SegA05.innerText='製品名称(日本語)';
		SegA06.innerText='製品名称(英語)';

		//SegA15.innerText='アッセンブリ内容';
		//SegA17.innerText='アッセンブリ工場';
		CreateDate.innerText='作成日時';
		ProgressStatus.innerText='企画進行状況';

		InputSegs.innerText='入力者';

		//ReviseNumber.innerText='改訂番号';
		ReviseDate.innerText='改訂日時';

		ViewSearch1.innerHTML= vishImgJ;
		ViewSearch2.innerHTML= vishImgJ;

		//SegB03.innerText='生産工場';
		//SegB04.innerText='納品場所';

	}

	return false;

}


//-->