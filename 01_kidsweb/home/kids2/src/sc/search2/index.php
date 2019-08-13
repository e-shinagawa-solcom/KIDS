<?php

// ----------------------------------------------------------------------------
/**
*       納品書検索画面
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       処理概要
*         ・納品書データ検索条件入力
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定の読み込み
include_once ( "conf.inc" );

// ライブラリ読み込み
require ( LIB_FILE );

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// 文字列チェック
$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 602 売上管理（売上検索）
if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 文字列チェック
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ヘルプ対応
$aryData["lngFunctionCode"] = DEF_FUNCTION_SC2;

// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("search2/base_search.html", "sc/search2/sc_search.tmpl", $aryData ,$objAuth );

$objDB->close();

return true;

?>

