<!--

var g_DialogLoadFlag = false;

if(window.dialogArguments){
	var g_aryArgs = window.dialogArguments;
}
else if(window.opener){
	var g_aryArgs = window.opener.args;
}
else{
	var g_aryArgs = window.parent.opener.args;
}

var g_ActionScriptName = ''; // フォームアクション値格納グローバル変数


var g_aryEditData = new Array(); // 編集済みマスタデータ格納用グローバル変数
var g_EditCnt = 0;               // 編集フラグ値グローバル変数


function fncIframe( obj1 , obj2 )
{

// alert(g_aryArgs.join("\r\n"));

	// Iframe 生成
	obj1.innerHTML = '<iframe id="' + g_aryArgs[0][1] + '" name="Rwin" scrolling="' + g_aryArgs[0][2] + '" src="' + g_aryArgs[0][0] + '"></iframe>';

	if(typeof(obj2) != 'undefined' )
	{
		// lngLanguageCode 代入
		obj2.value = g_aryArgs[0][3];
	}

}



function fncGetActionScriptName()
{

	//alert(g_aryArgs[3][0] + '  ' + g_aryArgs[4][0]);

	if( typeof(g_aryArgs[3]) == 'undefined' ) return false;

	for( i = 0; i < g_aryArgs[3].length; i++ )
	{
		if( g_aryArgs[3][i] == 'ActionScriptName' )
		{
			g_ActionScriptName = g_aryArgs[4][i];
			break;
		}
	}

	if( g_ActionScriptName == '' )
	{
		g_ActionScriptName = 'http://www.kuwagata.co.jp/';
	}

	return g_ActionScriptName;
}






//--------------------------------------------------------------------------------------------------------------------
// 概要     :
//
// ﾊﾟﾗﾒｰﾀｰ  :
//
// 解説     :
//
// 外部関数 :
//--------------------------------------------------------------------------------------------------------------------
function fncGetActionScriptNameforRegistration()
{

	if( typeof(g_aryArgs[1]) == 'undefined' ) return false;

	for( i = 0; i < g_aryArgs[1].length; i++ )
	{
		if( g_aryArgs[1][i] == 'ActionScriptName' )
		{
			g_ActionScriptName = g_aryArgs[2][i];
			break;
		}
	}

	return g_ActionScriptName;
}







/************************************************************/

var g_offY = 0;

function fncStopHeader( ObjID )
{

	sy = document.body.scrollTop;

	//ObjID.style.visibility = 'hidden';

	ObjID.style.top = sy + g_offY;

	//ObjID.style.visibility = 'visible';

}
/************************************************************/

/************************************************************/
var g_clickID;                  // 前回のクリック行
var g_lngTrNum;                 // 前回のTRの数
var g_DefaultColor = '';        // 前回のクリック行の色
var g_SelectColor  = '#bbbbbb'; // 選択したときの色
var trClickFlg     = "on";      // クリックカウントフラグ

/************************************************************/
// objID : 		クリック行のオブジェクト

////////// TR選択行の背景色変更関数 //////////
function fncSelectTrColor( objID )
{

	// trClickFlgがoffのときは処理を終了
	if( trClickFlg == "off" ) return false;

	// 選択行すでにある場合に、以前の色に戻す
	if( g_DefaultColor != '')
	{
		g_clickID.style.background = g_DefaultColor;
	}

	// 以前と同じ選択行を選択した場合に、初期化
	if(g_clickID == objID){
		g_clickID      = '';
		g_DefaultColor = '';
	}
	// 選択行の色と場所を保存し、反転
	else
	{
		g_clickID      = objID;
		g_DefaultColor = objID.style.background;
		// 選択行を反転
		objID.style.background = g_SelectColor;
	}

	return false;
}


/************************************************************/
// objID : 		クリック行のオブジェクト
// strIdName : 	TRの数値部分は除くID名( tda0 → 'tda' )
// lngTrNum : 	１レコードに使用しているTRの数( rowspanの数 )

////////// TR選択行の背景色変更関数[複数行対応版] //////////
function fncSelectSomeTrColor( objID , strIdName , lngTrNum )
{

	// trClickFlgがoffのときは処理を終了
	if( trClickFlg == "off" ) return false;


	// 選択行すでにある場合に、以前の色に戻す
	if( g_DefaultColor != '')
	{
		for ( i = 0; i < g_lngTrNum; i++ )
		{
			document.getElementById( g_clickID + i ).style.background = g_DefaultColor;
		}
	}

	// 以前と同じ選択行を選択した場合に、初期化
	if( g_clickID == strIdName )
	{
		g_clickID      = '';
		g_lngTrNum     = '';
		g_DefaultColor = '';
	}

	// 選択行の色と場所を保存し、反転
	else
	{
		g_clickID      = strIdName;
		g_lngTrNum     = lngTrNum;
		g_DefaultColor = objID.style.background;

		for ( i = 0; i < g_lngTrNum; i++ )
		{
			document.getElementById( g_clickID + i ).style.background = g_SelectColor;
		}
	}


	return false;
}
/*****************************************************************************/





function fncNoSelectSomeTrColor( objID , strIdName , lngTrNum )
{

	return false;

}





function SortOn( obj )
{
	obj.style.background = '#bbbbbb'; //6d8aab
}

function SortOff( obj )
{
	obj.style.backgroundColor = '#799797';
}







function fncSort( strSortValue,lngSortCount )
{
//alert(strSortValue);

	if( ( lngSortCount % 2) == 0 )
	{
		lngSortCount = 1;
	}
	else
	{
		lngSortCount = 0;
	}
//alert(lngSortCount);
	document.form1.strSort.value = strSortValue;
	document.form1.lngSortNumber.value = lngSortCount
	document.form1.submit();

	return false;
}

function fncSort2( strSortValue, strSortOrder )
{
	document.form1.strSort.value = strSortValue;
	document.form1.strSortOrder.value = strSortOrder;
	document.form1.submit();

	return false;
}

function fncSubwin( code )
{
//alert(code);
	window.open( code ,"","width=1000,height=670,scrollbars=yes,toolbar=no");
}




//------------------------------------------------
// 解説 : 検索結果画面用プリロード表示非表示関数
//------------------------------------------------
function fncShowHidePreload( strMode )
{
	// 表示
	if( strMode == 0 )
	{
		parent.Preload.style.visibility = 'visible';
	}

	// 非表示
	else if( strMode == 1 )
	{
		parent.Preload.style.visibility = 'hidden';
	}

	return false;
}



//-----------------------------------------------------
// 概要    : オブジェクトのリサイズ処理関数
//
// ﾊﾟﾗﾒｰﾀｰ : [objId]   . オブジェクトID
//           [lngWidth]  . WIDTH微調整値
//           [lngHeight] . HEIGHT微調整値
//
// 解説    : ウィンドウ枠内表示可能領域サイズから
//           オブジェクトのwidth,height値に代入。
//
// ｲﾍﾞﾝﾄ   : body   . [onload],[onresize]
//           iframe . [onload]
//-----------------------------------------------------
function fncObjectResize( objId , lngWidth , lngHeight )
{

	// ウィンドウ枠内表示可能領域の取得
	var winH = document.body.offsetHeight;
	var winW = document.body.offsetWidth;

	// リサイズ - 微調整値
	objId.style.width  = winW - lngWidth;
	objId.style.height = winH - lngHeight;

	return false;
}
//-->