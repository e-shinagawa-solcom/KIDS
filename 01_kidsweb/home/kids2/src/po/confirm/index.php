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
require_once LIB_DEBUGFILE;
require_once LIB_EXCLUSIVEFILE;
require SRC_ROOT . "po/cmn/lib_po.php";
//2007.07.23 matsuki update start
require SRC_ROOT . "po/cmn/lib_pop.php";
//2007.07.23 matsuki update start
require SRC_ROOT . "po/cmn/lib_pos1.php";
require SRC_ROOT . "po/cmn/column.php";
require SRC_ROOT . "po/cmn/lib_por.php";
// require SRC_ROOT . "so/cmn/lib_so.php";
$objDB = new clsDB();
$objAuth = new clsAuth();

$objDB->open("", "", "", "");

$aryData["strSessionID"] = $_POST["strSessionID"];
//    aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($_POST["strSessionID"], $objAuth, $objDB);
$UserDisplayName = $objAuth->UserDisplayName;
$UserDisplayCode = $objAuth->UserID;

// 排他制御チェック
/*
if (fncCheckExclusiveControl(DEF_FUNCTION_E3, $_POST["strProductCode"], $_POST["strReviseCode"], $objDB)) {
//    echo "test";
    fncOutputError(9213, DEF_ERROR, "", true, "../po/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}
*/
if ($_POST["strMode"] == "cancel") {
    // 排他制御ロック解放
    $objDB->transactionBegin();
    $result = unlockExclusive($objAuth, $objDB);
    $objDB->transactionCommit();
    return true;
}

// ここから追加ボタン押下処理


if ($_POST["strMode"] == "insert") {
    // 更新データ取得
    $aryUpdate["lngorderno"] = $_POST["lngOrderNo"];
    $aryUpdate["lngrevisionno"] = $_POST["lngRevisionNo"];
    // $aryUpdate["dtmexpirationdate"] = $_POST["dtmExpirationDate"];
    $aryUpdate["lngpayconditioncode"] = $_POST["lngPayConditionCode"];
    $aryUpdate["lngdeliveryplacecode"] = $_POST["lngLocationCode"];
    $aryUpdate["strProductCode"] = $_POST["strProductCode"];
    $aryUpdate["strReviseCode"] = $_POST["strReviseCode"];
    $aryUpdate["strnote"] = $_POST["strNote"];
    $aryUpdate["lngorderstatuscode"] = 2;
    
    for ($i = 0; $i < count($_POST["aryDetail"]); $i++) {
        $aryUpdateDetail[$i]["lngpurchaseorderdetailno"] = $i + 1;
        $aryUpdateDetail[$i]["lngorderdetailno"] = $_POST["aryDetail"][$i]["lngOrderDetailNo"];
        $aryUpdateDetail[$i]["lngsortkey"] = $_POST["aryDetail"][$i]["lngSortKey"];

        $aryUpdateDetail[$i]["strproductcode"] = $_POST["strProductCode"];
        $aryUpdateDetail[$i]["strrevisecode"] = $_POST["strReviseCode"];
        
        $aryUpdateDetail[$i]["lngdeliverymethodcode"] = $_POST["aryDetail"][$i]["lngDeliveryMethodCode"];
        $aryUpdateDetail[$i]["strdeliverymethodname"] = $_POST["aryDetail"][$i]["strDeliveryMethodName"];
        $aryUpdateDetail[$i]["lngproductunitcode"] = $_POST["aryDetail"][$i]["lngProductUnitCode"];
        $aryUpdateDetail[$i]["lngorderno"] = $_POST["aryDetail"][$i]["lngOrderNo"];
        $aryUpdateDetail[$i]["lngrevisionno"] = $_POST["aryDetail"][$i]["lngRevisionNo"];
        $aryUpdateDetail[$i]["lngstocksubjectcode"] = $_POST["aryDetail"][$i]["lngStockSubjectCode"];
        $aryUpdateDetail[$i]["lngstockitemcode"] = $_POST["aryDetail"][$i]["lngStockItemCode"];
        $aryUpdateDetail[$i]["lngmonetaryunitcode"] = $_POST["aryDetail"][$i]["lngMonetaryUnitCode"];
        $aryUpdateDetail[$i]["lngcustomercompanycode"] = $_POST["aryDetail"][$i]["lngCustomerCompanyCode"];
        $aryUpdateDetail[$i]["curproductprice"] = $_POST["aryDetail"][$i]["curProductPrice"];
        $aryUpdateDetail[$i]["lngproductquantity"] = $_POST["aryDetail"][$i]["lngProductQuantity"];
        $aryUpdateDetail[$i]["cursubtotalprice"] = $_POST["aryDetail"][$i]["curSubtotalPrice"];
        $aryUpdateDetail[$i]["dtmseliverydate"] = $_POST["aryDetail"][$i]["dtmDeliveryDate"];
        $aryUpdateDetail[$i]["strnote"] = $_POST["aryDetail"][$i]["strDetailNote"];
    }

    $objDB->transactionBegin();

    // リビジョンチェック
    // 発注マスタ更新
    if (!fncUpdateOrder($aryUpdate, $aryUpdateDetail, $objDB)) {return false;}
    // 発注明細更新
    if (!fncUpdateOrderDetail($aryUpdate, $aryUpdateDetail, $objDB)) {return false;}
    // 発注書マスタ更新
    //if(!fncUpdatePurchaseOrder($aryUpdate, $aryUpdateDetail, $objAuth, $objDB)){ return false; }
    $aryResult = fncInsertPurchaseOrderByDetail($aryUpdate, $aryUpdateDetail, $objAuth, $objDB);

    // 排他制御ロック解放
    $result = unlockExclusive($objAuth, $objDB);

    $objDB->transactionCommit();

    // 更新後発注書データ取得
    $aryPurcharseOrder = fncGetPurchaseOrder($aryResult, $objDB);
    if (!$aryPurcharseOrder) {
        fncOutputError(9051, DEF_ERROR, "発注書の取得に失敗しました。", true, "", $objDB);
        return false;
    }

    $strHtml = fncCreatePurchaseOrderHtml($aryPurcharseOrder, $aryData["strSessionID"]);
    $aryData["aryPurchaseOrder"] = $strHtml;

    // テンプレート読み込み
    $objTemplate = new clsTemplate();

//        header("Content-type: text/plain; charset=UTF-8");
    $objTemplate->getTemplate("/po/finish/parts.tmpl");

    // テンプレート生成
    $objTemplate->replace($aryData);
    // $objTemplate->complete();

    // HTML出力
    echo $objTemplate->strTemplate;

    return true;
}
// 追加ボタン押下処理ここまで

