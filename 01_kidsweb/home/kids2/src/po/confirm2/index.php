<?php

// ----------------------------------------------------------------------------
/**
 *       発注管理  登録確認画面
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
 *         ・登録確認画面を表示
 *         ・エラーチェック
 *         ・登録ボタン押下後、登録処理へ
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 読み込み
include 'conf.inc';
require LIB_FILE;
require SRC_ROOT . "po/cmn/lib_po.php";
//2007.07.23 matsuki update start
require SRC_ROOT . "po/cmn/lib_pop.php";
//2007.07.23 matsuki update start
require SRC_ROOT . "po/cmn/lib_pos1.php";
require SRC_ROOT . "po/cmn/column.php";
require SRC_ROOT . "po/cmn/lib_por.php";
// require SRC_ROOT . "so/cmn/lib_so.php";
require_once LIB_DEBUGFILE;
require LIB_EXCLUSIVEFILE;

$objDB = new clsDB();
$objAuth = new clsAuth();

$objDB->open("", "", "", "");

$aryData["strSessionID"] = $_GET["strSessionID"];
//    aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($_POST["strSessionID"], $objAuth, $objDB);
$UserDisplayName = trim($objAuth->UserDisplayName);
$UserDisplayCode = trim($objAuth->UserID);

// ここから追加ボタン押下処理
if ($_POST["strMode"] == "update") {

    $_POST["strPayConditionName"] = $_POST["strPayConditionName"];
    $_POST["strLocationName"] = $_POST["strLocationName"];
    $_POST["strNote"] = $_POST["strNote"];

    $objDB->transactionBegin();

    // 発注書マスタロック
    if (!lockOrder($_POST["lngPurchaseOrderNo"], $objDB)) {
        fncOutputError(9051, DEF_ERROR, "発注書のロック取得に失敗しました。", true, "", $objDB);
        return false;
    }

    // 発注書更新有無チェック
    if (isPurchaseOrderModified($_POST["lngPurchaseOrderNo"], $_POST["lngRevisionNo"], $objDB)) {
        fncOutputError(9051, DEF_ERROR, "他ユーザーが発注書を更新または削除しています。", true, "", $objDB);
        return false;
    }
    // 対象の追加明細を取得
    $arrayAdd = GetAdditionalOrderDetail($_POST, $objAuth, $objDB);
    foreach ($arrayAdd as $add) {
//echo sprintf("add:lngorderno=%s,lngrevisionno=%s", $add["lngorderno"], $add["lngrevisionno"]) . "<br>";
        // 追加対象明細発注マスタ状態チェック（明細取得とロック取得の隙間に更新される可能性を考慮して）
        $errorMsg = CanOrder($add["lngorderno"], $add["lngrevisionno"], $objDB);
        if ($errorMsg) {
            fncOutputError(9051, DEF_ERROR, $errorMsg, true, "", $objDB);
            return false;
        }
        // 発注マスタ更新（追加明細のみ）
        if (!FixOrder($add["lngorderno"], $add["lngrevisionno"], $objDB)) {
            fncOutputError(9051, DEF_ERROR, "発注マスタの確定処理に失敗しました。", true, "", $objDB);
            return false;
        }
    }
    
    $aryUpdate["strProductCode"] = $_POST["strProductCode"];
    $aryUpdate["strReviseCode"] = $_POST["strReviseCode"];
    for ($i = 0; $i < count($_POST["aryDetail"]); $i++) {
        $aryUpdateDetail[$i]["lngorderdetailno"] = $_POST["aryDetail"][$i]["lngOrderDetailNo"];
        $aryUpdateDetail[$i]["lngsortkey"] = $_POST["aryDetail"][$i]["lngSortKey"];
        $aryUpdateDetail[$i]["lngdeliverymethodcode"] = $_POST["aryDetail"][$i]["lngDeliveryMethodCode"];
        $aryUpdateDetail[$i]["strdeliverymethodname"] = $_POST["aryDetail"][$i]["strDeliveryMethodName"];
        $aryUpdateDetail[$i]["lngproductunitcode"] = $_POST["aryDetail"][$i]["lngProductUnitCode"];
        $aryUpdateDetail[$i]["lngorderno"] = $_POST["aryDetail"][$i]["lngOrderNo"];
        $aryUpdateDetail[$i]["lngrevisionno"] = $_POST["aryDetail"][$i]["lngOrderRevisionNo"];
        $aryUpdateDetail[$i]["lngstocksubjectcode"] = $_POST["aryDetail"][$i]["lngStockSubjectCode"];
        $aryUpdateDetail[$i]["lngstockitemcode"] = $_POST["aryDetail"][$i]["lngStockItemCode"];
        $aryUpdateDetail[$i]["curproductprice"] = $_POST["aryDetail"][$i]["curProductPrice"];
        $aryUpdateDetail[$i]["lngproductquantity"] = $_POST["aryDetail"][$i]["lngProductQuantity"];
        $aryUpdateDetail[$i]["cursubtotalprice"] = $_POST["aryDetail"][$i]["curSubtotalPrice"];
        $aryUpdateDetail[$i]["dtmseliverydate"] = $_POST["aryDetail"][$i]["dtmDeliveryDate"];
        $aryUpdateDetail[$i]["strnote"] = $_POST["aryDetail"][$i]["strDetailNote"];
        $aryUpdateDetail[$i]["strmoldno"] = $_POST["aryDetail"][$i]["strMoldNo"];
    }

    // 発注明細更新
    if (!fncUpdateOrderDetail($aryUpdate, $aryUpdateDetail, $objDB)) {return false;}

    // 対象の削除明細を取得
    $arrayDel = GetRemovalOrderDetail($_POST, $objAuth, $objDB);

    if (!is_array($arrayDel)) {
        $arrayDel[] = $arrayDel;
    }
    foreach ($arrayDel as $del) {
        // 削除対象明細発注マスタ状態チェック（明細削除対応）
        $errorMsg = CanDeletePurchaseOrderDetail($del["lngorderno"], $del["lngrevisionno"], $objDB);
        if ($errorMsg) {
            fncOutputError(9051, DEF_ERROR, $errorMsg, true, "", $objDB);
            return false;
        }
        // 削除対象明細発注マスタ状態リセット（明細削除対応）
        if (!CancelOrder($del["lngorderno"], $del["lngrevisionno"], $objDB)) {
            fncOutputError(9051, DEF_ERROR, "発注マスタの取消処理に失敗しました。", true, "", $objDB);
            return false;
        }
    }

    // 発注書マスタ更新
    if (!fncUpdatePurchaseOrder($_POST, $objDB, $objAuth)) {return false;}
    // 発注書明細登録（追加分も含めて）
    if (!fncUpdatePurchaseOrderDetail($_POST, $objDB)) {return false;}

    // 排他制御ロック解放
    $result = unlockExclusive($objAuth, $objDB);

    $objDB->transactionCommit();

    // 更新後のデータを再度読み込む
    $updatedPurchaseOrder = fncGetPurchaseOrderEdit($_POST["lngPurchaseOrderNo"], intval($_POST["lngRevisionNo"]) + 1, $objDB);

    $strHtml = fncCreatePurchaseOrderUpdateHtml($updatedPurchaseOrder, $aryData["strSessionID"]);
    $aryData["aryPurchaseOrder"] = $strHtml;

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
// 追加ボタン押下処理ここまで

// 更新データ取得
$aryData["lngCustomerCompanyCode"] = $_POST["lngCustomerCompanyCode"];
$aryData["lngOrderNo"] = $_POST["lngOrderNo"];
$aryData["lngPurchaseOrderNo"] = $_POST["lngPurchaseOrderNo"];
$aryData["lngRevisionNo"] = $_POST["lngRevisionNo"];
$aryData["lngPayConditionCode"] = $_POST["lngPayConditionCode"];
$aryData["strPayConditionName"] = $_POST["strPayConditionName"];
$aryData["strLocationName"] = $_POST["strLocationName"];
$aryData["strNote"] = $_POST["strNote"];
$aryData["lngMonetaryUnitCode"] = $_POST["lngMonetaryUnitCode"];
$aryData["strmonetaryunitname"] = $_POST["strMonetaryUnitName"];
$aryData["aryDetail"] = $_POST["aryDetail"];
$aryData["payConditionDisableFlag"] = $_POST["payConditionDisableFlag"];
// 明細行を除く
for ($i = 0; $i < count($_POST); $i++) {
    list($strKeys, $strValues) = each($_POST);
    if ($strKeys != "aryDetail") {
        $aryData[$strKeys] = $strValues;
    }
}
// 明細行処理 ===========================================================================================
// 金型NOの生成
for ($i = 0; $i < count($_POST["aryDetail"]); $i++) {
    if ($_POST["aryDetail"][$i]["strMoldNo"] == "") {
        $strmoldno = fncGetMoldNo( 
            $_POST["strProductCode"], 
            $_POST["strReviseCode"], 
            $_POST["aryDetail"][$i]["lngStockSubjectCode"], 
            $_POST["aryDetail"][$i]["lngStockItemCode"],
            $objDB
        );
        $_POST["aryDetail"][$i]["strMoldNo"] = $strmoldno;
    }
}
// 明細行のhidden生成
if (is_array($_POST["aryDetail"])) {
    $aryData["strDetailHidden"] = fncDetailHidden($_POST["aryDetail"], "insert", $objDB);
}

$aryTytle = $aryTableTytle;
// カラム名の設定
$aryHeadColumnNames = fncSetPurchaseTabelName($aryTableViewHead, $aryTytle);
$aryDetailColumnNames = fncSetPurchaseTabelName($aryTableViewDetail, $aryTytle);

$allPrice = 0;
$aryData["lngDetailCount"] = count($_POST["aryDetail"]);
for ($i = 0; $i < count($_POST["aryDetail"]); $i++) {

    $_POST["aryDetail"][$i]["lngrecordno"] = $i + 1;

    // 仕入科目
    $_POST["aryDetail"][$i]["strStockSubjectName"] = fncGetMasterValue("m_stocksubject", "lngstocksubjectcode", "strstocksubjectname", $_POST["aryDetail"][$i]["lngStockSubjectCode"], '', $objDB);
    // 仕入部品
    $_POST["aryDetail"][$i]["strStockItemName"] = fncGetMasterValue("m_stockitem", "lngstockitemcode", "strstockitemname", $_POST["aryDetail"][$i]["lngStockItemCode"], "lngstocksubjectcode = " . $_POST["aryDetail"][$i]["lngStockSubjectCode"], $objDB);
    // 運搬方法
    $_POST["aryDetail"][$i]["strCarrierName"] = fncGetMasterValue("m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $_POST["aryDetail"][$i]["lngDeliveryMethodCode"], '', $objDB);

    $_POST["aryDetail"][$i]["dtmdeliverydate"] = $_POST["aryDetail"][$i]["dtmDeliveryDate"];

    $_POST["aryDetail"][$i]["strDetailNote"] = $_POST["aryDetail"][$i]["strDetailNote"];
    // 単価
    $aryData["strMonetarySign"] = fncGetMasterValue("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $aryData["lngMonetaryUnitCode"], '', $objDB);

    $_POST["aryDetail"][$i]["curproductprice_DIS"] = convertPrice($aryData["lngMonetaryUnitCode"], $aryData["strMonetarySign"], str_replace(",", "", $_POST["aryDetail"][$i]["curProductPrice"]), 'unitprice');
    $_POST["aryDetail"][$i]["lnggoodsquantity_DIS"] = $_POST["aryDetail"][$i]["lngProductQuantity"];
    $_POST["aryDetail"][$i]["curtotalprice_DIS"] = convertPrice($aryData["lngMonetaryUnitCode"], $aryData["strMonetarySign"], str_replace(",", "", $_POST["aryDetail"][$i]["curSubtotalPrice"]), 'price');
    $allPrice = $allPrice + (double) (str_replace(",", "", $_POST["aryDetail"][$i]["curSubtotalPrice"]));
    $_POST["aryDetail"][$i]["strproductcode_DISCODE"] = ($_POST["strProductCode"] != "") ? "[" . $_POST["strProductCode"] . "]" : "";
    $_POST["aryDetail"][$i]["strproductname"] = $_POST["strProductName"];
    $_POST["aryDetail"][$i]["strstockitemcode_DISCODE"] = ($_POST["aryDetail"][$i]["lngStockItemCode"] != "") ? "[" . $_POST["aryDetail"][$i]["lngStockItemCode"] . "]" : "";
    $_POST["aryDetail"][$i]["strstocksubjectcode_DISCODE"] = ($_POST["aryDetail"][$i]["lngStockSubjectCode"] != "") ? "[" . $_POST["aryDetail"][$i]["lngStockSubjectCode"] . "]" : "";
    $_POST["aryDetail"][$i]["lngStockItemCode"] = $_POST["aryDetail"][$i]["lngStockItemCode"];
    // テンプレート読み込み
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("po/confirm/parts_detail.tmpl");

    // テンプレート生成
    $objTemplate->replace($aryDetailColumnNames);
    $objTemplate->replace($_POST["aryDetail"][$i]);
    $objTemplate->complete();

    // HTML出力
    $aryDetailTable[] = $objTemplate->strTemplate;
}
$aryData["curAllTotalPrice"] = $allPrice;
// exit();

$aryData["lngOrderNo"] = $_POST["lngOrderNo"];

$aryData["strDetailTable"] = implode("\n", $aryDetailTable);

$aryData["strMode"] = "update";

// 登録日
$aryData["dtminsertdate"] = date('Y/m/d', time());

// 入力者
$aryData["lngInputUserCode"] = $UserDisplayCode;
$aryData["strInputUserName"] = $UserDisplayName;
// 支払条件
$strPayConditionName = fncGetMasterValue("m_paycondition", "lngpayconditioncode", "strpayconditionname", $_POST["lngPayConditionCode"], '', $objDB);
// あえてハイフン消すの？
$aryData["strPayConditionName"] = ($strPayConditionName == "−") ? "" : $strPayConditionName;

// 納品場所
$aryData["strLocationName"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "strcompanydisplayname", $_POST["lngLocationCode"] . ":str", '', $objDB);

// 状態
$aryData["strAction"] = "/po/confirm2/index.php?strSessionID=" . $_POST["strSessionID"];

//ヘッダ備考の特殊文字変換
$aryData["strNote"] = fncHTMLSpecialChars($_POST["strNote"]);

// 通貨記号+合計金額

$aryData["curAllTotalPrice_DIS"] = convertPrice($aryData["lngMonetaryUnitCode"], $aryData["strMonetarySign"], $aryData["curAllTotalPrice"], "price"); // 合計金額

// コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）

// 入力者
$aryData["lngInputUserCode_DISCODE"] = ($aryData["lngInputUserCode"] != "") ? "[" . $aryData["lngInputUserCode"] . "]" : "";
// 仕入先
//$aryData["strCustomerCode"] = $aryData["lngCustomerCompanyCode"];
$aryData["strCustomerName"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "strcompanydisplayname", $aryData["lngCustomerCompanyCode"] . ":str", '', $objDB);
$aryData["lngCustomerCode_DISCODE"] = ($aryData["lngCustomerCompanyCode"] != "") ? "[" . $aryData["lngCustomerCompanyCode"] . "]" : "";

// 納品場所
$aryData["lngLocationCode_DISCODE"] = ($aryData["lngLocationCode"] != "") ? "[" . $aryData["lngLocationCode"] . "]" : "";

// watanabe update end

// 支払条件整合性チェック
$aryData["lngMonetaryUnitCode"] = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryData["lngMonetaryUnitCode"] . ":str", '', $objDB);
//$aryData["lngCustomerCode"] = $aryData["lngCustomerCompanyCode"];
$aryData["lngCustomerCode"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngCustomerCompanyCode"] . ":str", '', $objDB);
//$aryData["lngCustomerCode"] = fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode",  $aryData["lngCustomerCompanyCode"], '', $objDB);
if ($aryData["payConditionDisableFlag"] == 'false') {
    $aryData = fncPayConditionCodeMatch($aryData, $aryHeadColumnNames, $_POST["aryDetail"], $objDB);
}

$objDB->close();

// テンプレート読み込み
$objTemplate = new clsTemplate();

$objTemplate->getTemplate("po/confirm2/parts.tmpl");

// テンプレート生成

$objTemplate->replace($aryHeadColumnNames);
$objTemplate->replace($aryData);
$objTemplate->complete();

// HTML出力 明細行は_%strDetailTable%_で受け渡し
echo $objTemplate->strTemplate;

return true;
