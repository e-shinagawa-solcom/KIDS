<?php

// ----------------------------------------------------------------------------
/**
 *       商品管理  検索画面
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
 *         ・検索画面表示処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 設定の読み込み
include_once "conf.inc";

// ライブラリ読み込み
require LIB_FILE;
require( "libsql.php" );

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
if ($_POST) {
    $aryData = $_POST;
} elseif ($_GET) {
    $aryData = $_GET;
}

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 権限確認
if (!fncCheckAuthority(DEF_FUNCTION_P0, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 302 商品管理（商品検索）
if (!fncCheckAuthority(DEF_FUNCTION_P2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}


// 303 商品管理（商品検索）　管理モード）
if ( fncCheckAuthority( DEF_FUNCTION_P3, $objAuth ) )
{
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
}
else
{
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
}
// 304 商品管理（詳細表示）
if (fncCheckAuthority(DEF_FUNCTION_P4, $objAuth)) {
    $aryData["btnDetail_visibility"] = "visible";
    $aryData["btnDetailVisible"] = "checked";
} else {
    $aryData["btnDetail_visibility"] = "hidden";
    $aryData["btnDetailVisible"] = "";
}
// 部門
$aryData["lngInChargeGroupCodeSelect"]	= fncGetPulldown( "m_group", "lnggroupcode", "strgroupdisplaycode || ' ' || strgroupdisplayname as strgroupdisplayname", 0,'WHERE bytgroupdisplayflag = true and lngcompanycode in (0,1)', $objDB );
// カテゴリー
$lngUserCode = $objAuth->UserCode;
$aryData["lngCategoryCode"]				= fncGetPulldownQueryExec( fncSqlqueryCategory2(array(0=>$lngUserCode)), $aryData["lngCategoryCode"], $objDB, 2);
// 企画進行状況
$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", 0,'', $objDB );
// 証紙
$aryData["lngCertificateClassCode"]		= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", 0, '', $objDB );
// 版権元
$aryData["lngCopyrightCode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", 0, '', $objDB );

//　プルダウンリストの取得に失敗した場合エラー表示
if ( !$aryData["lngGoodsPlanProgressCode"] or !$aryData["lngCertificateClassCode"] or !$aryData["lngCopyrightCode"] )
{
	fncOutputError ( 9055, DEF_WARNING, "システム管理者にお問い合わせ下さい。", TRUE, "", $objDB );
}
// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("search/base_search.html", "p/search/p_search.html", $aryData, $objAuth);

$objDB->close();

return true;
