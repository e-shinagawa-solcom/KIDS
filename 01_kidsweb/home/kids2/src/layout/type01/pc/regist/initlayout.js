<!--


//-----------------------------------------------------------------------------
// 概要 : ローカル変数定義
// 解説 :「TabIndex」値の設定
//-----------------------------------------------------------------------------

//------------------------------------------
// 適用箇所 :「サブウィンドウボタン」
//------------------------------------------
var NumTabA1   = ''   ; // vendor
var NumTabA1_2 = ''   ; // creation
var NumTabA1_3 = ''   ; // assembly
var NumTabB1   = '10' ; // dept
var NumTabC1   = '22' ; // products
var NumTabD1   = '12' ; // location
var NumTabE1   = ''   ; // applicant
var NumTabF1   = ''   ; // wfinput
var NumTabG1   = ''   ; // vi
var NumTabH1   = '7'  ; // supplier
var NumTabI1   = ''   ; // input


//------------------------------------------
// 適用箇所 :「商品管理」タブ
//------------------------------------------
var PTabNumA = '' ; // A
var PTabNumB = '' ; // B


//------------------------------------------
// 適用箇所 :「受注・発注・売上・仕入」タブ
//------------------------------------------
var TabNumA = '38' ; // ヘッダー
var TabNumB = '20' ; // 明細


//------------------------------------------
// 適用箇所 :「登録ボタン」
//------------------------------------------
var RegistNum = '37' ;


//------------------------------------------
// 適用箇所 :「行追加ボタン」
//------------------------------------------
var AddRowNum = '36' ;


//------------------------------------------
// 適用箇所 :「カレンダーボタン」
//------------------------------------------
var NumDateTabA = '2'  ;
var NumDateTabB = '18' ;
var NumDateTabC = '28' ;


//------------------------------------------
// 適用箇所 :「製品数量ボタン」
//------------------------------------------
var NumPunitTab = '33' ;






	//-------------------------------------------------------------------------
	// v2 tomita update
	//-------------------------------------------------------------------------
	// 明細顧客表示ボタンイメージ生成
	var dinflag = 0;

	var din1 = '<a href="javascript:void(0);"><img onmouseover="fncChangeDINImage( this, 0 ); return false;" onmouseout="fncChangeDINImage( this, 1 ); return false;" src="' + d_in_off + '" width="27" height="109" border="0" tabindex=""></a>';

	var din3 = '<a href="javascript:void(0);"><img src="' + d_in_on + '" width="27" height="109" border="0" tabindex=""></a>';


	// 「ワークフロー順序ウィンドウ」ボタンイメージ生成
	var darkgrayOpenBt1 = '<a href="javascript:void(0);"><img onfocus="fncDarkGrayOpenButton( \'on\' , this );" onblur="fncDarkGrayOpenButton( \'off\' , this );" onmouseover="fncDarkGrayOpenButton( \'on\' , this );" onmouseout="fncDarkGrayOpenButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + darkgrayopen1 + '" width="19" height="19" border="0" tabindex=""></a>';

	var darkgrayOpenBt3 = '<a href="javascript:void(0);"><img src="' + darkgrayopen3 + '" width="19" height="19" border="0" tabindex=""></a>';

	var darkgrayOpenBtNotActive = '<img src="' + darkgrayopen1 + '" width="19" height="19" border="0" tabindex="">';


	// HTML書き換え・明細顧客表示
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

	// イメージ書き換え
	function fncChangeDINImage( obj, type )
	{
		obj.src = ( type == 0 ) ? d_in_off_on : d_in_off ;
	}

	// 明細顧客表示
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

	// 明細顧客表示初期化
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





///// TAB IMAGE /////
var objtabA1 = '<a href="javascript:void(0);" onclick="ShowInputA();autoFocus1();"><img onfocus="TabAOn(this);" onblur="TabAOff(this);fncDefaultTabindex( document.all.strProductCode );" onmouseover="TabAOn(this);" onmouseout="TabAOff(this);" src="' + tabA1 + '" width="24" height="272" border="0" alt="HEADER" tabindex="' + TabNumA + '"></a>';
var objtabA3 = '<img src="' + tabA3 + '" width="24" height="272" border="0" alt="HEADER">';
var objtabB1 = '<a href="javascript:void(0);" onclick="ShowInputB();autoFocus2();window.DLwin.fncDtHtml();"><img onfocus="TabBOn(this);" onblur="TabBOff(this);fncDefaultTabindex( document.all.dtmOrderAppDate );" onmouseover="TabBOn(this);" onmouseout="TabBOff(this);" src="' + tabB1 + '" width="24" height="272" border="0" alt="DETAIL" tabindex="' + TabNumB + '"></a>';
var objtabB3 = '<img src="' + tabB3 + '" width="24" height="272" border="0" alt="DETAIL">';




	///// DATA OPEN BUTTON /////
