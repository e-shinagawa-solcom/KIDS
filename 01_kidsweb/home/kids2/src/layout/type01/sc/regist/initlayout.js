<!--


//-----------------------------------------------------------------------------
// ���� : �������ѿ����
// ���� :��TabIndex���ͤ�����
//-----------------------------------------------------------------------------

//------------------------------------------
// Ŭ�Ѳս� :�֥��֥�����ɥ��ܥ����
//------------------------------------------
var NumTabA1   = '9'  ; // vendor
var NumTabA1_2 = ''   ; // creation
var NumTabA1_3 = ''   ; // assembly
var NumTabB1   = ''   ; // dept
var NumTabC1   = ''   ; // products
var NumTabD1   = ''   ; // location
var NumTabE1   = ''   ; // applicant
var NumTabF1   = ''   ; // wfinput
var NumTabG1   = ''   ; // vi
var NumTabH1   = ''   ; // supplier
var NumTabI1   = ''   ; // input


//------------------------------------------
// Ŭ�Ѳս� :�־��ʴ����ץ���
//------------------------------------------
var PTabNumA = '' ; // A
var PTabNumB = '' ; // B


//------------------------------------------
// Ŭ�Ѳս� :�ּ���ȯ����塦�����ץ���
//------------------------------------------
var TabNumA = '31' ; // �إå���
var TabNumB = '15' ; // ����


//------------------------------------------
// Ŭ�Ѳս� :����Ͽ�ܥ����
//------------------------------------------
var RegistNum = '30' ;


//------------------------------------------
// Ŭ�Ѳս� :�ֹ��ɲåܥ����
//------------------------------------------
var AddRowNum = '29' ;


//------------------------------------------
// Ŭ�Ѳս� :�֥��������ܥ����
//------------------------------------------
var NumDateTabA = '2'  ;
var NumDateTabB = ''   ;
var NumDateTabC = '21' ;


//------------------------------------------
// Ŭ�Ѳս� :�����ʿ��̥ܥ����
//------------------------------------------
var NumPunitTab = '26' ;






	//-------------------------------------------------------------------------
	// v2 tomita update
	//-------------------------------------------------------------------------
	// ���ٸܵ�ɽ���ܥ��󥤥᡼������
	var dinflag = 0;

	var din1 = '<a href="javascript:void(0);"><img onmouseover="fncChangeDINImage( this, 0 ); return false;" onmouseout="fncChangeDINImage( this, 1 ); return false;" src="' + d_in_off + '" width="27" height="109" border="0" tabindex=""></a>';

	var din3 = '<a href="javascript:void(0);"><img src="' + d_in_on + '" width="27" height="109" border="0" tabindex=""></a>';


	// �֥���ե����������ɥ��ץܥ��󥤥᡼������
	var darkgrayOpenBt1 = '<a href="javascript:void(0);"><img onfocus="fncDarkGrayOpenButton( \'on\' , this );" onblur="fncDarkGrayOpenButton( \'off\' , this );" onmouseover="fncDarkGrayOpenButton( \'on\' , this );" onmouseout="fncDarkGrayOpenButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + darkgrayopen1 + '" width="19" height="19" border="0" tabindex=""></a>';

	var darkgrayOpenBt3 = '<a href="javascript:void(0);"><img src="' + darkgrayopen3 + '" width="19" height="19" border="0" tabindex=""></a>';

	var darkgrayOpenBtNotActive = '<img src="' + darkgrayopen1 + '" width="19" height="19" border="0" tabindex="">';


	// ���ʰ������ƥܥ���
	var getPBtn = '<a href="#"><img onfocus="fncDataOpenButton( \'on\' , this );" onblur="fncDataOpenButton( \'off\' , this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncDataOpenButton( \'on\' , this );" onmouseout="fncDataOpenButton( \'off\' , this );fncAlphaOff( this );" src="' + dataopen1 + '" width="19" height="19" border="0" tabindex="6"></a>';


	// HTML�񤭴��������ٸܵ�ɽ��
	function fncSetDINBtn( obj )
	{
		if( !dinflag )
		{
			obj.innerHTML = din3;
			dinflag       = 1;
		}
		else
		{
			obj.innerHTML = din1;
			dinflag       = 0;
		}
	}

	// ���᡼���񤭴���
	function fncChangeDINImage( obj, type )
	{
		obj.src = ( type == 0 ) ? d_in_off_on : d_in_off ;
	}

	// ���ٸܵ�ɽ��
	function fncViewDIN( obj )
	{
		if( dinflag )
		{
			document.getElementById( obj ).style.visibility     = 'visible';
			document.all.lngProductUnitCode_gs.style.visibility = 'hidden';
			document.all.lngProductUnitCode_ps.style.visibility = 'hidden';
			document.all.lngTaxClassCode.style.visibility       = 'hidden';

			document.all.SegDept.style.visibility      = 'visible';
			document.all.SegIncharge.style.visibility  = 'visible';
			document.all.VarsDept.style.visibility     = 'visible';
			document.all.VarsIncharge.style.visibility = 'visible';
		}
		else
		{
			document.getElementById( obj ).style.visibility     = 'hidden';
			document.all.lngProductUnitCode_gs.style.visibility = 'visible';
			document.all.lngProductUnitCode_ps.style.visibility = 'visible';
			document.all.lngTaxClassCode.style.visibility       = 'visible';

			document.all.SegDept.style.visibility      = 'hidden';
			document.all.SegIncharge.style.visibility  = 'hidden';
			document.all.VarsDept.style.visibility     = 'hidden';
			document.all.VarsIncharge.style.visibility = 'hidden';
		}
	}

	// ���ٸܵ�ɽ�������
	function fncResetDINView( mode )
	{
		switch( mode )
		{
			case 0:
				document.all.SegDetailIN.style.visibility           = 'hidden';
				document.all.SegDetailINBtn.innerHTML               = din1 ;
				document.all.lngProductUnitCode_gs.style.visibility = 'hidden';
				document.all.lngProductUnitCode_ps.style.visibility = 'hidden';
				document.all.lngTaxClassCode.style.visibility       = 'hidden';

				document.all.SegDept.style.visibility      = 'hidden';
				document.all.SegIncharge.style.visibility  = 'hidden';
				document.all.VarsDept.style.visibility     = 'hidden';
				document.all.VarsIncharge.style.visibility = 'hidden';

				dinflag = 0;
				break;

			case 1:
				document.all.lngProductUnitCode_gs.style.visibility = 'visible';
				document.all.lngProductUnitCode_ps.style.visibility = 'visible';
				document.all.lngTaxClassCode.style.visibility       = 'visible';
				break;

			default:
				break;
		}
	}
	//-------------------------------------------------------------------------





