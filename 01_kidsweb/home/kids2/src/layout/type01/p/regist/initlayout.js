<!--


//-----------------------------------------------------------------------------
// 概要 : ローカル変数定義
// 解説 :「TabIndex」値の設定
//-----------------------------------------------------------------------------

//------------------------------------------
// 適用箇所 :「サブウィンドウボタン」
//------------------------------------------
var NumTabA1   = ''   ; // vendor
var NumTabA1_2 = '23' ; // creation
var NumTabA1_3 = '25' ; // assembly
var NumTabB1   = '5'  ; // dept
var NumTabC1   = ''   ; // products
var NumTabD1   = '27' ; // location
var NumTabE1   = ''   ; // applicant
var NumTabF1   = ''   ; // wfinput
var NumTabG1   = '11' ; // vi
var NumTabH1   = ''   ; // supplier
var NumTabI1   = ''   ; // input


//------------------------------------------
// 適用箇所 :「商品管理」タブ
//------------------------------------------
var PTabNumA = '42' ; // A
var PTabNumB = '21' ; // B


//------------------------------------------
// 適用箇所 :「受注・発注・売上・仕入」タブ
//------------------------------------------
var TabNumA = '' ; // ヘッダー
var TabNumB = '' ; // 明細


//------------------------------------------
// 適用箇所 :「登録ボタン」
//------------------------------------------
var RegistNum = '41' ;


//------------------------------------------
// 適用箇所 :「行追加ボタン」
//------------------------------------------
var AddRowNum = '' ;


//------------------------------------------
// 適用箇所 :「カレンダーボタン」
//------------------------------------------
var NumDateTabA = '' ;
var NumDateTabB = '' ;
var NumDateTabC = '' ;


//------------------------------------------
// 適用箇所 :「製品数量ボタン」
//------------------------------------------
var NumPunitTab = '' ;





	//-------------------------------------------------------------------------
	// v2 tomita update
	//-------------------------------------------------------------------------
	// 「ワークフロー順序ウィンドウ」ボタンイメージ生成
	var darkgrayOpenBt1 = '<a href="javascript:void(0);"><img onfocus="fncDarkGrayOpenButton( \'on\' , this );" onblur="fncDarkGrayOpenButton( \'off\' , this );" onmouseover="fncDarkGrayOpenButton( \'on\' , this );" onmouseout="fncDarkGrayOpenButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + darkgrayopen1 + '" width="19" height="19" border="0" tabindex=""></a>';

	var darkgrayOpenBt3 = '<a href="javascript:void(0);"><img src="' + darkgrayopen3 + '" width="19" height="19" border="0" tabindex=""></a>';

	var darkgrayOpenBtNotActive = '<img src="' + darkgrayopen1 + '" width="19" height="19" border="0" tabindex="">';
	//-------------------------------------------------------------------------





	///// [PRODUCTS] TAB IMAGE /////
	var objPtabA1 = '<a href="javascript:void(0);" onclick="ShowInputA();autoFocus1();"><img onfocus="PTabAOn(this);" onblur="PTabAOff(this);fncDefaultTabindex( document.all.lngFactoryCode );" onmouseover="PTabAOn(this);" onmouseout="PTabAOff(this);" src="' + ptabA1 + '" width="24" height="272" border="0" alt="REGISTRATION A" tabindex="' + PTabNumA + '"></a>';
	var objPtabA3 = '<img src="' + ptabA3 + '" width="24" height="272" border="0" alt="REGISTRATION A">';
	var objPtabB1 = '<a href="javascript:void(0);" onclick="ShowInputB();autoFocus2();"><img onfocus="PTabBOn(this);" onblur="PTabBOff(this);fncDefaultTabindex( document.all.strProductName );" onmouseover="PTabBOn(this);" onmouseout="PTabBOff(this);" src="' + ptabB1 + '" width="24" height="272" border="0" alt="REGISTRATION B" tabindex="' + PTabNumB + '"></a>';
	var objPtabB3 = '<img src="' + ptabB3 + '" width="24" height="272" border="0" alt="REGISTRATION B">';


	//--------------------------------------------------------
	// 仕様詳細エディットボタン
	//--------------------------------------------------------
	var EditButton1 = '<a href="#"><img id="EditOpenBtImg" onfocus="fncEditButton( \'on\' , this );" onblur="fncEditButton( \'off\' , this );" onmouseover="fncEditButton( \'on\' , this );" onmouseout="fncEditButton( \'off\' , this );" src="' + editbt1 + '" width="19" height="19" border="0" alt="EDIT" tabindex="40"></a>';
	var EditButton3 = '<img id="EditOpenBtImg" src="' + editbt3 + '" width="19" height="19" border="0" alt="EDIT" tabindex="40">';