var dataopenbt1 = '<a href="#"><img onfocus="fncDataOpenButton( \'on\' , this );" onblur="fncDataOpenButton( \'off\' , this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncDataOpenButton( \'on\' , this );" onmouseout="fncDataOpenButton( \'off\' , this );fncAlphaOff( this );" src="' + dataopen1 + '" width="19" height="19" border="0" tabindex="4"></a>';





	///// TAX WIN BUTTON /////
var showTaxbt1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="GPOn(this);" onmouseout="GPOff(this);fncAlphaOff( this );" src="' + showwin1 + '" width="19" height="19" border="0" alt="DETAIL"></a>';
var showTaxbt3 = '<a href="#"><img src="' + showwin3 + '" width="19" height="19" border="0" alt="DETAIL"></a>';


	///// TAX WIN HEADER /////
var taxheader = '<img src="' + taxhead + '" widht="306" height="12" border="0">';

	///// TAX WIN BOTTOM /////
var taxbottoms = '<img src="' + taxbottom + '" widht="306" height="12" border="0">';


///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="仕入管理">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="SALES ORDER">';


///// INPUT A,B,C HEADER IMAGE /////
var headerAJ = '<img src="' + headtitleAJ + '" width="927" height="30" border="0" alt="ヘッダー">';
var headerAE = '<img src="' + headtitleAE + '" width="927" height="30" border="0" alt="HEADER">';
var headerBJ = '<img src="' + headtitleBJ + '" width="927" height="30" border="0" alt="明細">';
var headerBE = '<img src="' + headtitleBE + '" width="927" height="30" border="0" alt="DETAIL">';
var headerCJ = '<img src="' + headtitleCJ + '" width="927" height="30" border="0" alt="明細一覧">';
var headerCE = '<img src="' + headtitleCE + '" width="927" height="30" border="0" alt="DETAIL LIST">';


///// CSS VALUE /////
var fcolor = '#666666'; //項目フォントカラー
var segcolor = '#e8f0f1'; //項目背景色
var segbody = '#d6d0b1'; //INPUT A BODY 背景色
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //項目右空きBORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //項目左空きBORDER


