<!--


//-----------------------------------------------------------------------------
// ���� : �������ѿ����
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
// Ŭ�Ѳս� :�ּ���ȯ����塦�����ץ���
//------------------------------------------
var TabNumA = '' ; // �إå���
var TabNumB = '' ; // ����


//------------------------------------------
// Ŭ�Ѳս� :����Ͽ�ܥ����
//------------------------------------------
var RegistNum = '15' ;


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
var NumPunitTab = '11' ;





///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="�桼��������">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="USER CONTROL">';


///// INPUT A,B,C HEADER IMAGE /////
var headerAJ = '<img src="' + pheadtitleAJ + '" width="949" height="30" border="0" alt="�桼������Ͽ">';
var headerAE = '<img src="' + pheadtitleAE + '" width="949" height="30" border="0" alt="USER REGISTRATION">';



///// MAIN MTITLE IMAGE /////
var pictspace = '<img src="' + pictimg + '" width="70" height=70" border="0">';




///// CSS VALUE /////
var fcolor = '#666666'; //���ܥե���ȥ��顼
var segcolor = '#e8f0f1'; //�����طʿ�
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //���ܱ�����BORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //���ܺ�����BORDER
var brcolor03 = '#798787 #e8f0f1 #798787 #e8f0f1'; //���ܺ�������BORDER

var brcolorF1 = '#798787 #f1f1f1 #798787 #798787'; //���ܱ�����BORDER
var brcolorF2 = '#798787 #798787 #798787 #f1f1f1'; //���ܺ�����BORDER


function initLayoutUC()
{

	///// MAIN TITLE /////
	MainTitle.innerHTML = maintitleJ;


	///// INPUT A,B,C HEADER TITLE IMAGE /////
	SegAHeader.innerHTML = headerAJ;


	///// INPUT A,B,C BOTTOM IMAGE /////
	SegABottom.innerHTML = bottom03;


	///// PICTURE SPACE IMAGE /////
	if( typeof(PictSpace) != 'undefined' )
	{
		PictSpace.innerHTML = pictspace;
	}

	if( typeof(DisplayPictUploadSegs) != 'undefined' && typeof(DisplayPictUploadVars) != 'undefined' )
	{
		DisplayPictUploadSegs.style.color = fcolor;
		DisplayPictUploadSegs.style.background = '#f1f1f1';
		DisplayPictUploadSegs.style.borderColor = brcolorF1;
		DisplayPictUploadVars.style.background = '#f1f1f1';
		DisplayPictUploadVars.style.borderColor = brcolorF2;

		PixelImg.innerHTML = '<img src="/img/type01/uc/pixel.gif" width="82" height="23" border="0">';
	}

	return false;
}


//-->