//-------------------------------------------------------------------------------
///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="商品管理">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="PRODUCTS">';



	///// GOODS PLAN BUTTON /////
var showGPbt1 = '<a href="#"><img onmouseover="GPOn(this);" onmouseout="GPOff(this);" src="' + showwin1 + '" width="19" height="19" border="0" alt="DETAIL"></a>';
var showGPbt3 = '<a href="#"><img src="' + showwin3 + '" width="19" height="19" border="0" alt="DETAIL"></a>';


	///// GOODS PLAN HEADER /////
var gpheader = '<img src="' + gphead + '" widht="290" height="12" border="0">';

	///// GOODS PLAN BOTTOM /////
var gpbottoms = '<img src="' + gpbottom + '" widht="290" height="12" border="0">';


///// INPUT A,B,C HEADER IMAGE /////
var headerAJ = '<img src="' + pheadtitleAJ + '" width="927" height="30" border="0">';
var headerAE = '<img src="' + pheadtitleAE + '" width="927" height="30" border="0" >';
var headerBJ = '<img src="' + pheadtitleBJ + '" width="927" height="30" border="0">';
var headerBE = '<img src="' + pheadtitleBE + '" width="927" height="30" border="0">';

///// [RENEW] INPUT A,B,C HEADER IMAGE /////
var headerARenewJ = '<img src="' + pheadtitleAJ + '" width="927" height="30" border="0">';
var headerARenewE = '<img src="' + pheadtitleAE + '" width="927" height="30" border="0">';
var headerBRenewJ = '<img src="' + pheadtitleBJ + '" width="927" height="30" border="0">';
var headerBRenewE = '<img src="' + pheadtitleBE + '" width="927" height="30" border="0">';


///// CSS VALUE /////
var fcolor = '#666666'; //項目フォントカラー
var segcolor = '#e8f0f1'; //項目背景色
var segbody = '#d6d0b1'; //INPUT A BODY 背景色
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //項目右空きBORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //項目左空きBORDER
var brcolor03 = '#798787 #e8f0f1 #798787 #e8f0f1'; //項目左右空きBORDER


