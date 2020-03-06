<?php

// ----------------------------------------------------------------------------
/**
 *       LC管理  LC帳票出力画面
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// ■ ライブラリファイル読込
//-------------------------------------------------------------------------
// 読み込み
include 'conf.inc';
//共通ファイル読み込み
require_once '../lcModel/lcModelCommon.php';
//クラスファイルの読み込み
require_once '../lcModel/db_common.php';
require LIB_FILE;

//-------------------------------------------------------------------------
// ■ オブジェクト生成
//-------------------------------------------------------------------------
$objDB = new clsDB();
$objAuth = new clsAuth();

//-------------------------------------------------------------------------
// ■ DBオープン
//-------------------------------------------------------------------------
$objDB->open("", "", "", "");
//LC用DB接続インスタンス生成
$db = new lcConnect();

//-------------------------------------------------------------------------
// ■ パラメータ取得
//-------------------------------------------------------------------------
$aryData = $_POST;

$aryData["strSessionID"] = $_REQUEST["strSessionID"]; // セッションID
$aryData["lngLanguageCode"] = 1; // 言語コード

// setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// セッション確認
// $objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// // 2100 LC管理
// if ( !fncCheckAuthority( DEF_FUNCTION_LC0, $objAuth ) )
// {
//         fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
// }

// // 2101 LC情報
// if ( !fncCheckAuthority( DEF_FUNCTION_LC1, $objAuth ) )
// {
//         fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
// }

$objDB->close();

//経理サブシステムDB接続
$lcModel = new lcModel();

//ユーザー権限の取得
$login_user_auth = $lcModel->getUserAuth($user_id);

//ログイン状況の最大管理番号の取得
$maxLgno = $lcModel->getMaxLoginStateNum();

//排他制御
$chkEpRes = $lcModel->chkEp($maxLgno, substr($login_user_auth["usrauth"], 1, 1), $user_id);

//ログイン者の有無
$loginCount = $lcModel->getUserCount();

// echo fncGetReplacedHtmlWithBase("lc/base_lc.html", "lc/report/parts.html", $aryData ,$objAuth );

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("lc/report/parts.html");

// テンプレート生成
$objTemplate->replace($aryData);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

//初期処理実行
//jsへの引き渡しデータ
$arr = array(
    "chkEpRes" => $chkEpRes,
    "userAuth" => substr($login_user_auth["usrauth"], 1, 1),
    "session_id" => $aryData["strSessionID"],
    "openDate" => $_REQUEST["openDate"],
);
echo "<script>$(function(){lcInit('" . json_encode($arr) . "');});</script>";

return true;