function initLayoutPC()
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

	///// TAX BUTTON /////
	TaxBt.innerHTML = showTaxbt1;

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
	SegA11.style.color = fcolor;
	SegA12.style.color = fcolor;
	SegA13.style.color = fcolor;
	SegA14.style.color = fcolor;
	SegA15.style.color = fcolor;

	///// INPUT A SEGMENT BG COLOR /////
	SegA01.style.background = segcolor;
	SegA02.style.background = '#f1f1f1';
	SegA03.style.background = segcolor;
	// *v1* SegA04.style.background = segcolor;
	// *v1* SegA0405.style.background = segcolor;
	// *v1* SegA05.style.background = segcolor;
	SegA06.style.background = segcolor;
	SegA07.style.background = segcolor;
	SegA08.style.background = segcolor;
	SegA09.style.background = segcolor;
	SegA10.style.background = segcolor;
	SegA11.style.background = segcolor;
	SegA12.style.background = segcolor;
	SegA13.style.background = segcolor;
	SegA14.style.background = segcolor;
	SegA15.style.background = segcolor;

	///// INPUT A VARS BG COLOR /////
	VarsA01.style.background = segcolor;
	VarsA02.style.background = '#f1f1f1';
	VarsA03.style.background = segcolor;
	// *v1* VarsA04.style.background = segcolor;
	// *v1* VarsA05.style.background = segcolor;
	VarsA06.style.background = segcolor;
	VarsA07.style.background = segcolor;
	VarsA08.style.background = segcolor;
	VarsA09.style.background = segcolor;
	VarsA10.style.background = segcolor;
	VarsA11.style.background = segcolor;
	VarsA12.style.background = segcolor;
	VarsA13.style.background = segcolor;
	VarsA14.style.background = segcolor;
	VarsA15.style.background = segcolor;

	///// INPUT A SEGMENT BORDER COLOR /////
	SegA01.style.borderColor = brcolor01;
	SegA02.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	SegA03.style.borderColor = brcolor01;
	// *v1* SegA04.style.borderColor = brcolor01;
	// *v1* SegA0405.style.borderColor = brcolor02;
	// *v1* SegA05.style.borderColor = brcolor01;
	SegA06.style.borderColor = brcolor01;
	SegA07.style.borderColor = brcolor01;
	SegA08.style.borderColor = brcolor01;
	SegA09.style.borderColor = brcolor01;
	SegA10.style.borderColor = brcolor01;
	SegA11.style.borderColor = brcolor01;
	SegA12.style.borderColor = brcolor01;
	SegA13.style.borderColor = brcolor01;
	SegA14.style.borderColor = brcolor01;
	SegA15.style.borderColor = brcolor01;

	///// INPUT A VARS BORDER COLOR /////
	VarsA01.style.borderColor = brcolor02;
	VarsA02.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	VarsA03.style.borderColor = brcolor02;
	// *v1* VarsA04.style.borderColor = brcolor02;
	// *v1* VarsA05.style.borderColor = brcolor02;
	VarsA06.style.borderColor = brcolor02;
	VarsA07.style.borderColor = brcolor02;
	VarsA08.style.borderColor = brcolor02;
	VarsA09.style.borderColor = brcolor02;
	VarsA10.style.borderColor = brcolor02;
	VarsA11.style.borderColor = brcolor02;
	VarsA12.style.borderColor = brcolor02;
	VarsA13.style.borderColor = brcolor02;
	VarsA14.style.borderColor = brcolor02;
	VarsA15.style.borderColor = brcolor02;




	///// INPUT B FONT COLOR /////
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
	SegB09.style.color = fcolor;
	SegB10.style.color = fcolor;
	SegB12.style.color = fcolor;
	SegB13.style.color = fcolor;
	SegB14.style.color = fcolor;
	SegB15.style.color = fcolor;
	SegB20.style.color = fcolor;
	SegB21.style.color = fcolor;

	///// INPUT B SEGMENT BG COLOR /////
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
	SegB09.style.background = segcolor;
	SegB10.style.background = segcolor;
	SegB12.style.background = '#f1f1f1';
	SegB13.style.background = '#f1f1f1';
	SegB14.style.background = '#f1f1f1';
	SegB15.style.background = '#f1f1f1';
	SegB16.style.background = '#f1f1f1';
	SegB20.style.background = segcolor;
	SegB21.style.background = segcolor;

	///// INPUT B VARS BG COLOR /////
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
	VarsB09.style.background = segcolor;
	VarsB10.style.background = segcolor;
	VarsB12.style.background = '#f1f1f1';
	VarsB13.style.background = '#f1f1f1';
	VarsB14.style.background = '#f1f1f1';
	VarsB15.style.background = '#f1f1f1';
	VarsB16.style.background = '#f1f1f1';
	VarsB20.style.background = segcolor;
	VarsB21.style.background = segcolor;

	///// INPUT B SEGMENT BORDER COLOR /////
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
	SegB09.style.borderColor = brcolor01;
	SegB10.style.borderColor = brcolor01;
	SegB12.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	SegB13.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	SegB14.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	SegB15.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	SegB16.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	SegB20.style.borderColor = brcolor01;
	SegB21.style.borderColor = brcolor01;

	///// INPUT B VARS BORDER COLOR /////
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
	VarsB09.style.borderColor = brcolor02;
	VarsB10.style.borderColor = brcolor02;
	VarsB12.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	VarsB13.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	VarsB14.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	VarsB15.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	VarsB16.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	VarsB20.style.borderColor = brcolor02;
	VarsB21.style.borderColor = brcolor02;

	///// TAX WINDOW /////
	TaxHeader.innerHTML = taxheader;
	TaxBottom.innerHTML = taxbottoms;

	Tax.style.color = fcolor;
	TotalStdAmt.style.color = fcolor;

	Tax.style.background = '#f1f1f1';
	TotalStdAmt.style.background = '#f1f1f1';
;
	TaxVars.style.background = '#f1f1f1';
	TotalStdAmtVars.style.background = '#f1f1f1';

	Tax.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	TotalStdAmt.style.borderColor = '#798787 #f1f1f1 #798787 #798787';

	TaxVars.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	TotalStdAmtVars.style.borderColor = '#798787 #798787 #798787 #f1f1f1';



	//-------------------------------------------------------------------------
	// v2 tomita update
	//-------------------------------------------------------------------------
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


	var obj = document.DSO.lngWorkflowOrderCode;

	// 「承認なし」の場合の判定 -> 「承認なし」の場合はボタン押下禁止
	for( i=0; i < obj.options.length; i++ )
	{
		if( obj.options[i].text == '承認なし' )
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
// 解説 : 「製品単位計上」「荷姿単位計上」でのフォーカス移動処理関数
//-------------------------------------------------------------------
function fncForceFocus( obj )
{
	// オブジェクトの有効性を確認
	if( typeof(obj) == "undefined" || obj.disabled == true )
	{
		return false;
	}
	obj.focus();
}


//-->