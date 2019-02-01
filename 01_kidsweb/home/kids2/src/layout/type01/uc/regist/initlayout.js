<!--


//-----------------------------------------------------------------------------
// 概要 : ローカル変数定義
// 解説 :「TabIndex」値の設定
//-----------------------------------------------------------------------------

//------------------------------------------
// 適用箇所 :「サブウィンドウボタン」
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
// 適用箇所 :「商品管理」タブ
//------------------------------------------
var PTabNumA = '' ; // A
var PTabNumB = '' ; // B


//------------------------------------------
// 適用箇所 :「受注・発注・売上・仕入」タブ
//------------------------------------------
var TabNumA = '' ; // ヘッダー
var TabNumB = '' ; // 明細


//------------------------------------------
// 適用箇所 :「登録ボタン」
//------------------------------------------
var RegistNum = '15' ;


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
var NumPunitTab = '11' ;





///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="ユーザー管理">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="USER CONTROL">';


///// INPUT A,B,C HEADER IMAGE /////
var headerAJ = '<img src="' + pheadtitleAJ + '" width="949" height="30" border="0" alt="ユーザー登録">';
var headerAE = '<img src="' + pheadtitleAE + '" width="949" height="30" border="0" alt="USER REGISTRATION">';



///// MAIN MTITLE IMAGE /////
var pictspace = '<img src="' + pictimg + '" width="70" height=70" border="0">';




///// CSS VALUE /////
var fcolor = '#666666'; //項目フォントカラー
var segcolor = '#e8f0f1'; //項目背景色
var segbody = '#d6d0b1'; //INPUT A BODY 背景色
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //項目右空きBORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //項目左空きBORDER
var brcolor03 = '#798787 #e8f0f1 #798787 #e8f0f1'; //項目左右空きBORDER

var brcolorF1 = '#798787 #f1f1f1 #798787 #798787'; //項目右空きBORDER
var brcolorF2 = '#798787 #798787 #798787 #f1f1f1'; //項目左空きBORDER


function initLayoutUC()
{

	///// MAIN TITLE /////
	MainTitle.innerHTML = maintitleJ;

	///// INPUT A BODYS COLOR /////
	SegABodys.style.background = segbody;


	///// INPUT A,B,C HEADER TITLE IMAGE /////
	SegAHeader.innerHTML = headerAJ;


	///// INPUT A,B,C BOTTOM IMAGE /////
	SegABottom.innerHTML = bottom03;


	///// PICTURE SPACE IMAGE /////
	if( typeof(PictSpace) != 'undefined' )
	{
		PictSpace.innerHTML = pictspace;
	}


	SegA01.style.color = fcolor;
	SegA02.style.color = fcolor;
	SegA03.style.color = fcolor;
	SegA04.style.color = fcolor;
	SegA05.style.color = fcolor;
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
	SegA16.style.color = fcolor;


	SegA01.style.background = '#f1f1f1';
	SegA02.style.background = segcolor;
	SegA03.style.background = segcolor;
	SegA04.style.background = segcolor;
	SegA05.style.background = segcolor;
	SegA06.style.background = '#f1f1f1';
	SegA07.style.background = segcolor;
	SegA08.style.background = '#f1f1f1';
	SegA09.style.background = segcolor;
	SegA10.style.background = segcolor;
	SegA11.style.background = segcolor;
	SegA12.style.background = segcolor;
	SegA13.style.background = segcolor;
	SegA1213.style.background = segcolor;
	SegA14.style.background = segcolor;
	SegA15.style.background = segcolor;
	SegA16.style.background = segcolor;


	SegA01.style.borderColor = brcolorF1;
	SegA02.style.borderColor = brcolor01;
	SegA03.style.borderColor = brcolor01;
	SegA04.style.borderColor = brcolor01;
	SegA05.style.borderColor = brcolor01;
	SegA06.style.borderColor = brcolorF1;
	SegA07.style.borderColor = brcolor01;
	SegA08.style.borderColor = brcolorF1;
	SegA09.style.borderColor = brcolor01;
	SegA10.style.borderColor = brcolor01;
	SegA11.style.borderColor = brcolor01;
	SegA12.style.borderColor = brcolor01;
	SegA13.style.borderColor = brcolor01;
	SegA1213.style.borderColor = brcolor02;
	SegA14.style.borderColor = brcolor01;
	SegA15.style.borderColor = brcolor01;
	SegA16.style.borderColor = brcolor01;


	VarsA01.style.background = '#f1f1f1';
	VarsA02.style.background = segcolor;
	VarsA03.style.background = segcolor;
	VarsA04.style.background = segcolor;
	VarsA05.style.background = segcolor;
	VarsA06.style.background = '#f1f1f1';
	VarsA07.style.background = segcolor;
	VarsA08.style.background = '#f1f1f1';
	VarsA09.style.background = segcolor;
	VarsA10.style.background = segcolor;
	VarsA11.style.background = segcolor;
	VarsA12.style.background = segcolor;
	VarsA13.style.background = segcolor;
	VarsA14.style.background = segcolor;
	VarsA15.style.background = segcolor;
	VarsA16.style.background = segcolor;


	VarsA01.style.borderColor = brcolorF2;
	VarsA02.style.borderColor = brcolor02;
	VarsA03.style.borderColor = brcolor02;
	VarsA04.style.borderColor = brcolor02;
	VarsA05.style.borderColor = brcolor02;
	VarsA06.style.borderColor = brcolorF2;
	VarsA07.style.borderColor = brcolor02;
	VarsA08.style.borderColor = brcolorF2;
	VarsA09.style.borderColor = brcolor02;
	VarsA10.style.borderColor = brcolor02;
	VarsA11.style.borderColor = brcolor02;
	VarsA12.style.borderColor = brcolor02;
	VarsA13.style.borderColor = brcolor02;
	VarsA14.style.borderColor = brcolor02;
	VarsA15.style.borderColor = brcolor02;
	VarsA16.style.borderColor = brcolor02;

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