//-----------------------------------------------------------------------------
// ���� : ���֥��᡼�����֥������Ȥ�����
//-----------------------------------------------------------------------------
var objtabA1 = '<a href="javascript:void(0);" onclick="ShowInputA();autoFocus1();"><img onfocus="TabAOn(this);" onblur="TabAOff(this);fncDefaultTabindex( document.all.strProductCode );" onmouseover="TabAOn(this);" onmouseout="TabAOff(this);" src="' + tabA1 + '" width="24" height="272" border="0" alt="HEADER" tabindex="' + TabNumA + '"></a>';
var objtabA3 = '<img src="' + tabA3 + '" width="24" height="272" border="0" alt="HEADER">';
var objtabB1 = '<a href="javascript:void(0);" onclick="ShowInputB();autoFocus2();window.DLwin.fncDtHtml();"><img onfocus="TabBOn(this);" onblur="TabBOff(this);fncDefaultTabindex( document.all.dtmOrderAppDate );" onmouseover="TabBOn(this);" onmouseout="TabBOff(this);" src="' + tabB1 + '" width="24" height="272" border="0" alt="DETAIL" tabindex="' + TabNumB + '"></a>';
var objtabB3 = '<img src="' + tabB3 + '" width="24" height="272" border="0" alt="DETAIL">';




//-----------------------------------------------------------------------------
// ���� : ����ǡ����ɤ߹��ߥܥ��󥪥֥������Ȥ�����
//-----------------------------------------------------------------------------
var dataopenbt1 = '<a href="#"><img onfocus="fncDataOpenButton( \'on\' , this );" onblur="fncDataOpenButton( \'off\' , this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncDataOpenButton( \'on\' , this );" onmouseout="fncDataOpenButton( \'off\' , this );fncAlphaOff( this );" src="' + dataopen1 + '" width="19" height="19" border="0" tabindex="4"></a>';





///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="������">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="SALES CONTROL">';


