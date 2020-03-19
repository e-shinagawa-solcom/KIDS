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

// 612 売上管理（売上検索）
if ( !fncCheckAuthority( DEF_FUNCTION_SC12, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 613 売上管理（納品書検索　管理モード）
if ( fncCheckAuthority( DEF_FUNCTION_SC13, $objAuth ) )
{
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
}
else
{
    $aryData["btnInvalidVisible"] = "";
}
// ヘルプ対応
$aryData["lngFunctionCode"] = DEF_FUNCTION_SC2;

// 売上区分プルダウンメニュー 生成
$aryData["lngSalesClassCode"] = "<option value=\"\"></option>\n";
$aryData["lngSalesClassCode"] .= fncGetPulldown("m_salesclass", "lngsalesclasscode", "lngsalesclasscode, strsalesclassname", "", '', $objDB);

// 消費税区分プルダウンメニュー 生成
$aryData["lngTaxClassCode"] = "<option value=\"\"></option>\n";
$aryData["lngTaxClassCode"] .= fncGetPulldown("m_taxClass", "lngTaxClassCode", "lngTaxClassCode, strtaxclassname", "", '', $objDB);


// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("search/base_search.html", "sc/search2/sc_search.tmpl", $aryData ,$objAuth );

$objDB->close();

return true;

?>