// 明細行を除く
for ($i = 0; $i < count($_POST); $i++) {
    list($strKeys, $strValues) = each($_POST);
    if ($strKeys != "aryPoDitail") {
        $aryData[$strKeys] = $strValues;
    }
}

// 明細行処理 ===========================================================================================
// 明細行のhidden生成
if (is_array($_POST["aryDetail"])) {
    $aryData["strDetailHidden"] = fncDetailHidden($_POST["aryDetail"], "insert", $objDB);
}

/*
// 言語の設定
if ( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
{
$aryTytle = $aryTableTytleEng;
}
else
{
$aryTytle = $aryTableTytle;
}
 */

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

/*
// 顧客品番
$_POST["aryDetail"][$i]["strGoodsName"] = fncGetMasterValue( "m_product", "strproductcode", "strGoodsCode", $_POST["aryDetail"][$i]["strProductCode"].":str", "bytinvalidflag = false", $objDB );
 */
    // 運搬方法
    $_POST["aryDetail"][$i]["strCarrierName"] = fncGetMasterValue("m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $_POST["aryDetail"][$i]["lngDeliveryMethodCode"], '', $objDB);
    // 単位
    $_POST["aryDetail"][$i]["strProductUnitName"] = fncGetMasterValue("m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["aryDetail"][$i]["lngProductUnitCode"], '', $objDB);

    // 明細行備考の特殊文字変換
    $_POST["aryDetail"][$i]["strDetailNote"] = fncHTMLSpecialChars($_POST["aryDetail"][$i]["strDetailNote"]);

    // 通貨記号
    $_POST["aryDetail"][$i]["strMonetarySign"] = ($_POST["aryDetail"][0]["lngMonetaryUnitCode"] == 1) ? "\\\\" : fncGetMasterValue("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $_POST["aryDetail"][$i]["lngMonetaryUnitCode"], '', $objDB);
    //2004/03/17 watanabe update start
    $strProductName = "";
    $strProductCode_wk = substr($_POST["strProductCode"],0,5);
    $subCondition = "strrevisecode='".$_POST["strReviseCode"]."' AND lngrevisionno = (select MAX(lngrevisionno) from m_product where strproductcode ='".$strProductCode_wk."'and strrevisecode='".$_POST["strReviseCode"]."')";
    if ($strProductName = fncGetMasterValue("m_product", 
                                            "strproductcode", 
                                            "strproductname", 
                                            $strProductCode_wk . ":str", 
                                            $subCondition,
                                             $objDB)) {
        $_POST["aryDetail"][$i]["strproductname"] = $strProductName;
    }
    // watanabe end

    // 2004/03/11 number_format watanabe
    // 単価
    $_POST["aryDetail"][$i]["curproductprice_DIS"] = $_POST["aryDetail"][$i]["curProductPrice"];
    $_POST["aryDetail"][$i]["lnggoodsquantity_DIS"] = $_POST["aryDetail"][$i]["lngProductQuantity"];
    $_POST["aryDetail"][$i]["curtotalprice_DIS"] = $_POST["aryDetail"][$i]["curSubtotalPrice"];
    $allPrice = $allPrice + (double) (str_replace(",", "", explode(" ", $_POST["aryDetail"][$i]["curSubtotalPrice"])[1]));
    // watanabe update end

    // 2004/03/19 watanabe update コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
    $_POST["aryDetail"][$i]["strproductcode_DISCODE"] = ($_POST["strProductCode"] != "") ? "[" . $_POST["strProductCode"] . "]" : "";
    $_POST["aryDetail"][$i]["strstockitemcode_DISCODE"] = ($_POST["aryDetail"][$i]["lngStockItemCode"] != "") ? "[" . $_POST["aryDetail"][$i]["lngStockItemCode"] . "]" : "";
    $_POST["aryDetail"][$i]["strstocksubjectcode_DISCODE"] = ($_POST["aryDetail"][$i]["lngStockSubjectCode"] != "") ? "[" . $_POST["aryDetail"][$i]["lngStockSubjectCode"] . "]" : "";

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

$aryData["strMode"] = "insert";

// 登録日
$aryData["dtminsertdate"] = date('Y/m/d', time());

// 入力者
$aryData["lngInputUserCode"] = $UserDisplayCode;
$aryData["strInputUserName"] = $UserDisplayName;
// 通貨
$aryData["strMonetaryUnitName"] = fncGetMasterValue("m_monetaryunit", "lngMonetaryUnitCode", "strmonetaryunitname", $_POST["aryDetail"][0]["lngMonetaryUnitCode"] . ":str", '', $objDB);
// 支払条件
$strPayConditionName = fncGetMasterValue("m_paycondition", "lngpayconditioncode", "strpayconditionname", $_POST["lngPayConditionCode"], '', $objDB);
// あえてハイフン消すの？
$aryData["strPayConditionName"] = ($strPayConditionName == "−") ? "" : $strPayConditionName;

// 納品場所
$aryData["strLocationName"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "strcompanydisplayname", $aryData["lngLocationCode"] . ":str", '', $objDB);

// 状態
$aryData["strAction"] = "/po/confirm/index.php?strSessionID=" . $aryData["strSessionID"];

//ヘッダ備考の特殊文字変換
$aryData["strNote"] = fncHTMLSpecialChars($aryData["strNote"]);

// 通貨記号+合計金額
$aryData["strMonetarySign"] = $_POST["aryDetail"][0]["strMonetarySign"];
$aryData["curAllTotalPrice_DIS"] = convertPrice($_POST["aryDetail"][0]["lngMonetaryUnitCode"], $aryData["strMonetarySign"], $aryData["curAllTotalPrice"], "price");// 合計金額

// コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）

// 入力者
$aryData["lngInputUserCode_DISCODE"] = ($aryData["lngInputUserCode"] != "") ? "[" . $aryData["lngInputUserCode"] . "]" : "";
// 仕入先
$aryData["strCustomerCode"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode", $_POST["aryDetail"][0]["lngCustomerCompanyCode"] . ":str", '', $objDB);
$aryData["strCustomerName"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplayname", $_POST["aryDetail"][0]["lngCustomerCompanyCode"] . ":str", '', $objDB);
$aryData["lngCustomerCode_DISCODE"] = ($_POST["aryDetail"][0]["lngCustomerCompanyCode"] != "") ? "[" . $aryData["strCustomerCode"] . "]" : "";

// 納品場所
$aryData["lngLocationCode_DISCODE"] = ($aryData["lngLocationCode"] != "") ? "[" . $aryData["lngLocationCode"] . "]" : "";

// watanabe update end

// 支払条件整合性チェック
$aryData["lngMonetaryUnitCode"] = $_POST["aryDetail"][0]["lngMonetaryUnitCode"];
$aryData["lngCustomerCode"] = $_POST["aryDetail"][0]["lngCustomerCompanyCode"];
$aryData = fncPayConditionCodeMatch($aryData, $aryHeadColumnNames, $_POST["aryDetail"], $objDB);

$objDB->close();

// テンプレート読み込み
$objTemplate = new clsTemplate();
//var_dump($aryData);

$objTemplate->getTemplate("po/confirm/parts.tmpl");

// テンプレート生成

$objTemplate->replace($aryHeadColumnNames);
$objTemplate->replace($aryData);
$objTemplate->complete();

// HTML出力 明細行は_%strDetailTable%_で受け渡し
echo $objTemplate->strTemplate;

return true;
