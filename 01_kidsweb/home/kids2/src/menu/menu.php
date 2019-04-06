<?php
/**
*	メインメニュー表示　処理
*
*	メインメニュー画面の表示処理
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/
*	@copyright Copyright &copy; 2003, Wiseknot
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp>
*	@access    public
*	@version   1.00
*
*	処理概要
*	メインメニュー画面の表示処理
*
*/

// メインメニュー画面
// index.php -> strSessionID    -> index.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require( SRC_ROOT. "menu/cmn/lib_submenu.php" );


$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData["strSessionID"]    = $_GET["strSessionID"];
setcookie("lngLanguageCode", 1,0,"/");

// 文字列チェック
$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認（メインメニュー画面）
if ( !fncCheckAuthority( DEF_FUNCTION_MENU0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", FALSE, "", $objDB );
}

// 商品管理メニュー
if ( fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
{
	$aryData["Pnavi_visibility"] = "visible";
}
else
{
	$aryData["Pnavi_visibility"] = "hidden";
}
// 受注管理メニュー
if ( fncCheckAuthority( DEF_FUNCTION_SO0, $objAuth ) )
{
	$aryData["SOnavi_visibility"] = "visible";
}
else
{
	$aryData["SOnavi_visibility"] = "hidden";
}
// 発注管理メニュー
if ( fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
{
	$aryData["POnavi_visibility"] = "visible";
}
else
{
	$aryData["POnavi_visibility"] = "hidden";
}
// 売上管理メニュー
if ( fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
{
	$aryData["SCnavi_visibility"] = "visible";
}
else
{
	$aryData["SCnavi_visibility"] = "hidden";
}
// 仕入管理メニュー
if ( fncCheckAuthority( DEF_FUNCTION_PC0, $objAuth ) )
{
	$aryData["PCnavi_visibility"] = "visible";
}
else
{
	$aryData["PCnavi_visibility"] = "hidden";
}
// ワークフロー管理メニュー
if ( fncCheckAuthority( DEF_FUNCTION_WF0, $objAuth ) )
{
	$aryData["WFnavi_visibility"] = "visible";
}
else
{
	$aryData["WFnavi_visibility"] = "hidden";
}
// 帳票出力メニュー
if ( fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	$aryData["LOnavi_visibility"] = "visible";
}
else
{
	$aryData["LOnavi_visibility"] = "hidden";
}
// データエクスポートメニュー
if ( fncCheckAuthority( DEF_FUNCTION_DE0, $objAuth ) )
{
	$aryData["DEnavi_visibility"] = "visible";
}
else
{
	$aryData["DEnavi_visibility"] = "hidden";
}


// アップロードメニュー
if ( fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
{
	$aryData["UPLOADnavi_visibility"] = "visible";
}
else
{
	$aryData["UPLOADnavi_visibility"] = "hidden";
}


// ユーザー管理メニュー
if ( fncCheckAuthority( DEF_FUNCTION_UC0, $objAuth ) )
{
	$aryData["UCnavi_visibility"] = "";
}
else
{
	$aryData["UCnavi_visibility"] = "none";
}
// マスター管理メニュー
if ( fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	$aryData["Mnavi_visibility"] = "";
}
else
{
	$aryData["Mnavi_visibility"] = "none";
}

// システム管理メニュー
if ( fncCheckAuthority( DEF_FUNCTION_SYS0, $objAuth ) )
{
	$aryData["SYSnavi_visibility"] = "";
}
else
{
	$aryData["SYSnavi_visibility"] = "none";
}

// 締め処理メニュー
if ( fncCheckAuthority( DEF_FUNCTION_CLD0, $objAuth ) )
{
	$aryData["DATACLOSEDnavi_visibility"] = "";
}
else
{
	$aryData["DATACLOSEDnavi_visibility"] = "none";
}

// 見積もり原価計算メニュー
if ( fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) )
{
	$aryData["Enavi_visibility"] = "visible";
}
else
{
	$aryData["Enavi_visibility"] = "hidden";
}


// 金型管理
if ( fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth))
{
	$aryData["MMnavi_visibility"] = "visible";
}
else
{
	$aryData["MMnavi_visibility"] = "hidden";
}

// 金型帳票管理
if ( fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth))
{
	$aryData["MRnavi_visibility"] = "visible";
}
else
{
	$aryData["MRnavi_visibility"] = "hidden";
}
// L/C管理
if ( fncCheckAuthority( DEF_FUNCTION_LC0, $objAuth))
{
	$aryData["LCnavi_visibility"] = "visible";
}
else
{
	$aryData["LCnavi_visibility"] = "hidden";
}

	// サブメニュー生成
	$aryData = fncSetSubMenu( $aryData, $objAuth, $objDB );



$aryData["strSessionID"] = $objAuth->SessionID;

// 現在のお知らせ記事取得
$strQuery = "SELECT strSystemInformationTitle, strSystemInformationBody FROM m_SystemInformation ORDER BY dtmInsertDate DESC LIMIT 1";
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum > 0 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$aryData["strMessageTitle"] = $objResult->strsysteminformationtitle;
	$aryData["strMessageBody"]  = $objResult->strsysteminformationbody;
}




$aryData["strSystemVersion"] = DEF_SYSTEM_VERSION;

// ヘルプ対応
$aryData["lngFunctionCode"] = DEF_FUNCTION_MENU0;

// HTML出力
echo fncGetReplacedHtml( "menu/parts.tmpl", $aryData, $objAuth );


$objDB->close();


return TRUE;
?>
