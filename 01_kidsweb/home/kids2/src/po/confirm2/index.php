<?php

// ----------------------------------------------------------------------------
/**
 *       ȯ�����  ��Ͽ��ǧ����
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
 *       ��������
 *         ����Ͽ��ǧ���̤�ɽ��
 *         �����顼�����å�
 *         ����Ͽ�ܥ��󲡲��塢��Ͽ������
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

// �ɤ߹���
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

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// ���å�����ǧ
$objAuth = fncIsSession($_POST["strSessionID"], $objAuth, $objDB);
$UserDisplayName = trim($objAuth->UserDisplayName);
$UserDisplayCode = trim($objAuth->UserID);

// ��¾��������å�
if (fncCheckExclusiveControl(DEF_FUNCTION_E3, $_POST["strProductCode"], $_POST["strReviseCode"], $objDB)) {
    echo "test";
    fncOutputError(9213, DEF_ERROR, "", true, "../po/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

// ���������ɲåܥ��󲡲�����
if ($_POST["strMode"] == "update") {

    $_POST["strPayConditionName"] = mb_convert_encoding($_POST["strPayConditionName"], "EUC-JP", "UTF-8");
    $_POST["strLocationName"] = mb_convert_encoding($_POST["strLocationName"], "EUC-JP", "UTF-8");
    $_POST["strNote"] = mb_convert_encoding($_POST["strNote"], "EUC-JP", "UTF-8");

	$objDB->transactionBegin();
	// ȯ���ޥ�������
fncDebug("kids2.log", "pass-2", __FILE__, __LINE__, "a" );
	if(!fncUpdatePurchaseOrder($_POST, $objDB, $objAuth)) { return false; }
fncDebug("kids2.log", "pass-3", __FILE__, __LINE__, "a" );
	// ȯ������ٹ���
	if(!fncUpdatePurchaseOrderDetail($_POST, $objDB)) { return false; }
fncDebug("kids2.log", "pass-4", __FILE__, __LINE__, "a" );

	// ������Υǡ���������ɤ߹���
	$updatedPurchaseOrder = fncGetPurchaseOrderEdit($_POST["lngPurchaseOrderNo"], $_POST["lngRevisionNo"], $objDB);

	$strHtml = fncCreatePurchaseOrderUpdateHtml($updatedPurchaseOrder, $aryData["strSessionID"]);
	$aryData["aryPurchaseOrder"] = $strHtml;

	// $objDB->transactionRollback();
	$objDB->transactionCommit();

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();

	header("Content-type: text/plain; charset=EUC-JP");
	$objTemplate->getTemplate( "po/finish/parts.tmpl" );
		
	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );

	// HTML����
	echo $objTemplate->strTemplate;

	return true;

}
// �ɲåܥ��󲡲����������ޤ�

// �����ǡ�������
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

// ���ٹԤ����
for ($i = 0; $i < count($_POST); $i++) {
    list($strKeys, $strValues) = each($_POST);
    if ($strKeys != "aryDetail") {
        $aryData[$strKeys] = $strValues;
    }
}

// ���ٹԽ��� ===========================================================================================
// ���ٹԤ�hidden����
if (is_array($_POST["aryDetail"])) {
    $aryData["strDetailHidden"] = fncDetailHidden($_POST["aryDetail"], "insert", $objDB);
}

/*
// ���������
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
// �����̾������
$aryHeadColumnNames = fncSetPurchaseTabelName($aryTableViewHead, $aryTytle);
$aryDetailColumnNames = fncSetPurchaseTabelName($aryTableViewDetail, $aryTytle);
$allPrice = 0;
$aryData["lngDetailCount"] = count($_POST["aryDetail"]);
for ($i = 0; $i < count($_POST["aryDetail"]); $i++) {

    $_POST["aryDetail"][$i]["lngrecordno"] = $i + 1;

    // ��������
    $_POST["aryDetail"][$i]["strStockSubjectName"] = fncGetMasterValue("m_stocksubject", "lngstocksubjectcode", "strstocksubjectname", $_POST["aryDetail"][$i]["lngStockSubjectCode"], '', $objDB);
    // ��������
    $_POST["aryDetail"][$i]["strStockItemName"] = fncGetMasterValue("m_stockitem", "lngstockitemcode", "strstockitemname", $_POST["aryDetail"][$i]["lngStockItemCode"], "lngstocksubjectcode = " . $_POST["aryDetail"][$i]["lngStockSubjectCode"], $objDB);

/*
// �ܵ�����
$_POST["aryDetail"][$i]["strGoodsName"] = fncGetMasterValue( "m_product", "strproductcode", "strGoodsCode", $_POST["aryDetail"][$i]["strProductCode"].":str", "bytinvalidflag = false", $objDB );
 */
    // ������ˡ
    $_POST["aryDetail"][$i]["strCarrierName"] = fncGetMasterValue("m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $_POST["aryDetail"][$i]["lngDeliveryMethodCode"], '', $objDB);

    $_POST["aryDetail"][$i]["dtmdeliverydate"] = $_POST["aryDetail"][$i]["dtmDeliveryDate"];
    

    $_POST["aryDetail"][$i]["strDetailNote"] = mb_convert_encoding($_POST["aryDetail"][$i]["strDetailNote"],"EUC-JP", "UTF-8");
    // 2004/03/11 number_format watanabe
    // ñ��
    $_POST["aryDetail"][$i]["strMonetarySign"] = $aryData["lngMonetaryUnitCode"];
    $_POST["aryDetail"][$i]["curproductprice_DIS"] = ($_POST["aryDetail"][$i]["curProductPrice"] != "") ? number_format((double) (str_replace(",", "", $_POST["aryDetail"][$i]["curProductPrice"])), 4) : "";
    $_POST["aryDetail"][$i]["lnggoodsquantity_DIS"] = ($_POST["aryDetail"][$i]["lngProductQuantity"] != "") ? number_format(str_replace(",", "", $_POST["aryDetail"][$i]["lngProductQuantity"])) : "";
    $_POST["aryDetail"][$i]["curtotalprice_DIS"] = ($_POST["aryDetail"][$i]["curSubtotalPrice"] != "") ? number_format((double) (str_replace(",", "", $_POST["aryDetail"][$i]["curSubtotalPrice"])), 2) : "";
    $allPrice = $allPrice + (double) (str_replace(",", "", $_POST["aryDetail"][$i]["curSubtotalPrice"]));
    // watanabe update end

    // 2004/03/19 watanabe update �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
    $_POST["aryDetail"][$i]["strproductcode_DISCODE"] = ($_POST["strProductCode"] != "") ? "[" . $_POST["strProductCode"] . "]" : "";
    $_POST["aryDetail"][$i]["strproductname"] = mb_convert_encoding($_POST["strProductName"], "EUC-JP", "UTF-8");
    $_POST["aryDetail"][$i]["strstockitemcode_DISCODE"] = ($_POST["aryDetail"][$i]["lngStockItemCode"] != "") ? "[" . $_POST["aryDetail"][$i]["lngStockItemCode"] . "]" : "";
    $_POST["aryDetail"][$i]["strstocksubjectcode_DISCODE"] = ($_POST["aryDetail"][$i]["lngStockSubjectCode"] != "") ? "[" . $_POST["aryDetail"][$i]["lngStockSubjectCode"] . "]" : "";

    // �ƥ�ץ졼���ɤ߹���
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("po/result/parts_detail2.tmpl");

    // �ƥ�ץ졼������
    $objTemplate->replace($aryDetailColumnNames);
    $objTemplate->replace($_POST["aryDetail"][$i]);
    $objTemplate->complete();

    // HTML����
    $aryDetailTable[] = $objTemplate->strTemplate;
}
$aryData["curAllTotalPrice"] = $allPrice;
// exit();

