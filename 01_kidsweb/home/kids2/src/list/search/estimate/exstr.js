<!--








//------------------------------------------------------------
// 解説 : ヘッダーイメージの定義
//------------------------------------------------------------
var poheaderAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="見積原価書検索">';
var poheaderAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="ESTIMATE COST LIST SEARCH">';






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

		SegA15.innerHTML = 'Creation date';
		SegA01.innerText = 'Products code';
		SegA02.innerText = 'Products name(ja)';
		SegA18.innerText = 'Input person';
		SegA04.innerText = 'Dept';
		SegA05.innerText = 'In charge name';
		SegA03.innerText = 'Delivery date';

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

		SegA15.innerHTML = '作成日時';
		SegA01.innerText = '製品コード';
		SegA02.innerText = '製品名称(日本語)';
		SegA18.innerText = '入力者';
		SegA04.innerText = '部門';
		SegA05.innerText = '担当者';
		SegA03.innerText = '納期';


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