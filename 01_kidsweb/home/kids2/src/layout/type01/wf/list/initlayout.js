<!--


///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="����ե�����">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="WORK FLOW">';


///// INPUT A,B,C HEADER IMAGE /////
var headerAJ = '<img src="' + wfheader01J + '" width="949" height="30" border="0" alt="�Ʒ����">';
var headerAE = '<img src="' + wfheader01E + '" width="949" height="30" border="0" alt="WORK FLOW LIST">';
//var headerBJ = '<img src="' + headtitleBJ + '" width="766" height="30" border="0" alt="����">';
//var headerBE = '<img src="' + headtitleBE + '" width="766" height="30" border="0" alt="DETAIL">';


///// CSS VALUE /////
var fcolor = '#666666'; //���ܥե���ȥ��顼
var segcolor = '#e8f0f1'; //�����طʿ�
var segbody = '#d6d0b1'; //INPUT A BODY �طʿ�
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //���ܱ�����BORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //���ܺ�����BORDER


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