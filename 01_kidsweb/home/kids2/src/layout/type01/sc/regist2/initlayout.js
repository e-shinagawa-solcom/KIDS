//<!--


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
var NumTabB1   = '7'  ; // dept
var NumTabC1   = '19' ; // products
var NumTabD1   = '9'  ; // location
var NumTabE1   = ''   ; // applicant
var NumTabF1   = ''   ; // wfinput
var NumTabG1   = ''   ; // vi
var NumTabH1   = '4'  ; // supplier
var NumTabI1   = ''   ; // input


//------------------------------------------
// 適用箇所 :「商品管理」タブ
//------------------------------------------
var PTabNumA = '' ; // A
var PTabNumB = '' ; // B


//------------------------------------------
// 適用箇所 :「受注・発注・売上・仕入」タブ
//------------------------------------------
var TabNumA = '35' ; // ヘッダー
var TabNumB = '17' ; // 明細


//------------------------------------------
// 適用箇所 :「登録ボタン」
//------------------------------------------
var RegistNum = '34' ;


//------------------------------------------
// 適用箇所 :「行追加ボタン」
//------------------------------------------
var AddRowNum = '32' ;


//------------------------------------------
// 適用箇所 :「カレンダーボタン」
//------------------------------------------
var NumDateTabA = '2'  ;
var NumDateTabB = '15' ;
var NumDateTabC = '25' ;


//------------------------------------------
// 適用箇所 :「製品数量ボタン」
//------------------------------------------
var NumPunitTab = '30' ;






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

				document.all.SegDept.style.visibility      = 'hidden';
				document.all.SegIncharge.style.visibility  = 'hidden';
				document.all.VarsDept.style.visibility     = 'hidden';
				document.all.VarsIncharge.style.visibility = 'hidden';

				dinflag = 0;
				break;

			case 1:
				document.all.lngProductUnitCode_gs.style.visibility = 'visible';
				document.all.lngProductUnitCode_ps.style.visibility = 'visible';
				break;

			default:
				break;
		}
	}
	//-------------------------------------------------------------------------





///// TAB IMAGE /////
var objtabA1 = '<a href="javascript:void(0);" onclick="ShowInputA();fncRedirectOpen();autoFocus1();"><img onfocus="TabAOn(this);" onblur="TabAOff(this);fncDefaultTabindex( document.all.strProductCode );" onmouseover="TabAOn(this);" onmouseout="TabAOff(this);" src="' + tabA1 + '" width="24" height="272" border="0" alt="HEADER" tabindex="' + TabNumA + '"></a>';
var objtabA3 = '<img src="' + tabA3 + '" width="24" height="272" border="0" alt="HEADER">';
var objtabB1 = '<a href="javascript:void(0);" onclick="ShowInputB();autoFocus2();window.DLwin.fncDtHtml();"><img onfocus="TabBOn(this);" onblur="TabBOff(this);fncDefaultTabindex( document.all.dtmOrderAppDate );" onmouseover="TabBOn(this);" onmouseout="TabBOff(this);" src="' + tabB1 + '" width="24" height="272" border="0" alt="DETAIL" tabindex="' + TabNumB + '"></a>';
var objtabB3 = '<img src="' + tabB3 + '" width="24" height="272" border="0" alt="DETAIL">';


/* *v1*
//----------------------------------------------------
// 「ワークフロー順序ウィンドウ」ボタンイメージ生成
//----------------------------------------------------
var darkgrayOpenBt1 = '<a href="javascript:void(0);"><img onfocus="fncDarkGrayOpenButton( \'on\' , this );" onblur="fncDarkGrayOpenButton( \'off\' , this );" onmouseover="fncDarkGrayOpenButton( \'on\' , this );" onmouseout="fncDarkGrayOpenButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + darkgrayopen1 + '" width="19" height="19" border="0" tabindex=""></a>';

var darkgrayOpenBt3 = '<a href="javascript:void(0);"><img src="' + darkgrayopen3 + '" width="19" height="19" border="0" tabindex=""></a>';

var darkgrayOpenBtNotActive = '<img src="' + darkgrayopen1 + '" width="19" height="19" border="0" tabindex="">';
*/


	///// TAX WIN BUTTON /////
var showTaxbt1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="GPOn(this);" onmouseout="GPOff(this);fncAlphaOff( this );" src="' + showwin1 + '" width="19" height="19" border="0" alt="DETAIL"></a>';
var showTaxbt3 = '<a href="#"><img src="' + showwin3 + '" width="19" height="19" border="0" alt="DETAIL"></a>';


	///// TAX WIN HEADER /////
var taxheader = '<img src="' + taxhead + '" widht="306" height="12" border="0">';

	///// TAX WIN BOTTOM /////
var taxbottoms = '<img src="' + taxbottom + '" widht="306" height="12" border="0">';


///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="発注管理">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="SALES ORDER">';


///// INPUT A,B,C HEADER IMAGE /////
var headerAJ = '<img src="' + scdheadtitleAJ + '" width="927" height="30" border="0" alt="売上（納品書）登録">';
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


function initLayoutPO()
{
	if (typeof(TabA)!='undefined')
	{
		TabA.innerHTML = objtabA3;
	}

	if (typeof(TabB)!='undefined')
	{
		TabB.innerHTML = objtabB1;
	}

	///// MAIN TITLE /////
	MainTitle.innerHTML = maintitleJ;

	///// INPUT A BODYS COLOR /////
	SegABodys.style.background = segbody;
	SegCBodys.style.background = segbody;

	///// INPUT A,B,C HEADER TITLE IMAGE /////
	SegAHeader.innerHTML = headerAJ;
	SegCHeader.innerHTML = headerCJ;

	///// INPUT A,B,C BOTTOM IMAGE /////
	SegABottom.innerHTML = bottom01;
	SegCBottom.innerHTML = bottom01;

	///// TAX WINDOW /////
	TaxHeader.innerHTML = taxheader;
	TaxBottom.innerHTML = taxbottoms;

	Tax.style.color = fcolor;
	TotalStdAmt.style.color = fcolor;

	Tax.style.background = '#f1f1f1';
	TotalStdAmt.style.background = '#f1f1f1';

	TaxVars.style.background = '#f1f1f1';
	TotalStdAmtVars.style.background = '#f1f1f1';

	Tax.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	TotalStdAmt.style.borderColor = '#798787 #f1f1f1 #798787 #798787';

	TaxVars.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	TotalStdAmtVars.style.borderColor = '#798787 #798787 #798787 #f1f1f1';



	//-------------------------------------------------------------------------
	// v2 tomita update
	//-------------------------------------------------------------------------
	/*
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
*/

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