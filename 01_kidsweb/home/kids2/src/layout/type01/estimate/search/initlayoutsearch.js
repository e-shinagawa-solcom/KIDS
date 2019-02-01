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
var RegistNum = '' ;


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






///// VIEW SEARCH IMAGE /////
var vishImgJ = '<img src="' + vishJ + '" width="100" height="23" border="0">';
var vishImgE = '<img src="' + vishE + '" width="100" height="23" border="0">';

///// OFf ON BUTTON /////
var offBt = '<a href="#"><img onmouseover="OnBt(this);" onmouseout="OffBt(this);" src="' + off + '" width="19" height="19" border="0"></a>';
var onBt = '<a href="#"><img src="' + on + '" width="19" height="19" border="0"></a>';

function initLayoutESSearch()
{
	ViewSearch1.innerHTML = vishImgJ;

	CheckAll1.innerHTML = offBt;
	CheckAll2.innerHTML = offBt;
}


function initLayoutSegs( obj1, obj2, obj3, obj4 )
{
	Backs.style.background = '#d6d0b1';


	var initYpos1 = 50; //TOP座標・商品化企画書項目初期値
	var initYpos2 = 50; //TOP座標・商品管理項目初期値

	var moveYpos = 31;  //TOP座標・移動値


	var check1Xpos = 10; //LEFT座標・チェックボックス[表示]固定値
	var check2Xpos = 58; //LEFT座標・チェックボックス[検索]固定値

	var segsXpos = 110; //LEFT座標・カラム固定値
	var varsXpos = 266; //LEFT座標・フォーム要素固定値


	var segsWidth = 157; //カラム幅固定値

	var FontColors    = '#666666';
	var BackColors1   = '#e8f0f1';
	var BackColors2   = '#f1f1f1';
	var BorderColors1 = '#798787 #e8f0f1 #798787 #798787';
	var BorderColors2 = '#798787 #798787 #798787 #e8f0f1';


	var lay1 = obj1.children; //カラム
	var lay2 = obj2.children; //フォーム要素
	var lay3 = obj3.children; //チェックボックス[表示]
	var lay4 = obj4.children; //チェックボックス[検索]


	var lngtabindex = 1; //TAB INDEX 初期値


	///// カラム展開 /////
	if ( obj1 != '' )
	{
		for (i = 0; i < lay1.length; i++)
		{
			lay1[i].style.top         = initYpos2 + ( moveYpos * i );
			lay1[i].style.left        = segsXpos;
			lay1[i].style.width       = segsWidth;
			lay1[i].style.background  = BackColors1;
			lay1[i].style.borderColor = BorderColors1;
			lay1[i].style.color       = FontColors;
		}
	}

	///// フォーム要素展開 /////
	if ( obj2 != '' )
	{
		for (i = 0; i < lay2.length; i++)
		{
			lay2[i].style.top         = initYpos2 + ( moveYpos * i );
			lay2[i].style.left        = varsXpos;
			lay2[i].style.background  = BackColors1;
			lay2[i].style.borderColor = BorderColors2;
		}
	}

	///// チェックボックス[検索]展開 /////
	if ( obj3 != '')
	{
		for (i = 0; i < lay3.length; i++)
		{
			lay3[i].style.top         = initYpos2 + ( moveYpos * i );
			lay3[i].style.left        = check1Xpos;
			lay3[i].style.background  = BackColors1;
			lay3[i].style.borderColor = BorderColors1;

			lay3[24].style.background  = '#f1f1f1';
			lay3[24].style.borderColor = '#798787 #e8f0f1 #798787 #798787';
		}
	}

	///// チェックボックス[表示]展開 /////
	if ( obj4 != '' )
	{
		for (i = 0; i < lay4.length; i++)
		{
			lay4[i].style.top         = initYpos2 + ( moveYpos * i );
			lay4[i].style.left        = check2Xpos;
			lay4[i].style.background  = BackColors1;
			lay4[i].style.borderColor = BorderColors2;

			lay4[24].style.background  = '#f1f1f1';
			lay4[24].style.borderColor = '#798787 #798787 #798787 #e8f0f1';
		}
	}


	///// TAB INDEX 展開 /////
	for (i = 0; i < window.PS.elements.length; i++)
	{
		window.PS.elements[i].tabindex = lngtabindex + 1;
	}


	if( typeof(WFStatus) != 'undefined' )
	{
		WFStatus.style.background  = '#f1f1f1';
		WFStatus.style.borderColor = '#798787 #e8f0f1 #798787 #798787';

		varWFStatus.style.background  = '#f1f1f1';
		varWFStatus.style.borderColor = '#798787 #798787 #798787 #e8f0f1';
	}


	return false;
}






//----------------------------------------------------------------------
// 解説 : サブウィンドウクローズ時のフォーカス適用関数
//----------------------------------------------------------------------
function fncIfrmFocusObject( strMode )
{

	switch( strMode )
	{

		case 'creation':
			document.all.strFactoryName.focus();
			break;

		case 'assembly':
			document.all.strAssemblyFactoryName.focus();
			break;

		case 'dept':
			document.all.strInChargeUserName.focus();
			break;

		case 'location':
			document.all.strDeliveryPlaceName.focus();
			break;

		case 'vi':
			document.all.strCustomerUserName.focus();
			break;

		case 'input':
			document.all.strInputUserName.focus();
			break;

		default:
			break;

	}

	return false;
}




//-->