


//-----------------------------------------------------------------------------
// ���� : ���������ѿ����
// ���� :��TabIndex���ͤ�����
//-----------------------------------------------------------------------------

//------------------------------------------
// Ŭ�Ѳս� :�֥��֥�����ɥ��ܥ����
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
// Ŭ�Ѳս� :�־��ʴ����ץ���
//------------------------------------------
var PTabNumA = '' ; // A
var PTabNumB = '' ; // B


//------------------------------------------
// Ŭ�Ѳս� :�ּ�����ȯ������塦�����ץ���
//------------------------------------------
var TabNumA = '' ; // �إå���
var TabNumB = '' ; // ����


//------------------------------------------
// Ŭ�Ѳս� :����Ͽ�ܥ����
//------------------------------------------
var RegistNum = '' ;


//------------------------------------------
// Ŭ�Ѳս� :�ֹ��ɲåܥ����
//------------------------------------------
var AddRowNum = '' ;


//------------------------------------------
// Ŭ�Ѳս� :�֥��������ܥ����
//------------------------------------------
var NumDateTabA = '' ;
var NumDateTabB = '' ;
var NumDateTabC = '' ;


//------------------------------------------
// Ŭ�Ѳս� :�����ʿ��̥ܥ����
//------------------------------------------
var NumPunitTab = '' ;






///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="������">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="SALES CONTROL">';



///// INPUT A,B,C HEADER IMAGE /////
var headerAJ = '<img src="' + sosheadtitleAJ + '" width="949" height="30" border="0" alt="��帡��">';
var headerAE = '<img src="' + sosheadtitleAE + '" width="949" height="30" border="0" alt="SC SEARCH">';



///// [SEARCH]SEARCH BT IMAGE /////
var schSchBtJ1 = '<a href="#"><img onmouseover="schSchJOn(this)" onmouseout="schSchJOff(this)" src="' + schSchJ1 + '" width="82" height="24" border="0" alt="����"></a>';
var schSchBtE1 = '<a href="#"><img onmouseover="schSchEOn(this)" onmouseout="schSchEOff(this)" src="' + schSchE1 + '" width="82" height="24" border="0" alt="SEARCH"></a>';

///// [SEARCH]CLEAR BT IMAGE /////
var schClrBtJ1 = '<a href="#"><img onmouseover="schClrJOn(this)" onmouseout="schClrJOff(this)" src="' + schClrJ1 + '" width="82" height="24" border="0" alt="���ꥢ"></a>';
var schClrBtE1 = '<a href="#"><img onmouseover="schClrEOn(this)" onmouseout="schClrEOff(this)" src="' + schClrE1 + '" width="82" height="24" border="0" alt="CLEAR"></a>';





///// CSS VALUE /////
var fcolor = '#666666'; //���ܥե���ȥ��顼
var segcolor = '#e8f0f1'; //�����طʿ�
var segbody = '#d6d0b1'; //INPUT A BODY �طʿ�
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //���ܱ�����BORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //���ܺ�����BORDER
var brcolor03 = '#798787 #e8f0f1 #798787 #e8f0f1'; //���ܺ�������BORDER



function initLayoutSC()
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
	schSchButton.innerHTML = schSchBtJ1;
	schClrButton.innerHTML = schClrBtJ1;

	return false;
}


//-->