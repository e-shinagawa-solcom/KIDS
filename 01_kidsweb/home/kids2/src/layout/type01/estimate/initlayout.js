


var menuBackImg = '<img src="' + menuback1 + '" width="313" height="27">';


///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="見積原価管理">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="ESTIMATE COST CONTROL">';


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

	if( typeof(MenuBack) != "undefined" ) MenuBack.innerHTML  = menuBackImg;

	return false;
}


//-->