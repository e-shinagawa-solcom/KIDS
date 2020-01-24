<!--


//// [WF] MSW BUTTON TAB INDEX NUM /////
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

// 登録ボタン
var RegistNum = '';

//// [WF] DATE BUTTON TAB INDEX NUM /////
var NumDateTabA = '';
var NumDateTabB = '';



///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="商品管理">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="PRODUCTS">';


///// INPUT A,B,C HEADER IMAGE /////
var headerAJ = '<img src="' + confirmHeadJ + '" width="949" height="30" border="0" alt="登録確認">';
var headerAE = '<img src="' + confirmHeadE + '" width="949" height="30" border="0" alt="REGISTRAITION CONFIRM">';



///// [SEARCH]REGIST BLUE BT IMAGE /////
var schSchBtJ1 = '<a href="#" onclick=""><img onmouseover="BlueRegistJOn(this)" onmouseout="BlueRegistJOff(this)" src="' + blueregistJ1 + '" width="72" height="20" border="0" alt="登録"></a>';
var schSchBtE1 = '<a href="#" onclick=""><img onmouseover="BlueRegistEOn(this)" onmouseout="BlueRegistEOff(this)" src="' + blueregistE1 + '" width="72" height="20" border="0" alt="REGIST"></a>';

///// [SEARCH]BACK BLUE BT IMAGE /////
var schClrBtJ1 = '<a href="#"><img onmouseover="BlueBackJOn(this);" onmouseout="BlueBackJOff(this);" src="' + bluebackJ1 + '" width="72" height="20" border="0" alt="戻る"></a>';
var schClrBtE1 = '<a href="#"><img onmouseover="BlueBackEOn(this);" onmouseout="BlueBackEOff(this);" src="' + bluebackE1 + '" width="72" height="20" border="0" alt="BACK"></a>';


///// CSS VALUE /////
var fcolor = '#666666'; //項目フォントカラー
var segcolor = '#e8f0f1'; //項目背景色
var segbody = '#d6d0b1'; //INPUT A BODY 背景色
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //項目右空きBORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //項目左空きBORDER


function initLayoutP()
{
	///// MAIN TITLE /////
	MainTitle.innerHTML = maintitleJ;

	///// INPUT A BODYS COLOR /////
	SegABodys.style.background = segbody;

	///// INPUT A,B,C HEADER TITLE IMAGE /////
	//SegAHeader.innerHTML = headerAJ;

	///// INPUT A,B,C BOTTOM IMAGE /////
	SegABottom.innerHTML = bottom03;

	///// [SEARCH]SEARCH & CLEAR BUTTON /////
	schSchButton.innerHTML = schSchBtJ1;
	schClrButton.innerHTML = schClrBtJ1;

	return false;
}







//-->