<?php

// ----------------------------------------------------------------------------
/**
 *       売上管理  検索画面
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
// 602 売上管理（売上検索）
if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
{
    fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 603 売上管理（売上検索　管理モード）
if ( fncCheckAuthority( DEF_FUNCTION_SC3, $objAuth ) )
{
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
    // 607 売上管理（無効化）
    if ( fncCheckAuthority( DEF_FUNCTION_SC7, $objAuth ) )
    {
        $aryData["btnInvalid_visibility"] = 'style="visibility: visible"';
        $aryData["btnInvalidVisible"] = "disabled";
    }
    else
    {
        $aryData["btnInvalid_visibility"] = 'style="visibility: hidden"';
        $aryData["btnInvalidVisible"] = "disabled";
    }
}
else
{
    $aryData["AdminSet_visibility"] = 'style="visibility: hidden"';
    $aryData["btnInvalid_visibility"] = 'style="visibility: hidden"';
    $aryData["btnInvalidVisible"] = "";
}
// 604 売上管理（詳細表示）
if ( fncCheckAuthority( DEF_FUNCTION_SC4, $objAuth ) )
{
    $aryData["btnDetail_visibility"] = 'style="visibility: visible"';
    $aryData["btnDetailVisible"] = "checked";
}
else
{
    $aryData["btnDetail_visibility"] = 'style="visibility: hidden"';
    $aryData["btnDetailVisible"] = "";
}


// 文字列チェック
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// 売上ステータス
$aryData["lngSalesStatusCode"] 		= fncGetCheckBoxObject( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", "lngSalesStatusCode[]", 'where lngSalesStatusCode not in (1,2,3)', $objDB );

// 売上区分
$aryData["lngSalesClassCode"]		= fncGetPulldown( "m_salesclass", "lngsalesclasscode", "lngsalesclasscode, strsalesclassname", 1, '', $objDB );

//　プルダウンリストの取得に失敗した場合エラー表示
if ( !$aryData["lngSalesStatusCode"] or !$aryData["lngSalesClassCode"] )
{
    fncOutputError ( 9055, DEF_WARNING, "システム管理者にお問い合わせ下さい。", TRUE, "", $objDB );
}

// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("search/base_search.html", "sc/search/sc_search.html", $aryData, $objAuth);

$objDB->close();

return true;
