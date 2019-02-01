


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







///// TAB IMAGE /////
/*
var objtabA1 = '<a href="javascript:void(0);" onclick="ShowInputA();autoFocus1();"><img onfocus="TabAOn(this);" onblur="TabAOff(this);fncDefaultTabindex( document.all.strProductCode );" onmouseover="TabAOn(this);" onmouseout="TabAOff(this);" src="' + tabA1 + '" width="24" height="272" border="0" alt="HEADER" tabindex="' + TabNumA + '"></a>';
var objtabA3 = '<img src="' + tabA3 + '" width="24" height="272" border="0" alt="HEADER">';
var objtabB1 = '<a href="javascript:void(0);" onclick="ShowInputB();autoFocus2();window.DLwin.fncDtHtml();"><img onfocus="TabBOn(this);" onblur="TabBOff(this);fncDefaultTabindex( document.all.dtmOrderAppDate );" onmouseover="TabBOn(this);" onmouseout="TabBOff(this);" src="' + tabB1 + '" width="24" height="272" border="0" alt="DETAIL" tabindex="' + TabNumB + '"></a>';
var objtabB3 = '<img src="' + tabB3 + '" width="24" height="272" border="0" alt="DETAIL">';
*/




	///// DATA OPEN BUTTON /////

var datasetbt1 = '<a href="#"><img onfocus="fncChangeBtImg( this, dataset2 );" onblur="fncChangeBtImg( this, dataset1 );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncChangeBtImg( this, dataset2 );" onmouseout="fncChangeBtImg( this, dataset1 ); fncAlphaOff( this );" src="' + dataset1 + '" width="19" height="19" border="0" tabindex="2"></a>';


var darkgrayOpenBt1 = '<a href="javascript:void(0);"><img onfocus="fncDarkGrayOpenButton( \'on\' , this );" onblur="fncDarkGrayOpenButton( \'off\' , this );" onmouseover="fncDarkGrayOpenButton( \'on\' , this );" onmouseout="fncDarkGrayOpenButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + darkgrayopen1 + '" width="19" height="19" border="0" tabindex=""></a>';

var darkgrayOpenBt3 = '<a href="javascript:void(0);"><img src="' + darkgrayopen3 + '" width="19" height="19" border="0" tabindex=""></a>';

var darkgrayOpenBtNotActive = '<img src="' + darkgrayopen1 + '" width="19" height="19" border="0" tabindex="">';



	///// TAX WIN BUTTON /////
/*
var showTaxbt1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="GPOn(this);" onmouseout="GPOff(this);fncAlphaOff( this );" src="' + showwin1 + '" width="19" height="19" border="0" alt="DETAIL"></a>';
var showTaxbt3 = '<a href="#"><img src="' + showwin3 + '" width="19" height="19" border="0" alt="DETAIL"></a>';


	///// TAX WIN HEADER /////
var taxheader = '<img src="' + taxhead + '" widht="306" height="12" border="0">';

	///// TAX WIN BOTTOM /////
var taxbottoms = '<img src="' + taxbottom + '" widht="306" height="12" border="0">';
*/


///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="仕入管理">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="SALES ORDER">';


///// INPUT A,B,C HEADER IMAGE /////
var headerAJ = '<img src="' + esheadtitle1J + '" width="949" height="30" border="0" alt="ヘッダー">';
var headerAE = '<img src="' + esheadtitle1E + '" width="949" height="30" border="0" alt="HEADER">';
var headerBJ = '<img src="' + esheadtitle2J + '" width="949" height="30" border="0" alt="見積情報">';
var headerBE = '<img src="' + esheadtitle2E + '" width="949" height="30" border="0" alt="ESTIMATE INFOMATION">';
var headerCJ = '<img src="' + esheadtitle3J + '" width="949" height="30" border="0" alt="見積明細">';
var headerCE = '<img src="' + esheadtitle3E + '" width="949" height="30" border="0" alt="ESTIMATE DETAIL">';
var headerDJ = '<img src="' + esheadtitle4J + '" width="949" height="30" border="0" alt="見積明細一覧">';
var headerDE = '<img src="' + esheadtitle4E + '" width="949" height="30" border="0" alt="ESTIMATEDETAIL LIST">';


