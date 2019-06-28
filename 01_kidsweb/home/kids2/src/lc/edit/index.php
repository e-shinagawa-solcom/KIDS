<?php

// ----------------------------------------------------------------------------
/**
 *       LC管理  LC編集画面
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// ■ ライブラリファイル読込
//-------------------------------------------------------------------------
include 'conf.inc';
//共通ファイル読み込み
require_once '../lcModel/lcModelCommon.php';
//クラスファイルの読み込み
require_once '../lcModel/db_common.php';
require_once '../lcModel/kidscore_common.php';
require LIB_FILE;

//-------------------------------------------------------------------------
// ■ オブジェクト生成
//-------------------------------------------------------------------------
$objDB = new clsDB();
$objAuth = new clsAuth();
//LC用DB接続インスタンス生成
$db = new lcConnect();

//-------------------------------------------------------------------------
// ■ DBオープン
//-------------------------------------------------------------------------
$objDB->open("", "", "", "");

//-------------------------------------------------------------------------
// ■ パラメータ取得
//-------------------------------------------------------------------------
$aryData = $_GET;

$aryData["strSessionID"] = $_REQUEST["strSessionID"]; // セッションID

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);


//ユーザーID取得(半角スペースがあるため)
$usrId = trim($objAuth->UserID);

// // 2100 LC管理
// if ( !fncCheckAuthority( DEF_FUNCTION_LC0, $objAuth ) )
// {
//         fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
// }

// // 2101 LC編集
// if ( !fncCheckAuthority( DEF_FUNCTION_LC2, $objAuth ) )
// {
//         fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
// }

$objDB->close();

//経理サブシステムDB接続
$lcModel = new lcModel();

//ログイン状況判定処理
$logined_flg = false;
$login_state = $lcModel->getLoginState($usrId);

//ユーザー権限の取得
$loginUserAuth = $lcModel->getUserAuth($usrId);

$userAuth = substr($loginUserAuth, 1, 1);

//HTMLへの引き渡しデータ
$aryData["login_state"] = $login_state;

echo fncGetReplacedHtmlWithBase("lc/base_lc.html", "lc/edit/parts.tmpl", $aryData, $objAuth);

//初期処理実行
//jsへの引き渡しデータ
$arr = array(
    "login_state" => $login_state,
    "session_id" => $aryData["strSessionID"],
    "userAuth" => $userAuth,
    "pono" => $_REQUEST["pono"],
    "poreviseno" => $_REQUEST["poreviseno"],
    "polineno" => $_REQUEST["polineno"],
);
mb_convert_variables('UTF-8', 'EUC-JP', $arr);
echo "<script>$(function(){lcInit('" . json_encode($arr) . "');});</script>";
return true;
