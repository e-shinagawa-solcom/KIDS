<!--


//-----------------------------------------------------------------------------
// 概要 : ローカル変数定義
// 解説 :「TabIndex」値の設定
//-----------------------------------------------------------------------------

//------------------------------------------
// 適用箇所 :「サブウィンドウボタン」
//------------------------------------------
var NumTabA1   = '' ; // vendor
var NumTabA1_2 = '' ; // creation
var NumTabA1_3 = '' ; // assembly
var NumTabB1   = '' ; // dept
var NumTabC1   = '' ; // products
var NumTabD1   = '' ; // location
var NumTabE1   = '' ; // applicant
var NumTabF1   = '' ; // wfinput
var NumTabG1   = '' ; // vi
var NumTabH1   = '' ; // supplier
var NumTabI1   = '' ; // input


//------------------------------------------
// 適用箇所 :「商品管理」タブ
//------------------------------------------
var PTabNumA = '' ; // A
var PTabNumB = '' ; // B


//------------------------------------------
// 適用箇所 :「受注・発注・売上・仕入」タブ
//------------------------------------------
var TabNumA = '' ; // ヘッダー
var TabNumB = '' ; // 明細


//------------------------------------------
// 適用箇所 :「登録ボタン」
//------------------------------------------
var RegistNum = '' ;


//------------------------------------------
// 適用箇所 :「行追加ボタン」
//------------------------------------------
var AddRowNum = '' ;


//------------------------------------------
// 適用箇所 :「カレンダーボタン」
//------------------------------------------
var NumDateTabA = '' ;
var NumDateTabB = '' ;
var NumDateTabC = '' ;


//------------------------------------------
// 適用箇所 :「製品数量ボタン」
//------------------------------------------
var NumPunitTab = '' ;






///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="見積原価管理">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="ESTIMATE COST CONTROL">';





///// INPUT A,B,C HEADER IMAGE /////
var headerAJ = '<img src="' + esheadtitleAJ + '" width="949" height="30" border="0" alt="見積原価検索">';
var headerAE = '<img src="' + esheadtitleAE + '" width="949" height="30" border="0" alt="ESTIMATE COST SEARCH">';



///// [SEARCH]SEARCH BT IMAGE /////
var schSchBtJ1 = '<a href="#"><img onmouseover="schSchJOn(this)" onmouseout="schSchJOff(this)" src="' + schSchJ1 + '" width="82" height="24" border="0" alt="検索"></a>';
var schSchBtE1 = '<a href="#"><img onmouseover="schSchEOn(this)" onmouseout="schSchEOff(this)" src="' + schSchE1 + '" width="82" height="24" border="0" alt="SEARCH"></a>';

///// [SEARCH]CLEAR BT IMAGE /////
var schClrBtJ1 = '<a href="#"><img onmouseover="schClrJOn(this)" onmouseout="schClrJOff(this)" src="' + schClrJ1 + '" width="82" height="24" border="0" alt="クリア"></a>';
var schClrBtE1 = '<a href="#"><img onmouseover="schClrEOn(this)" onmouseout="schClrEOff(this)" src="' + schClrE1 + '" width="82" height="24" border="0" alt="CLEAR"></a>';





///// CSS VALUE /////
var fcolor = '#666666'; //項目フォントカラー
var segcolor = '#e8f0f1'; //項目背景色
var segbody = '#d6d0b1'; //INPUT A BODY 背景色
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //項目右空きBORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //項目左空きBORDER
var brcolor03 = '#798787 #e8f0f1 #798787 #e8f0f1'; //項目左右空きBORDER



function initLayoutES()
{
	///// MAIN TITLE /////
	MainTitle.innerHTML = maintitleJ;

	///// INPUT A BODYS COLOR /////
	SegABodys.style.background = segbody;

	///// INPUT A,B,C HEADER TITLE IMAGE /////
	SegAHeader.innerHTML = headerAJ;

	///// INPUT A,B,C BOTTOM IMAGE /////
	SegABottom.innerHTML = bottom03;

	///// [SEARCH]SEARCH & CLEAR BUTTON /////
	if(typeof(schSchButton) != 'undefined' )
	{
		schSchButton.innerHTML = schSchBtJ1;
	}

	if(typeof(schClrButton) != 'undefined' )
	{
		schClrButton.innerHTML = schClrBtJ1;
	}

	return false;
}


//-->