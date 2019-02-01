<!--








//------------------------------------------------------------
// 解説 : ヘッダーイメージの定義
//------------------------------------------------------------
var poheaderAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="発注書検索">';
var poheaderAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="PO SEARCH">';






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

		window.top.SegAHeader.innerHTML = poheaderAE;

		SegA01.innerText='Date';
		SegA02.innerText='P order No.';
		SegA03.innerText='Vendor';
		SegA04.innerText='Dept';
		SegA05.innerText='In charge name';

		SegPCode.innerText='Products code';

		SegA15.innerText='Regist date';

		SegA18.innerText='Input person';

		//SegB01.innerText='Products code/name';

		if( typeof(InvalidSegs) != 'undefined' )
		{
			InvalidSegs.innerText='Invalid';
		}

		if( typeof(AdminSet) != 'undefined' )
		{
			AdminMode.innerText='Administrator mode';
		}


		ViewSearch1.innerHTML= vishImgE;



	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngCount == 1 )
	{

		window.top.SegAHeader.innerHTML = poheaderAJ;

		SegA01.innerText='計上日';
		SegA02.innerText='発注ＮＯ.';
		SegA03.innerText='仕入先';
		SegA04.innerText='部門';
		SegA05.innerText='担当者';

		SegPCode.innerText='製品コード';

		SegA15.innerText='登録日';

		SegA18.innerText='入力者';

		//SegB01.innerText='製品コード・名称';

		if( typeof(InvalidSegs) != 'undefined' )
		{
			InvalidSegs.innerText='無効';
		}

		if( typeof(AdminSet) != 'undefined' )
		{
			AdminMode.innerText='管理者モード';
		}


		ViewSearch1.innerHTML= vishImgJ;


	}

	return false;

}


//-->