$aryData["lngOrderNo"] = $_POST["lngOrderNo"];

$aryData["strDetailTable"] = implode("\n", $aryDetailTable);

$aryData["strMode"] = "update";

// ��Ͽ��
$aryData["dtminsertdate"] = date('Y/m/d', time());

// ���ϼ�
$aryData["lngInputUserCode"] = $UserDisplayCode;
$aryData["strInputUserName"] = $UserDisplayName;
// ��ʧ���
$strPayConditionName = fncGetMasterValue("m_paycondition", "lngpayconditioncode", "strpayconditionname", $_POST["lngPayConditionCode"], '', $objDB);
// �����ƥϥ��ե�ä��Ρ�
$aryData["strPayConditionName"] = ($strPayConditionName == "��") ? "" : $strPayConditionName;

// Ǽ�ʾ��
$aryData["strLocationName"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "strcompanydisplayname", $_POST["lngLocationCode"] . ":str", '', $objDB);

// ����
$aryData["strAction"] = "/po/confirm2/index.php?strSessionID=" . $_POST["strSessionID"];
$aryData["strMonetarySign"] = $aryData["lngMonetaryUnitCode"];

//�إå����ͤ��ü�ʸ���Ѵ�
$aryData["strNote"] = fncHTMLSpecialChars(mb_convert_encoding($_POST["strNote"], "EUC-JP", "auto"));

// �̲ߵ���+��׶��

$aryData["curAllTotalPrice_DIS"] = number_format($aryData["curAllTotalPrice"], 2); // ��׶��

// �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������

// ���ϼ�
$aryData["lngInputUserCode_DISCODE"] = ($aryData["lngInputUserCode"] != "") ? "[" . $aryData["lngInputUserCode"] . "]" : "";
// ������
//$aryData["strCustomerCode"] = $aryData["lngCustomerCompanyCode"];
$aryData["strCustomerName"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "strcompanydisplayname", $aryData["lngCustomerCompanyCode"] . ":str", '', $objDB);
$aryData["lngCustomerCode_DISCODE"] = ($aryData["lngCustomerCompanyCode"] != "") ? "[" . $aryData["lngCustomerCompanyCode"] . "]" : "";

// Ǽ�ʾ��
$aryData["lngLocationCode_DISCODE"] = ($aryData["lngLocationCode"] != "") ? "[" . $aryData["lngLocationCode"] . "]" : "";

// watanabe update end

// ��ʧ��������������å�
//$aryData["lngMonetaryUnitCode"] = $aryData["lngMonetaryUnitCode"];
$aryData["lngCustomerCode"] = $aryData["lngCustomerCompanyCode"];
$aryData = fncPayConditionCodeMatch($aryData, $aryHeadColumnNames, $_POST["aryDetail"], $objDB);

$objDB->close();

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
//var_dump($aryData);

$objTemplate->getTemplate("po/confirm2/parts.tmpl");

// �ƥ�ץ졼������

$objTemplate->replace($aryHeadColumnNames);
$objTemplate->replace($aryData);
$objTemplate->complete();

// HTML���� ���ٹԤ�_%strDetailTable%_�Ǽ����Ϥ�
echo $objTemplate->strTemplate;

return true;
