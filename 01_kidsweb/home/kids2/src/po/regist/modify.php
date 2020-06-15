<?php

// ----------------------------------------------------------------------------
/**
 *       発注管理  発注書修正画面
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
 *         ・修正時登録画面を表示
 *         ・入力エラーチェック
 *         ・登録ボタン押下後、登録確認画面へ
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 読み込み
include 'conf.inc';
require LIB_FILE;
require LIB_EXCLUSIVEFILE;
require SRC_ROOT . "po/cmn/lib_po.php";
// require(SRC_ROOT."po/cmn/lib_pop.php");
// require(SRC_ROOT."po/cmn/lib_pos1.php");
require SRC_ROOT . "po/cmn/lib_por.php";
require SRC_ROOT . "po/cmn/column.php";
require_once LIB_DEBUGFILE;

$objDB = new clsDB();
$objAuth = new clsAuth();

$aryData["strSessionID"] = $_REQUEST["strSessionID"];
$aryData["lngPurchaseOrderNo"] = $_REQUEST["lngPurchaseOrderNo"];
$aryData["lngRevisionNo"] = $_REQUEST["lngRevisionNo"];
$aryData["lngPayConditionCode"] = $_REQUEST["lngPayConditionCode"];
$aryData["strPayConditionName"] = $_REQUEST["strPayConditionName"];
$aryData["lngLocationCode"] = $_REQUEST["lngLocationCode"];
$aryData["strLocationName"] = $_REQUEST["strLocationName"];
$aryData["strNote"] = $_REQUEST["strNote"];
$aryData["strOrderCode"] = $_REQUEST["strOrderCode"];
$aryData["aryDetail"] = $_REQUEST["aryDetail"];
$aryData["lngOrderNo"] = $_REQUEST["lngOrderNo"];

//var_dump($_REQUEST);

$objDB->open("", "", "", "");

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

$lngUserCode = $objAuth->UserCode;

if ($_POST["strMode"] == "cancel") {
    // 排他ロックの解放
    $objDB->transactionBegin();
    unlockExclusive($objAuth, $objDB);
    $objDB->transactionCommit();
    return true;
}

// 500    発注管理
if (!fncCheckAuthority(DEF_FUNCTION_PO0, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 512 発注管理（発注書修正）
if (!fncCheckAuthority(DEF_FUNCTION_PO12, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// check
if ($_POST["strMode"] == "check" || $_POST["strMode"] == "renew") {
    $objDB->transactionBegin();
    // 発注書マスタ更新
    if (!fncUpdatePurchaseOrder($aryData, $objDB, $objAuth)) {return false;}

    // 発注書明細更新
    if (!fncUpdatePurchaseOrderDetail($aryData, $objDB)) {return false;}

    // 更新後のデータを再度読み込む
    $updatedPurchaseOrder = fncGetPurchaseOrderEdit($aryData["lngPurchaseOrderNo"], $aryData["lngRevisionNo"], $objDB);

    $strHtml = fncCreatePurchaseOrderUpdateHtml($updatedPurchaseOrder, $aryData["strSessionID"]);
    $aryData["aryPurchaseOrder"] = $strHtml;

    $objDB->transactionCommit();

    // テンプレート読み込み
    $objTemplate = new clsTemplate();

    header("Content-type: text/plain; charset=UTF-8");
    $objTemplate->getTemplate("po/finish/parts.tmpl");

    // テンプレート生成
    $objTemplate->replace($aryData);

    // HTML出力
    echo $objTemplate->strTemplate;

    return true;

}

// 発注書
$aryResult = fncGetPurchaseOrderEdit($aryData["lngPurchaseOrderNo"], $aryData["lngRevisionNo"], $objDB);

if (!$aryResult || count($aryResult) == 0) {
    fncOutputError(503, DEF_ERROR, "該当データの取得に失敗しました。", true, "", $objDB);
}
// ヘッダ
$aryNewResult["strOrderCode"] = $aryResult[0]["strordercode"];
$aryNewResult["lngRevisionNo"] = sprintf("%02d", $aryResult[0]["lngrevisionno"]);
$aryNewResult["dtmExpirationDate"] = $aryResult[0]["dtmexpirationdate"];
$aryNewResult["strProductCode"] = $aryResult[0]["strproductcode"];
$aryNewResult["strReviseCode"] = $aryResult[0]["strrevisecode"];
$aryNewResult["PayConditionDisabled"] = "disabled";
$aryNewResult["lngMonetaryUnitCode"] = fncGetPulldown("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitname", 0, "where lngmonetaryunitcode =" . $aryResult[0]["lngmonetaryunitcode"], $objDB);
// fncPulldownMenu(0, $aryResult[0]["lngmonetaryunitcode"], "where lngmonetaryunitcode =" . $aryResult[0]["lngmonetaryunitcode"], $objDB);
$aryNewResult["MonetaryUnitDisabled"] = "disabled";
$aryNewResult["strCustomerCode"] = $aryResult[0]["strcustomercode"];
$aryNewResult["strCustomerName"] = $aryResult[0]["strcustomername"];
$aryNewResult["strGroupDisplayCode"] = $aryResult[0]["strgroupdisplaycode"];
$aryNewResult["strGroupDisplayName"] = $aryResult[0]["strgroupdisplayname"];
$aryNewResult["strProductName"] = $aryResult[0]["strproductname"];
$aryNewResult["strProductEnglishName"] = $aryResult[0]["strproductenglishname"];
$aryNewResult["lngLocationCode"] = $aryResult[0]["strdeliveryplacecode"];
$aryNewResult["strLocationName"] = $aryResult[0]["strdeliveryplacename"];
$aryNewResult["strNote"] = $aryResult[0]["strnote"];
$aryNewResult["lngPurchaseOrderNo"] = $aryResult[0]["lngpurchaseorderno"];
$aryNewResult["lngPayConditionCodeOrign"] = $aryResult[0]["lngpayconditioncode"];
if ($aryResult[0]["lngcountrycode"] != 81) {
    $aryNewResult["inputPayCondition"] = "select";
    $aryNewResult["lngPayConditionCode"] = fncPulldownMenu(2, $aryResult[0]["lngpayconditioncode"], "", $objDB);
} else {
    $aryNewResult["inputPayCondition"] = "select";
    $aryNewResult["lngPayConditionCode"] = fncPulldownMenu(2, $aryResult[0]["lngpayconditioncode"], "where lngPayConditionCode=" . (int) $aryResult[0]["lngpayconditioncode"], $objDB);
}

// 運搬方法プルダウン（候補用）
$strPulldownDeliveryMethod = fncPulldownMenu(6, 0, "", $objDB);
// 単位プルダウン
$strPulldownProductUnit = fncPulldownMenu(7, $aryOrderHeader["lngproductunitcode"], "", $objDB);
$objDB->transactionBegin();
// 明細（候補の発注）
$aryOtherDetail = array();
if ($aryResult[0]["lngorderno"] == null) {
    $lngorderno = $aryData["lngOrderNo"];
} else {    
    $lngorderno = $aryResult[0]["lngorderno"];
}
if ($lngorderno != null) {
    $aryOtherDetail = fncGetOtherOrderDetail($lngorderno, $aryResult[0]["lngorderrevisionno"], $objDB);
}

// $aryOtherDetail = fncGetOtherOrderDetail($lngorderno, $aryResult[0]["lngorderrevisionno"], $objDB);


//if (!$aryOtherDetail || count($aryOtherDetail) == 0) {
//    fncOutputError(503, DEF_ERROR, "発注書番号に対する明細情報が見つかりません。", true, "", $objDB);
//}

// ロック（候補の発注）
foreach ($aryOtherDetail as $otherDetail) {
    if (!lockExclusive($otherDetail["lngorderno"], DEF_FUNCTION_PO5, $objAuth, $objDB)) {        
        $lngUserCode = getLockedUserID($otherDetail["lngorderno"], $objDB);
        fncOutputError(501, DEF_ERROR, "該当データが見積原価修正または発注確定でユーザーID：".$lngUserCode ."ロックされています。", true, "", $objDB);
    }
}

$objDB->transactionCommit();
// 明細表示 （候補の発注）
$aryOtherHtmlResult = fncGetOtherOrderDetailHtml($aryOtherDetail, $strPulldownDeliveryMethod, $strPulldownProductUnit, $aryData);

// 明細（発注書）
$aryHtmlResult = fncGetPurchaseOrderDetailHtml($aryResult, $objDB);

// $aryNewResult["purchaseOrderDetail"] = $aryHtmlResult["purchaseOrderDetail"];
// $aryNewResult["purchaseOrderDetailNo"] = $aryHtmlResult["purchaseOrderDetailNo"];
$aryNewResult["tableA_chkbox_body"] = $aryOtherHtmlResult["tableA_chkbox_body"];
$aryNewResult["tableA_body"] = $aryOtherHtmlResult["tableA_body"];
$aryNewResult["purchaseOrderDetail"] = $aryHtmlResult["purchaseOrderDetail"] .$aryOtherHtmlResult["tableB_body"];
$aryNewResult["purchaseOrderDetailNo"] = $aryHtmlResult["purchaseOrderDetailNo"] .$aryOtherHtmlResult["tableB_no_body"];

$objDB->close();
$objDB->freeResult($lngResultID);

$aryNewResult["strSessionID"] = $aryData["strSessionID"];
$aryNewResult["RENEW"] = true;

// ヘルプ対応
$aryNewResult["lngFunctionCode"] = DEF_FUNCTION_PO5;

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/po/regist/modify.html");

// テンプレート生成
$objTemplate->replace($aryNewResult);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

// echo fncGetReplacedHtml( "po/regist/renew.tmpl", $aryNewResult ,$objAuth );

return true;
