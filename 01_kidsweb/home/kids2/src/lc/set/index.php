<?php

// ----------------------------------------------------------------------------
/**
 *       LC管理  LC設定変更画面
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
$aryData = $_POST;

$aryData["strSessionID"] = $_REQUEST["strSessionID"]; // セッションID
$aryData["lngLanguageCode"] = 1; // 言語コード

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

//ユーザーID取得(半角スペースがあるため)
$usrId = trim($objAuth->UserID);

// //ユーザーID取得(半角スペースがあるため)
// $user_id = trim($objAuth->UserID);

// 2100 LC管理
// if ( !fncCheckAuthority( DEF_FUNCTION_LC0, $objAuth ) )
// {
//         fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
// }

// // 2102 LC設定変更
// if ( !fncCheckAuthority( DEF_FUNCTION_LC2, $objAuth ) )
// {
//         fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
// }

//経理サブシステムDB接続
$lcModel = new lcModel();

//ログイン状況の最大管理番号の取得
$maxLgno = $lcModel->getMaxLoginStateNum();

// ログアウト時刻の取得
$acloginstate = $lcModel->getAcLoginstateBylgno($maxLgno);
$lgoutymd = $acloginstate->lgoutymd;

//ユーザー権限の取得
$loginUserAuth = $lcModel->getUserAuth($usrId);

$userAuth = substr($loginUserAuth, 1, 1);

//ログイン状況判定処理
$logined_flg = false;
$loginState = $lcModel->getLoginState($usrId);

$objDB->transactionBegin();
// ackidsのデータをkidscore2に登録
// ackidsの銀行情報の取得
$bankArry = $lcModel->getBankInfo();
// kidscore2の銀行情報の削除
$deltedNum = fncDeleteBank($objDB);
if ($deltedNum >= 0) {
    // kidscore2の銀行情報の登録
    if (count($bankArry) > 0) {
        foreach ($bankArry as $bank) {
            fncInsertBank($objDB, $bank);
        }
    }
}

// ackidsの支払先情報の取得
$payfArry = $lcModel->getPayfInfo();
// kidscore2の支払先情報の削除
$deltedNum = fncDeletePayfinfo($objDB);
if ($deltedNum >= 0) {
// kidscore2の支払先情報の登録
    if (count($payfArry) > 0) {
        foreach ($payfArry as $payf) {
            fncInsertPayf($objDB, $payf);
        }
    }
}

$objDB->transactionCommit();
$objDB->close();
$lcModel->close();

//HTMLへの引き渡しデータ
$aryData["login_state"] = $loginState;

echo fncGetReplacedHtmlWithBase("lc/base_lc.html", "lc/set/parts.tmpl", $aryData, $objAuth);

//初期処理実行
//jsへの引き渡しデータ
$arr = array(
    "login_state" => $loginState,
    "session_id" => $aryData["strSessionID"],
    "lgoutymd" => $lgoutymd,
    "userAuth" => $userAuth,
);
mb_convert_variables('UTF-8', 'EUC-JP', $arr);
echo "<script>$(function(){lcInit('" . json_encode($arr) . "');});</script>";
return true;