///// INPUT A,B,C HEADER IMAGE /////
var headerAJ = '<img src="' + headtitleAJ + '" width="927" height="30" border="0" alt="�إå���">';
var headerAE = '<img src="' + headtitleAE + '" width="927" height="30" border="0" alt="HEADER">';
var headerBJ = '<img src="' + headtitleBJ + '" width="927" height="30" border="0" alt="����">';
var headerBE = '<img src="' + headtitleBE + '" width="927" height="30" border="0" alt="DETAIL">';
var headerCJ = '<img src="' + headtitleCJ + '" width="927" height="30" border="0" alt="���ٰ���">';
var headerCE = '<img src="' + headtitleCE + '" width="927" height="30" border="0" alt="DETAIL LIST">';


///// CSS VALUE /////
var fcolor = '#666666'; //���ܥե���ȥ��顼
var segcolor = '#e8f0f1'; //�����طʿ�
var segbody = '#d6d0b1'; //INPUT A BODY �طʿ�
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //���ܱ�����BORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //���ܺ�����BORDER


function initLayoutSC()
{

	//// OBJECT JUDGE /////
	if (typeof(TabA)!='undefined')
	{
		TabA.innerHTML = objtabA3;
	}

	if (typeof(TabB)!='undefined')
	{
		TabB.innerHTML = objtabB1;
	}

	if( typeof(DataOpenBt) != 'undefined' )
	{
		DataOpenBt.innerHTML = dataopenbt1;
	}


	///// MAIN TITLE /////
	MainTitle.innerHTML = maintitleJ;

	///// INPUT A BODYS COLOR /////
	SegABodys.style.background = segbody;
	SegBBodys.style.background = segbody;
	SegCBodys.style.background = segbody;

	///// INPUT A,B,C HEADER TITLE IMAGE /////
	SegAHeader.innerHTML = headerAJ;
	SegBHeader.innerHTML = headerBJ;
	SegCHeader.innerHTML = headerCJ;

	///// INPUT A,B,C BOTTOM IMAGE /////
	SegABottom.innerHTML = bottom01;
	SegBBottom.innerHTML = bottom01;
	SegCBottom.innerHTML = bottom01;

	///// INPUT A FONT COLOR /////
	SegA01.style.color = fcolor;
	SegA02.style.color = fcolor;
	SegA03.style.color = fcolor;
	// *v1* SegA04.style.color = fcolor;
	// *v1* SegA05.style.color = fcolor;
	SegA06.style.color = fcolor;
	SegA07.style.color = fcolor;
	SegA08.style.color = fcolor;
	SegA09.style.color = fcolor;
	SegA10.style.color = fcolor;
	// *v1* SegA11.style.color = fcolor;
	SegA12.style.color = fcolor;

	///// INPUT A SEGMENT BG COLOR /////
	SegA01.style.background = segcolor;
	SegA02.style.background = segcolor;
	SegA03.style.background = segcolor;
	// *v1* SegA04.style.background = segcolor;
	// *v1* SegA0405.style.background = segcolor;
	// *v1* SegA05.style.background = segcolor;
	SegA06.style.background = segcolor;
	SegA07.style.background = segcolor;
	SegA08.style.background = segcolor;
	SegA09.style.background = segcolor;
	SegA10.style.background = segcolor;
	// *v1* SegA11.style.background = '#f1f1f1';
	SegA12.style.background = segcolor;

	///// INPUT A VARS BG COLOR /////
	VarsA01.style.background = segcolor;
	VarsA02.style.background = segcolor;
	VarsA03.style.background = segcolor;
	// *v1* VarsA04.style.background = segcolor;
	// *v1* VarsA05.style.background = segcolor;
	VarsA06.style.background = segcolor;
	VarsA07.style.background = segcolor;
	VarsA08.style.background = segcolor;
	VarsA09.style.background = segcolor;
	VarsA10.style.background = segcolor;
	// *v1* VarsA11.style.background = '#f1f1f1';
	VarsA12.style.background = segcolor;

	///// INPUT A SEGMENT BORDER COLOR /////
	SegA01.style.borderColor = brcolor01;
	SegA02.style.borderColor = brcolor01;
	SegA03.style.borderColor = brcolor01;
	// *v1* SegA04.style.borderColor = brcolor01;
	// *v1* SegA0405.style.borderColor = brcolor02;
	// *v1* SegA05.style.borderColor = brcolor01;
	SegA06.style.borderColor = brcolor01;
	SegA07.style.borderColor = brcolor01;
	SegA08.style.borderColor = brcolor01;
	SegA09.style.borderColor = brcolor01;
	SegA10.style.borderColor = brcolor01;
	// *v1* SegA11.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	SegA12.style.borderColor = brcolor01;

	///// INPUT A VARS BORDER COLOR /////
	VarsA01.style.borderColor = brcolor02;
	VarsA02.style.borderColor = brcolor02;
	VarsA03.style.borderColor = brcolor02;
	// *v1* VarsA04.style.borderColor = brcolor02;
	// *v1* VarsA05.style.borderColor = brcolor02;
	VarsA06.style.borderColor = brcolor02;
	VarsA07.style.borderColor = brcolor02;
	VarsA08.style.borderColor = brcolor02;
	VarsA09.style.borderColor = brcolor02;
	VarsA10.style.borderColor = brcolor02;
	//VarsA11.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	VarsA12.style.borderColor = brcolor02;




	///// INPUT B FONT COLOR /////
	SegB01.style.color = fcolor;
	SegB02.style.color = fcolor;
	SegB03.style.color = fcolor;
	SegB04_1.style.color = fcolor;
	SegB04_2.style.color = fcolor;
	SegB04_3.style.color = fcolor;
	SegB04_4.style.color = fcolor;
	SegB04_5.style.color = fcolor;
	SegB05_1.style.color = fcolor;
	SegB05_2.style.color = fcolor;
	SegB05_3.style.color = fcolor;
	SegB05_4.style.color = fcolor;
	SegB05_5.style.color = fcolor;
	SegB06.style.color = fcolor;
	SegB07.style.color = fcolor;
	SegB08.style.color = fcolor;
	SegB13.style.color = fcolor;
	SegB14.style.color = fcolor;
	SegB15.style.color = fcolor;
	SegB16.style.color = fcolor;

	///// INPUT B SEGMENT BG COLOR /////
	SegB01.style.background = segcolor;
	SegB02.style.background = segcolor;
	SegB03.style.background = segcolor;
	SegB04_2.style.background = segcolor;
	SegB04_3.style.background = segcolor;
	SegB04_4.style.background = segcolor;
	SegB04_5.style.background = segcolor;
	SegB05_2.style.background = segcolor;
	SegB05_3.style.background = segcolor;
	SegB05_4.style.background = segcolor;
	SegB05_5.style.background = segcolor;
	SegB06.style.background = segcolor;
	SegB07.style.background = segcolor;
	SegB08.style.background = segcolor;
	SegB13.style.background = '#f1f1f1';
	SegB14.style.background = '#f1f1f1';
	SegB15.style.background = '#f1f1f1';
	SegB16.style.background = '#f1f1f1';

	///// INPUT B VARS BG COLOR /////
	VarsB01.style.background = segcolor;
	VarsB02.style.background = segcolor;
	VarsB03.style.background = segcolor;
	VarsB04_1.style.background = segcolor;
	VarsB04_2.style.background = segcolor;
	VarsB04_3.style.background = segcolor;
	VarsB04_4.style.background = segcolor;
	VarsB04_5.style.background = segcolor;
	VarsB05_1.style.background = segcolor;
	VarsB05_2.style.background = segcolor;
	VarsB05_3.style.background = segcolor;
	VarsB05_4.style.background = segcolor;
	VarsB05_5.style.background = segcolor;
	VarsB06.style.background = segcolor;
	VarsB07.style.background = segcolor;
	VarsB08.style.background = segcolor;
	VarsB13.style.background = '#f1f1f1';
	VarsB14.style.background = '#f1f1f1';
	VarsB15.style.background = '#f1f1f1';
	VarsB16.style.background = '#f1f1f1';

	///// INPUT B SEGMENT BORDER COLOR /////
	SegB01.style.borderColor = brcolor01;
	SegB02.style.borderColor = brcolor01;
	SegB03.style.borderColor = brcolor01;
	SegB04_2.style.borderColor = brcolor01;
	SegB04_3.style.borderColor = brcolor01;
	SegB04_4.style.borderColor = brcolor01;
	SegB04_5.style.borderColor = brcolor01;
	SegB05_2.style.borderColor = brcolor01;
	SegB05_3.style.borderColor = brcolor01;
	SegB05_4.style.borderColor = brcolor01;
	SegB05_5.style.borderColor = brcolor01;
	SegB06.style.borderColor = brcolor01;
	SegB07.style.borderColor = brcolor01;
	SegB08.style.borderColor = brcolor01;
	SegB13.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	SegB14.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	SegB15.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	SegB16.style.borderColor = '#798787 #f1f1f1 #798787 #798787';

	///// INPUT B VARS BORDER COLOR /////
	VarsB01.style.borderColor = brcolor02;
	VarsB02.style.borderColor = brcolor02;
	VarsB03.style.borderColor = brcolor02;
	VarsB04_1.style.borderColor = brcolor01;
	VarsB04_2.style.borderColor = brcolor02;
	VarsB04_3.style.borderColor = brcolor02;
	VarsB04_4.style.borderColor = brcolor02;
	VarsB04_5.style.borderColor = brcolor02;
	VarsB05_1.style.borderColor = brcolor02;
	VarsB05_2.style.borderColor = brcolor02;
	VarsB05_3.style.borderColor = brcolor02;
	VarsB05_4.style.borderColor = brcolor02;
	VarsB05_5.style.borderColor = brcolor02;
	VarsB06.style.borderColor = brcolor02;
	VarsB07.style.borderColor = brcolor02;
	VarsB08.style.borderColor = brcolor02;
	VarsB13.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	VarsB14.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	VarsB15.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	VarsB16.style.borderColor = '#798787 #798787 #798787 #f1f1f1';



	//-------------------------------------------------------------------------
	// v2 tomita update
	//-------------------------------------------------------------------------
	SegCRC.style.color        = fcolor;
	SegCRC.style.background   = segcolor;
	SegCRC.style.borderColor  = brcolor01;
	VarsCRC.style.background  = segcolor;
	VarsCRC.style.borderColor = brcolor02;

	SegPC.style.color        = fcolor;
	SegPC.style.background   = '#f1f1f1'; /* segcolor */
	SegPC.style.borderColor  = brcolor01;
	VarsPC.style.background  = '#f1f1f1'; /* segcolor */
	VarsPC.style.borderColor = brcolor02;

	/*
	SegRC.style.color         = fcolor;
	SegRC.style.background    = '#f1f1f1';
	SegRC.style.borderColor   = '#798787 #f1f1f1 #798787 #798787';
	VarsRC.style.background   = '#f1f1f1';
	VarsRC.style.borderColor  = '#798787 #798787 #798787 #f1f1f1';
	*/

	SegDept.style.color            = fcolor;
	SegDept.style.background       = '#f1f1f1';
	SegDept.style.borderColor      = '#798787 #f1f1f1 #798787 #798787';
	SegIncharge.style.color        = fcolor;
	SegIncharge.style.background   = '#f1f1f1';
	SegIncharge.style.borderColor  = '#798787 #f1f1f1 #798787 #798787';
	VarsDept.style.background      = '#f1f1f1';
	VarsDept.style.borderColor     = '#798787 #798787 #798787 #f1f1f1';
	VarsIncharge.style.background  = '#f1f1f1';
	VarsIncharge.style.borderColor = '#798787 #798787 #798787 #f1f1f1';

	SegDetailINBtn.innerHTML  = din1;

	SegBWF.style.color        = '#ffffff';
	SegBWF.style.borderColor  = '#cdcdcd #72828b #cdcdcd #cdcdcd';
	SegBWF.style.background   = 'transparent';
	VarsBWF.style.borderColor = '#cdcdcd #cdcdcd #cdcdcd #72828b';
	VarsBWF.style.background  = 'transparent';


	if( typeof(PopenBt) != 'undefined' )
	{
		PopenBt.innerHTML = getPBtn;
	}

	var obj = document.DSO.lngWorkflowOrderCode;

	// �־�ǧ�ʤ��פξ���Ƚ�� -> �־�ǧ�ʤ��פξ��ϥܥ��󲡲��ػ�
	for( i=0; i < obj.options.length; i++ )
	{
		if( obj.options[i].text == '��ǧ�ʤ�' )
		{
			if( i == 0 )
			{
				WFrootBt.innerHTML = darkgrayOpenBtNotActive;
				fncAlphaOn( document.all.WFrootBt );
			}
		}
		else
		{
			WFrootBt.innerHTML = darkgrayOpenBt1;
		}
	}

	// Debug
	//WFrootBt.innerHTML = darkgrayOpenBtNotActive;
	//fncAlphaOn( document.all.WFrootBt );
	//-------------------------------------------------------------------------



	return false;
}


//-------------------------------------------------------------------
// ���� : ������ñ�̷׾�סֲٻ�ñ�̷׾�פǤΥե���������ư�����ؿ�
//-------------------------------------------------------------------
function fncForceFocus( obj )
{
	// ���֥������Ȥ�ͭ�������ǧ
	if( typeof(obj) == "undefined" || obj.disabled == true )
	{
		return false;
	}
	obj.focus();
}


//-->