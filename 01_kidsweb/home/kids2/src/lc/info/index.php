<?php

// ----------------------------------------------------------------------------
/**
 *       LC管理  LC情報画面
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// ■ ライブラリファイル読込
//-------------------------------------------------------------------------
// 読み込み
include 'conf.inc';
//共通ファイル読み込み
require_once '../lcModel/lcModelCommon.php';
require_once '../lcModel/db_common.php';
require_once '../lcModel/kidscore_common.php';
require_once '../lcModel/lcinfo.php';
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
$aryData["aclcinitFlg"] = $_REQUEST["aclcinitFlg"]; // T_Aclcinfo初期化フラグ

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

//ユーザーID取得(半角スペースがあるため)
$usrId = trim($objAuth->UserID);
$usrName = trim($objAuth->UserDisplayName);

//経理サブシステムDB接続
$lcModel = new lcModel();

//ユーザー権限の取得
$loginUserAuth = $lcModel->getUserAuth($usrId);

$userAuth = substr($loginUserAuth, 1, 1);

//ログイン状況の最大管理番号の取得
$maxLgno = $lcModel->getMaxLoginStateNum();
$curDate = fncGetDateTimeString();

// T_Aclcinfo初期化フラグがtrueの場合
if ($aryData["aclcinitFlg"] == "true") {
    // t_aclcinfoデータの登録・更新処理
    $curDate = fncGetDateTimeString();

    // L/Cデータを取得する
    $orderCount = fncGetLcData($objDB, $lcModel, $usrName, $curDate);

    if ($orderCount > 0) {
        // lcgetdateを更新する
        $updCount = $lcModel->updateLcGetDate($maxLgno, date('Ymd H:i:s', strtotime($curDate)));

        if ($updCount < 0) {
            $lcModel->updateLgStateToInit($maxLgno);
        }
    }

}


//LC情報取得日の取得
$lcgetdate = $lcModel->getLcInfoDate();

if( $aryData["reSearchFlg"] != true )
{
    // 新規起動時
    // ackidsのデータをkidscore2に登録
    // トランザクションを開始する
    $objDB->transactionBegin();
    // L/C情報データの削除を行う
    fncDeleteLcInfo($objDB);
    //ACL/C情報データの取得
    $acLcInfoArry = $lcModel->getAcLcInfo();
    foreach ($acLcInfoArry as $acLcInfo) {
        $data = array();
        $data["pono"] = $acLcInfo["pono"];
        $data["polineno"] = $acLcInfo["polineno"];
        $data["poreviseno"] = $acLcInfo["poreviseno"];
        $data["postate"] = $acLcInfo["postate"];
        $data["opendate"] = $acLcInfo["opendate"];
        $data["portplace"] = $acLcInfo["portplace"];
        $data["payfcd"] = $acLcInfo["payfcd"];
        $data["payfnameomit"] = $acLcInfo["payfnameomit"];
        $data["payfnameformal"] = $acLcInfo["payfnameformal"];
        $data["productcd"] = $acLcInfo["productcd"];
        $data["productrevisecd"] = $acLcInfo["productrevisecd"];
        $data["productname"] = $acLcInfo["productname"];
        $data["productnamee"] = $acLcInfo["productnamee"];
        $data["productnumber"] = $acLcInfo["productnumber"];
        $data["unitname"] = $acLcInfo["unitname"];
        $data["unitprice"] = $acLcInfo["unitprice"];
        $data["moneyprice"] = $acLcInfo["moneyprice"];
        $data["shipstartdate"] = $acLcInfo["shipstartdate"];
        $data["shipenddate"] = $acLcInfo["shipenddate"];
        $data["sumdate"] = $acLcInfo["sumdate"];
        $data["poupdatedate"] = $acLcInfo["poupdatedate"];
        $data["deliveryplace"] = $acLcInfo["deliveryplace"];
        $data["currencyclass"] = $acLcInfo["currencyclass"];
        $data["lcnote"] = $acLcInfo["lcnote"];
        $data["shipterm"] = $acLcInfo["shipterm"];
        $data["validterm"] = $acLcInfo["validterm"];
        $data["bankcd"] = $acLcInfo["bankcd"];
        $data["bankname"] = $acLcInfo["bankname"];
        $data["bankreqdate"] = $acLcInfo["bankreqdate"];
        $data["lcno"] = $acLcInfo["lcno"];
        $data["lcamopen"] = $acLcInfo["lcamopen"];
        $data["validmonth"] = $acLcInfo["validmonth"];
        $data["usancesettlement"] = $acLcInfo["usancesettlement"];
        $data["bldetail1date"] = $acLcInfo["bldetail1date"];
        $data["bldetail1money"] = $acLcInfo["bldetail1money"];
        $data["bldetail2date"] = $acLcInfo["bldetail2date"];
        $data["bldetail2money"] = $acLcInfo["bldetail2money"];
        $data["bldetail3date"] = $acLcInfo["bldetail3date"];
        $data["bldetail3money"] = $acLcInfo["bldetail3money"];
        $data["lcstate"] = $acLcInfo["lcstate"];
        $data["shipym"] = $acLcInfo["shipym"];
        $count = fncInsertLcInfo($objDB, $data);
    }

    $objDB->transactionCommit();
    // $data["from"] = "201905";
    // $data["mode"] = "0";
    // $result = fncGetLcInfoData($objDB, $data);
    // var_dump($result);
}

//行背景設定取得
$background_color = $lcModel->getBackColor();


$objDB->close();
$lcModel->close();

//HTMLへの引き渡しデータ
//$aryData["chkEpRes"] = $chkEpRes;
$aryData["lc_info_date"] = date('Y/m/d H:i:s',  strtotime($lcgetdate->lcgetdate));

$aryData["background_color_0"] = 'rgb('. $background_color[0]["lngcolorred"] . ',' .$background_color[0]["lngcolorgreen"] . ','. $background_color[0]["lngcolorblue"] . ')';
$aryData["background_color_1"] = 'rgb('. $background_color[1]["lngcolorred"] . ',' .$background_color[1]["lngcolorgreen"] . ','. $background_color[1]["lngcolorblue"] . ')';
$aryData["background_color_2"] = 'rgb('. $background_color[2]["lngcolorred"] . ',' .$background_color[2]["lngcolorgreen"] . ','. $background_color[2]["lngcolorblue"] . ')';
$aryData["background_color_3"] = 'rgb('. $background_color[3]["lngcolorred"] . ',' .$background_color[3]["lngcolorgreen"] . ','. $background_color[3]["lngcolorblue"] . ')';
$aryData["background_color_6"] = 'rgb('. $background_color[6]["lngcolorred"] . ',' .$background_color[6]["lngcolorgreen"] . ','. $background_color[6]["lngcolorblue"] . ')';
$aryData["background_color_7"] = 'rgb('. $background_color[7]["lngcolorred"] . ',' .$background_color[7]["lngcolorgreen"] . ','. $background_color[7]["lngcolorblue"] . ')';
$aryData["background_color_9"] = 'rgb('. $background_color[9]["lngcolorred"] . ',' .$background_color[9]["lngcolorgreen"] . ','. $background_color[9]["lngcolorblue"] . ')';

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("lc/info/parts.html");

// テンプレート生成
$objTemplate->replace($aryData);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

//初期処理実行
//jsへの引き渡しデータ
$arr = array(
    "chkEpRes" => $chkEpRes,
    "background_color" => $background_color,
    "userAuth" => $userAuth,
    "session_id" => $aryData["strSessionID"],
    "reSearchFlg" => $aryData["reSearchFlg"],
    "lgno" => $maxLgno,
);
echo "<script>$(function(){lcInit('" . json_encode($arr) . "');});</script>";
return true;