function initLayoutP()
{
	if (typeof(PTabA)!='undefined')
	{
		PTabA.innerHTML = objPtabA3;
	}

	if (typeof(PTabB)!='undefined')
	{
		PTabB.innerHTML = objPtabB1;
	}

	///// GOODS PLAN BUTTON /////
	GoodsPlanButton.innerHTML = showGPbt1;

	CreateDate.style.color = fcolor;
	CreateDate.style.background = '#f1f1f1';
	CreateDateVars.style.background = '#f1f1f1';
	CreateDate.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	CreateDateVars.style.borderColor = '#798787 #798787 #798787 #f1f1f1';


	///// MAIN TITLE /////
	if( typeof(MainTitle) != 'undefined' )
	{
		MainTitle.innerHTML = maintitleJ;
	}

	///// INPUT A BODYS COLOR /////
	SegABodys.style.background = segbody;
	SegBBodys.style.background = segbody;

	///// INPUT A,B,C HEADER TITLE IMAGE /////
	if( typeof(SegAHeader) != 'undefined' ||
		typeof(SegBHeader) != 'undefined' )
	{
		SegAHeader.innerHTML = headerAJ;
		SegBHeader.innerHTML = headerBJ;
	}

	if( typeof(SegAHeaderRenew) != 'undefined' ||
		typeof(SegBHeaderRenew) != 'undefined' )
	{
		SegAHeaderRenew.innerHTML = headerARenewJ;
		SegBHeaderRenew.innerHTML = headerBRenewJ;
	}

	///// INPUT A,B,C BOTTOM IMAGE /////
	SegABottom.innerHTML = bottom01;
	SegBBottom.innerHTML = bottom01;

	///// INPUT A FONT COLOR /////
	SegA01.style.color = fcolor;
	SegA02.style.color = fcolor;
	SegA18.style.color = fcolor;
	SegA03.style.color = fcolor;
	SegA04.style.color = fcolor;
	SegA05.style.color = fcolor;
	SegA06.style.color = fcolor;
	SegA07.style.color = fcolor;
	SegA08.style.color = fcolor;
	SegA09.style.color = fcolor;
//SegA10.style.color = fcolor;
	SegA11.style.color = fcolor;
	SegA12.style.color = fcolor;
	SegA13.style.color = fcolor;
	SegA14.style.color = fcolor;
	SegA15.style.color = fcolor;

	SegA17.style.color = fcolor;

	///// INPUT A SEGMENT BG COLOR /////
	SegA01.style.background = segcolor;
	SegA02.style.background = segcolor;
	SegA18.style.background = segcolor;
	SegA03.style.background = segcolor;
	SegA04.style.background = segcolor;
	SegA0304.style.background = segcolor;
	SegA05.style.background = segcolor;
	SegA06.style.background = segcolor;
	SegA07.style.background = segcolor;
	SegA08.style.background = segcolor;
	SegA09.style.background = segcolor;
	SegB0809.style.background = segcolor;
//SegA10.style.background = segcolor;
	SegA11.style.background = segcolor;
	SegA12.style.background = segcolor;
	SegA13.style.background = segcolor;
	SegA14.style.background = segcolor;
	SegA15.style.background = segcolor;

	SegA17.style.background = segcolor;

	///// INPUT A VARS BG COLOR /////
	VarsA01.style.background = segcolor;
	VarsA02.style.background = segcolor;
	VarsA18.style.background = segcolor;
	//VarsA02_2.style.background = segcolor;
	VarsA03.style.background = segcolor;
	//VarsA03_2.style.background = segcolor;
	VarsA04.style.background = segcolor;
	//VarsA04_2.style.background = segcolor;
	VarsA05.style.background = segcolor;
	VarsA06.style.background = segcolor;
	VarsA07.style.background = segcolor;
	VarsA08.style.background = segcolor;
	VarsA09.style.background = segcolor;
//VarsA10.style.background = segcolor;
	VarsA11.style.background = segcolor;
	VarsA12.style.background = segcolor;
	VarsA13.style.background = segcolor;
	VarsA14.style.background = segcolor;
	VarsA15.style.background = segcolor;

	VarsA17.style.background = segcolor;
	//VarsA17_2.style.background = segcolor;

	///// INPUT A SEGMENT BORDER COLOR /////
	SegA01.style.borderColor = brcolor01;
	SegA02.style.borderColor = brcolor01;
	SegA18.style.borderColor = brcolor01;
	SegA03.style.borderColor = brcolor01;
	SegA04.style.borderColor = brcolor01;
	SegA0304.style.borderColor = brcolor02;
	SegA05.style.borderColor = brcolor01;
	SegA06.style.borderColor = brcolor01;
	SegA07.style.borderColor = brcolor01;
	SegA08.style.borderColor = brcolor01;
	SegA09.style.borderColor = brcolor01;
	SegB0809.style.borderColor = brcolor02;
//SegA10.style.borderColor = brcolor01;
	SegA11.style.borderColor = brcolor01;
	SegA12.style.borderColor = brcolor01;
	SegA13.style.borderColor = brcolor01;
	SegA14.style.borderColor = brcolor01;
	SegA15.style.borderColor = brcolor01;

	SegA17.style.borderColor = brcolor01;

	///// INPUT A VARS BORDER COLOR /////
	VarsA01.style.borderColor = brcolor02;
	VarsA02.style.borderColor = brcolor02;
	VarsA18.style.borderColor = brcolor02;
	//VarsA02_2.style.borderColor = brcolor02;
	VarsA03.style.borderColor = brcolor02;
	//VarsA03_2.style.borderColor = brcolor02;
	VarsA04.style.borderColor = brcolor02;
	//VarsA04_2.style.borderColor = brcolor02;
	VarsA05.style.borderColor = brcolor02;
	VarsA06.style.borderColor = brcolor02;
	VarsA07.style.borderColor = brcolor02;
	VarsA08.style.borderColor = brcolor02;
	VarsA09.style.borderColor = brcolor02;
//VarsA10.style.borderColor = brcolor02;
	VarsA11.style.borderColor = brcolor02;
	VarsA12.style.borderColor = brcolor02;
	VarsA13.style.borderColor = brcolor02;
	VarsA14.style.borderColor = brcolor02;
	VarsA15.style.borderColor = brcolor02;

	VarsA17.style.borderColor = brcolor02;
	//VarsA17_2.style.borderColor = brcolor02;




	///// INPUT B FONT COLOR /////
	SegB01.style.color = fcolor;
	SegB02.style.color = fcolor;
	SegB03.style.color = fcolor;
	SegB04.style.color = fcolor;
	SegB05.style.color = fcolor;
	SegB06.style.color = fcolor;
	SegB07.style.color = fcolor;
	SegB08.style.color = fcolor;
	SegB09.style.color = fcolor;
	SegB10.style.color = fcolor;
	SegB20.style.color = fcolor;
	SegB11.style.color = fcolor;
	SegB12.style.color = fcolor;
	SegB13.style.color = fcolor;
	SegB14.style.color = fcolor;

	SegB16.style.color = fcolor;
	SegB17.style.color = fcolor;

	///// INPUT B SEGMENT BG COLOR /////
	SegB01.style.background = segcolor;
	SegB02.style.background = segcolor;
	SegB03.style.background = segcolor;
	SegB04.style.background = segcolor;
	SegB05.style.background = segcolor;
	SegB06.style.background = segcolor;
	SegB07.style.background = segcolor;
	SegB08.style.background = segcolor;
	SegB09.style.background = segcolor;
	SegB10.style.background = segcolor;
	SegB20.style.background = segcolor;
	SegB11.style.background = segcolor;
	SegB12.style.background = segcolor;
	SegB13.style.background = segcolor;
	SegB14.style.background = segcolor;

	SegB16.style.background = segcolor;
	SegB17.style.background = segcolor;

	///// INPUT B VARS BG COLOR /////
	VarsB01.style.background = segcolor;
	VarsB02.style.background = segcolor;
	VarsB03.style.background = segcolor;
	//VarsB03_2.style.background = segcolor;
	VarsB04.style.background = segcolor;
	//VarsB04_2.style.background = segcolor;
	VarsB05.style.background = segcolor;
	VarsB06_1.style.background = segcolor;
	VarsB06_2.style.background = segcolor;
	VarsB07_1.style.background = segcolor;
	VarsB07_2.style.background = segcolor;
	VarsB08.style.background = segcolor;
	//VarsB08_2.style.background = segcolor;
	VarsB09.style.background = segcolor;
	VarsB10.style.background = segcolor;
	VarsB20.style.background = segcolor;
	VarsB11.style.background = segcolor;
	VarsB12.style.background = segcolor;
	VarsB13.style.background = segcolor;
	VarsB14.style.background = segcolor;

	VarsB16.style.background = segcolor;
	VarsB17.style.background = segcolor;

	///// INPUT B SEGMENT BORDER COLOR /////
	SegB01.style.borderColor = brcolor01;
	SegB02.style.borderColor = brcolor01;
	SegB03.style.borderColor = brcolor01;
	SegB04.style.borderColor = brcolor01;
	SegB05.style.borderColor = brcolor01;
	SegB06.style.borderColor = brcolor01;
	SegB07.style.borderColor = brcolor01;
	SegB08.style.borderColor = brcolor01;
	SegB09.style.borderColor = brcolor01;
	SegB10.style.borderColor = brcolor01;
	SegB20.style.borderColor = brcolor01;
	SegB11.style.borderColor = brcolor01;
	SegB12.style.borderColor = brcolor01;
	SegB13.style.borderColor = brcolor01;
	SegB14.style.borderColor = brcolor01;

	SegB16.style.borderColor = brcolor01;
	SegB17.style.borderColor = brcolor01;

	///// INPUT B VARS BORDER COLOR /////
	VarsB01.style.borderColor = brcolor02;
	VarsB02.style.borderColor = brcolor02;
	VarsB03.style.borderColor = brcolor02;
	//VarsB03_2.style.borderColor = brcolor02;
	VarsB04.style.borderColor = brcolor02;
	//VarsB04_2.style.borderColor = brcolor02;
	VarsB05.style.borderColor = brcolor02;
	VarsB06_1.style.borderColor = brcolor02;
	VarsB06_2.style.borderColor = brcolor02;
	VarsB07_1.style.borderColor = brcolor02;
	VarsB07_2.style.borderColor = brcolor02;
	VarsB08.style.borderColor = brcolor02;
	//VarsB08_2.style.borderColor = brcolor02;
	VarsB09.style.borderColor = brcolor02;
	VarsB10.style.borderColor = brcolor02;
	VarsB20.style.borderColor = brcolor02;
	VarsB11.style.borderColor = brcolor02;
	VarsB12.style.borderColor = brcolor02;
	VarsB13.style.borderColor = brcolor02;
	VarsB14.style.borderColor = brcolor02;

	VarsB16.style.borderColor = brcolor02;
	VarsB17.style.borderColor = brcolor02;



	///// GOODS PLAN WINDOW /////
	ReviseNumber.style.color = fcolor;
	ProgressStatus.style.color = fcolor;
	ReviseDate.style.color = fcolor;

	ProgressStatus.style.background = '#f1f1f1';
	ReviseNumber.style.background = '#f1f1f1';
	ReviseDate.style.background = '#f1f1f1';

	ProgressCodeVars.style.background = '#f1f1f1';
	ReviseNumberVars.style.background = '#f1f1f1';
	ReviseDateVars.style.background = '#f1f1f1';

	ProgressStatus.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	ReviseNumber.style.borderColor = '#798787 #f1f1f1 #798787 #798787';
	ReviseDate.style.borderColor = '#798787 #f1f1f1 #798787 #798787';

	ProgressCodeVars.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	ReviseNumberVars.style.borderColor = '#798787 #798787 #798787 #f1f1f1';
	ReviseDateVars.style.borderColor = '#798787 #798787 #798787 #f1f1f1';

	GoodsPlanHeader.innerHTML = gpheader;
	GoodsPlanBottom.innerHTML = gpbottoms;


	EditBt.innerHTML = EditButton1;


	// [特殊文字]ボタン表示
	SpecialBt.style.visibility = 'visible';





	//-------------------------------------------------------------------------
	// v2 tomita update
	//-------------------------------------------------------------------------
	SegBWF.style.color        = '#ffffff';
	SegBWF.style.borderColor  = '#cdcdcd #72828b #cdcdcd #cdcdcd';
	SegBWF.style.background   = 'transparent';
	VarsBWF.style.borderColor = '#cdcdcd #cdcdcd #cdcdcd #72828b';
	VarsBWF.style.background  = 'transparent';


	var obj = document.PPP2.lngWorkflowOrderCode;


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


//-->