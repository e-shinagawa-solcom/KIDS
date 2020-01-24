<!--


//// [P] MSW BUTTON TAB INDEX NUM /////
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

var AddRowNum = '';

//// [P] DATE BUTTON TAB INDEX NUM /////
var NumDateTabA = '';
var NumDateTabB = '';


var TabNumA = '';
var TabNumB = '';



///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="帳票出力">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="LIST OUTPUT">';




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



function initLayout()
{
	///// MAIN TITLE /////
	MainTitle.innerHTML = maintitleJ;

	///// INPUT A BODYS COLOR /////
	//SegABodys.style.background = segbody;

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