///// CSS VALUE /////
var fcolor    = '#666666'; //項目フォントカラー
var segcolor  = '#e8f0f1'; //項目背景色
var segbody   = '#d6d0b1'; //INPUT A BODY 背景色
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //項目右空きBORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //項目左空きBORDER
var brcolor03 = '#798787 #e8f0f1 #798787 #e8f0f1'; //項目両空きBORDER


function initLayoutES()
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


	if( typeof(DataSetBt) != 'undefined' )
	{
		DataSetBt.innerHTML = datasetbt1;
	}


	///// MAIN TITLE /////
	MainTitle.innerHTML = maintitleJ;

	///// TAX BUTTON /////
	//TaxBt.innerHTML = showTaxbt1;

	///// INPUT A BODYS COLOR /////
	SegABodys.style.background = segbody;
	SegBBodys.style.background = segbody;
	SegCBodys.style.background = segbody;
	SegDBodys.style.background = segbody;

	///// INPUT A,B,C HEADER TITLE IMAGE /////
	SegAHeader.innerHTML = headerAJ;
	SegBHeader.innerHTML = headerBJ;
	SegCHeader.innerHTML = headerCJ;
	SegDHeader.innerHTML = headerCJ;

	///// INPUT A,B,C BOTTOM IMAGE /////
	SegABottom.innerHTML = bottom03;
	SegBBottom.innerHTML = bottom03;
	SegCBottom.innerHTML = bottom03;
	SegDBottom.innerHTML = bottom03;

	var obj = document.HSO.lngWorkflowOrderCode;

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

	///// INPUT A FONT COLOR /////
	colProductsCode.style.color   = fcolor;
	colProductsName.style.color   = fcolor;
	colDeliveryDate.style.color   = fcolor;
	colDeptCode.style.color       = fcolor;
	colInChargeCode.style.color   = fcolor;
	colInChargeName.style.color   = fcolor;
	colSellingPrice.style.color   = fcolor;
	colCartonQty.style.color      = fcolor;
	colPlanQty.style.color        = fcolor;
	colRefoundQty.style.color     = fcolor;
	colWholesalePrice.style.color = fcolor;

	///// INPUT A SEGMENT BG COLOR /////
	colProductsCode.style.background   = segcolor;
	colProductsName.style.background   = segcolor;
	colDeliveryDate.style.background   = segcolor;
	colDeptCode.style.background       = segcolor;
	colInChargeCode.style.background   = segcolor;
	colInChargeName.style.background   = segcolor;
	colSellingPrice.style.background   = segcolor;
	colCartonQty.style.background      = segcolor;
	colPlanQty.style.background        = segcolor;
	colRefoundQty.style.background     = segcolor;
	colWholesalePrice.style.background = segcolor;


	///// INPUT A VARS BG COLOR /////
	varProductsCode.style.background   = segcolor;
	varProductsName.style.background   = segcolor;
	varDeliveryDate.style.background   = segcolor;
	varDeptCode.style.background       = segcolor;
	varInChargeCode.style.background   = segcolor;
	varInChargeName.style.background   = segcolor;
	varSellingPrice.style.background   = segcolor;
	varCartonQty.style.background      = segcolor;
	varPlanQty.style.background        = segcolor;
	varRefoundQty.style.background     = segcolor;
	varWholesalePrice.style.background = segcolor;


	///// INPUT A SEGMENT BORDER COLOR /////
	colProductsCode.style.borderColor   = brcolor01;
	colProductsName.style.borderColor   = brcolor01;
	colDeliveryDate.style.borderColor   = brcolor01;
	colDeptCode.style.borderColor       = brcolor01;
	colInChargeCode.style.borderColor   = brcolor01;
	colInChargeName.style.borderColor   = brcolor01;
	colSellingPrice.style.borderColor   = brcolor01;
	colCartonQty.style.borderColor      = brcolor01;
	colPlanQty.style.borderColor        = brcolor01;
	colRefoundQty.style.borderColor     = brcolor01;
	colWholesalePrice.style.borderColor = brcolor01;


	///// INPUT A VARS BORDER COLOR /////
	varProductsCode.style.borderColor   = brcolor02;
	varProductsName.style.borderColor   = brcolor02;
	varDeliveryDate.style.borderColor   = brcolor02;
	varDeptCode.style.borderColor       = brcolor02;
	varInChargeCode.style.borderColor   = brcolor02;
	varInChargeName.style.borderColor   = brcolor02;
	varSellingPrice.style.borderColor   = brcolor02;
	varCartonQty.style.borderColor      = brcolor02;
	varPlanQty.style.borderColor        = brcolor02;
	varRefoundQty.style.borderColor     = brcolor02;
	varWholesalePrice.style.borderColor = brcolor02;





	///// INPUT B FONT COLOR /////
	colDetailStockSubject.style.color = fcolor;
	colDetailStockItem.style.color    = fcolor;
	colDetailAmortized.style.color    = fcolor;
	colDetailSupplier.style.color     = fcolor;
	colDetailCurrency.style.color     = fcolor;
	colDetailRate.style.color         = fcolor;
	colDetailPlanQty.style.color      = fcolor;
	colDetailPrice.style.color        = fcolor;
	colDetailPlanEstimate.style.color = fcolor;
	colDetailInput.style.color        = fcolor;
	colDetailPlanRate.style.color     = fcolor;
	colDetailSubTotal.style.color     = fcolor;
	colDetailReamrk.style.color       = fcolor;


	///// INPUT B SEGMENT BG COLOR /////
	colDetailStockSubject.style.background = segcolor;
	colDetailStockItem.style.background    = segcolor;
	colDetailAmortized.style.background    = segcolor;
	colDetailSupplier.style.background     = segcolor;
	colDetailCurrency.style.background     = segcolor;
	colDetailRate.style.background         = segcolor;
	colDetailPlanQty.style.background      = segcolor;
	colDetailPrice.style.background        = segcolor;
	colDetailPlanEstimate.style.background = segcolor;
	colDetailInput.style.background        = segcolor;
	colDetailPlanRate.style.background     = segcolor;
	colDetailSubTotal.style.background     = '#f1f1f1';
	colDetailReamrk.style.background       = segcolor;


	///// INPUT B VARS BG COLOR /////
	varDetailStockSubject.style.background = segcolor;
	varDetailStockItem.style.background    = segcolor;
	varDetailAmortized.style.background    = segcolor;
	varDetailSupplierCode.style.background = segcolor;
	varDetailSupplierName.style.background = segcolor;
	varDetailCurrency.style.background     = segcolor;
	varDetailRate.style.background         = segcolor;
	varDetailPlanQty.style.background      = segcolor;
	varDetailPrice.style.background        = segcolor;
	varDetailPlanEstimate.style.background = segcolor;
	varDetailInput.style.background        = segcolor;
	varDetailPlanRate.style.background     = segcolor;
	varDetailSubTotal.style.background     = '#f1f1f1';
	varDetailReamrk.style.background       = segcolor;


	///// INPUT B SEGMENT BORDER COLOR /////
	colDetailStockSubject.style.borderColor = brcolor01;
	colDetailStockItem.style.borderColor    = brcolor01;
	colDetailAmortized.style.borderColor    = brcolor01;
	colDetailSupplier.style.borderColor     = brcolor01;
	colDetailCurrency.style.borderColor     = brcolor01;
	colDetailRate.style.borderColor         = brcolor01;
	colDetailPlanQty.style.borderColor      = brcolor01;
	colDetailPrice.style.borderColor        = brcolor01;
	colDetailPlanEstimate.style.borderColor = brcolor01;
	colDetailInput.style.borderColor        = brcolor01;
	colDetailPlanRate.style.borderColor     = brcolor01;
	colDetailSubTotal.style.borderColor     = brcolor01;
	colDetailReamrk.style.borderColor       = brcolor01;


	///// INPUT B VARS BORDER COLOR /////
	varDetailStockSubject.style.borderColor = brcolor02;
	varDetailStockItem.style.borderColor    = brcolor02;
	varDetailAmortized.style.borderColor    = brcolor02;
	varDetailSupplierCode.style.borderColor = brcolor03;
	varDetailSupplierName.style.borderColor = brcolor02;
	varDetailCurrency.style.borderColor     = brcolor02;
	varDetailRate.style.borderColor         = brcolor02;
	varDetailPlanQty.style.borderColor      = brcolor02;
	varDetailPrice.style.borderColor        = brcolor02;
	varDetailPlanEstimate.style.borderColor = brcolor02;
	varDetailInput.style.borderColor        = brcolor02;
	varDetailPlanRate.style.borderColor     = brcolor02;
	varDetailSubTotal.style.borderColor     = brcolor02;
	varDetailReamrk.style.borderColor       = brcolor02;


/*
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
*/
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
