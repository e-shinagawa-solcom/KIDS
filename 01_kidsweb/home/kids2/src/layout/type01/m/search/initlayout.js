<!--


//// [M] MSW BUTTON TAB INDEX NUM /////
var NumTabA1 = '';
var NumTabA1_2 = '';
var NumTabA1_3 = '';
var NumTabB1 = '';
var NumTabC1 = '';
var NumTabD1 = '';
var NumTabE1 = '';
var NumTabF1 = '';
var NumTabG1 = '';
var NumTabH1 = '';
var NumTabI1 = '';

var TabNumA = '';
var TabNumB = '';


//// [M] DATE BUTTON TAB INDEX NUM /////
var NumDateTabA = '';
var NumDateTabB = '';


// 登録ボタン
var RegistNum = '';

var AddRowNum = '';


///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="マスタ管理">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="MASTER">';


///// INPUT A,B,C HEADER IMAGE /////
//var headerAJ = '<img src="' + search01J + '" width="949" height="30" border="0" alt="マスタ検索">';
//var headerAE = '<img src="' + search01E + '" width="949" height="30" border="0" alt="MASTER SEARCH">';


///// [SEARCH]SEARCH BT IMAGE /////
var schSchBtJ1 = '<a href="#" onclick=""><img onmouseover="schSchJOn(this)" onmouseout="schSchJOff(this)" src="' + schSchJ1 + '" width="82" height="24" border="0" alt="検索"></a>';
var schSchBtE1 = '<a href="#" onclick=""><img onmouseover="schSchEOn(this)" onmouseout="schSchEOff(this)" src="' + schSchE1 + '" width="82" height="24" border="0" alt="SEARCH"></a>';

///// [SEARCH]CLEAR BT IMAGE /////
var schClrBtJ1 = '<a href="#"><img onmouseover="schClrJOn(this)" onmouseout="schClrJOff(this)" src="' + schClrJ1 + '" width="82" height="24" border="0" alt="クリア"></a>';
var schClrBtE1 = '<a href="#"><img onmouseover="schClrEOn(this)" onmouseout="schClrEOff(this)" src="' + schClrE1 + '" width="82" height="24" border="0" alt="CLEAR"></a>';


///// CSS VALUE /////
var fcolor = '#666666'; //項目フォントカラー
var segcolor = '#e8f0f1'; //項目背景色
var segbody = '#d6d0b1'; //INPUT A BODY 背景色
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //項目右空きBORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //項目左空きBORDER


function initLayoutM()
{
	///// MAIN TITLE /////
	MainTitle.innerHTML = maintitleJ;

	///// INPUT A BODYS COLOR /////
	//SegABodys.style.background = segbody;

	///// INPUT A,B,C HEADER TITLE IMAGE /////
	//SegAHeader.innerHTML = headerAJ;

	///// INPUT A,B,C BOTTOM IMAGE /////
	SegABottom.innerHTML = bottom03;

	///// [SEARCH]SEARCH & CLEAR BUTTON /////

	//schSchButton.innerHTML = schSchBtJ1;
	
	//schClrButton.innerHTML = schClrBtJ1;

	return false;
}







//-->