<!--


///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="ワークフロー管理">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="WORK FLOW">';


///// INPUT A,B,C HEADER IMAGE /////
var headerAJ = '<img src="' + wfheader01J + '" width="949" height="30" border="0" alt="案件一覧">';
var headerAE = '<img src="' + wfheader01E + '" width="949" height="30" border="0" alt="WORK FLOW LIST">';
//var headerBJ = '<img src="' + headtitleBJ + '" width="766" height="30" border="0" alt="明細">';
//var headerBE = '<img src="' + headtitleBE + '" width="766" height="30" border="0" alt="DETAIL">';


///// CSS VALUE /////
var fcolor = '#666666'; //項目フォントカラー
var segcolor = '#e8f0f1'; //項目背景色
var segbody = '#d6d0b1'; //INPUT A BODY 背景色
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //項目右空きBORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //項目左空きBORDER


function initLayoutWF()
{
	///// MAIN TITLE /////
	MainTitle.innerHTML = maintitleJ;

	///// INPUT A BODYS COLOR /////
	SegABodys.style.background = segbody;
	//SegBBodys.style.background = segbody;
	//SegCBodys.style.background = segbody;

	///// INPUT A,B,C HEADER TITLE IMAGE /////
	SegAHeader.innerHTML = headerAJ;
	//SegBHeader.innerHTML = headerBJ;
	//SegCHeader.innerHTML = headerCJ;

	///// INPUT A,B,C BOTTOM IMAGE /////
	SegABottom.innerHTML = bottom03;
	//SegBBottom.innerHTML = bottom01;
	//SegCBottom.innerHTML = bottom01;

	return false;
}







//-->