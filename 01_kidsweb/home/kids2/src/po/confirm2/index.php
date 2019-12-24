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
require_once(LIB_DEBUGFILE);

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

// 排他制御チェック
if (fncCheckExclusiveControl(DEF_FUNCTION_E3, $_POST["strProductCode"], $_POST["strReviseCode"], $objDB)) {
    echo "test";
    fncOutputError(9213, DEF_ERROR, "", true, "../po/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

// ここから追加ボタン押下処理
if ($_POST["strMode"] == "update") {

    $_POST["strPayConditionName"] = mb_convert_encoding($_POST["strPayConditionName"], "EUC-JP", "UTF-8");
    $_POST["strLocationName"] = mb_convert_encoding($_POST["strLocationName"], "EUC-JP", "UTF-8");
    $_POST["strNote"] = mb_convert_encoding($_POST["strNote"], "EUC-JP", "UTF-8");

	$objDB->transactionBegin();
	// 発注書マスタ更新
fncDebug("kids2.log", "pass-2", __FILE__, __LINE__, "a" );
	if(!fncUpdatePurchaseOrder($_POST, $objDB, $objAuth)) { return false; }
fncDebug("kids2.log", "pass-3", __FILE__, __LINE__, "a" );
	// 発注書明細更新
	if(!fncUpdatePurchaseOrderDetail($_POST, $objDB)) { return false; }
fncDebug("kids2.log", "pass-4", __FILE__, __LINE__, "a" );

	// 更新後のデータを再度読み込む
	$updatedPurchaseOrder = fncGetPurchaseOrderEdit($_POST["lngPurchaseOrderNo"], $_POST["lngRevisionNo"], $objDB);

	$strHtml = fncCreatePurchaseOrderUpdateHtml($updatedPurchaseOrder, $aryData["strSessionID"]);
	$aryData["aryPurchaseOrder"] = $strHtml;

	// $objDB->transactionRollback();
	$objDB->transactionCommit();

	// テンプレート読み込み
	$objTemplate = new clsTemplate();

	header("Content-type: text/plain; charset=EUC-JP");
	$objTemplate->getTemplate( "po/finish/parts.tmpl" );
		
	// テンプレート生成
	$objTemplate->replace( $aryData );

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
$aryData["dtmExpirationDate"] = $_POST["dtmExpirationDate"];
$aryData["lngPayConditionCode"] = $_POST["lngPayConditionCode"];
$aryData["strPayConditionName"] = $_POST["strPayConditionName"];
$aryData["strLocationName"] = $_POST["strLocationName"];
$aryData["strNote"] = $_POST["strNote"];
$aryData["lngMonetaryUnitCode"] = $_POST["lngMonetaryUnitCode"];
$aryData["strmonetaryunitname"] = mb_convert_encoding($_POST["strMonetaryUnitName"],"EUC-JP", "UTF-8");
$aryData["aryDetail"] = $_POST["aryDetail"];

// 明細行を除く
for ($i = 0; $i < count($_POST); $i++) {
    list($strKeys, $strValues) = each($_POST);
    if ($strKeys != "aryDetail") {
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

    $_POST["aryDetail"][$i]["dtmdeliverydate"] = $_POST["aryDetail"][$i]["dtmDeliveryDate"];
    

    $_POST["aryDetail"][$i]["strDetailNote"] = mb_convert_encoding($_POST["aryDetail"][$i]["strDetailNote"],"EUC-JP", "UTF-8");
    // 2004/03/11 number_format watanabe
    // 単価
    $_POST["aryDetail"][$i]["strMonetarySign"] = $aryData["lngMonetaryUnitCode"];
    $_POST["aryDetail"][$i]["curproductprice_DIS"] = ($_POST["aryDetail"][$i]["curProductPrice"] != "") ? number_format((double) (str_replace(",", "", $_POST["aryDetail"][$i]["curProductPrice"])), 4) : "";
    $_POST["aryDetail"][$i]["lnggoodsquantity_DIS"] = ($_POST["aryDetail"][$i]["lngProductQuantity"] != "") ? number_format(str_replace(",", "", $_POST["aryDetail"][$i]["lngProductQuantity"])) : "";
    $_POST["aryDetail"][$i]["curtotalprice_DIS"] = ($_POST["aryDetail"][$i]["curSubtotalPrice"] != "") ? number_format((double) (str_replace(",", "", $_POST["aryDetail"][$i]["curSubtotalPrice"])), 2) : "";
    $allPrice = $allPrice + (double) (str_replace(",", "", $_POST["aryDetail"][$i]["curSubtotalPrice"]));
    // watanabe update end

    // 2004/03/19 watanabe update コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
    $_POST["aryDetail"][$i]["strproductcode_DISCODE"] = ($_POST["strProductCode"] != "") ? "[" . $_POST["strProductCode"] . "]" : "";
    $_POST["aryDetail"][$i]["strproductname"] = mb_convert_encoding($_POST["strProductName"], "EUC-JP", "UTF-8");
    $_POST["aryDetail"][$i]["strstockitemcode_DISCODE"] = ($_POST["aryDetail"][$i]["lngStockItemCode"] != "") ? "[" . $_POST["aryDetail"][$i]["lngStockItemCode"] . "]" : "";
    $_POST["aryDetail"][$i]["strstocksubjectcode_DISCODE"] = ($_POST["aryDetail"][$i]["lngStockSubjectCode"] != "") ? "[" . $_POST["aryDetail"][$i]["lngStockSubjectCode"] . "]" : "";

    // テンプレート読み込み
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("po/result/parts_detail2.tmpl");

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
$aryData["strMonetarySign"] = $aryData["lngMonetaryUnitCode"];

//ヘッダ備考の特殊文字変換
$aryData["strNote"] = fncHTMLSpecialChars(mb_convert_encoding($_POST["strNote"], "EUC-JP", "auto"));

// 通貨記号+合計金額

$aryData["curAllTotalPrice_DIS"] = number_format($aryData["curAllTotalPrice"], 2); // 合計金額

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
//$aryData["lngMonetaryUnitCode"] = $aryData["lngMonetaryUnitCode"];
$aryData["lngCustomerCode"] = $aryData["lngCustomerCompanyCode"];
$aryData = fncPayConditionCodeMatch($aryData, $aryHeadColumnNames, $_POST["aryDetail"], $objDB);

$objDB->close();

// テンプレート読み込み
$objTemplate = new clsTemplate();
//var_dump($aryData);

$objTemplate->getTemplate("po/confirm2/parts.tmpl");

// テンプレート生成

$objTemplate->replace($aryHeadColumnNames);
$objTemplate->replace($aryData);
$objTemplate->complete();

// HTML出力 明細行は_%strDetailTable%_で受け渡し
echo $objTemplate->strTemplate;

return true;
