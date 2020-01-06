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
$UserDisplayName = $objAuth->UserDisplayName;
$UserDisplayCode = $objAuth->UserID;

// ��¾��������å�
if (fncCheckExclusiveControl(DEF_FUNCTION_E3, $_POST["strProductCode"], $_POST["strReviseCode"], $objDB)) {
//    echo "test";
    fncOutputError(9213, DEF_ERROR, "", true, "../po/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}
// ���������ɲåܥ��󲡲�����
if ($_POST["strMode"] == "insert") {
    // �����ǡ�������
    $aryUpdate["lngorderno"] = $_POST["lngOrderNo"];
    $aryUpdate["lngrevisionno"] = $_POST["lngRevisionNo"];
    $aryUpdate["dtmexpirationdate"] = $_POST["dtmExpirationDate"];
    $aryUpdate["lngpayconditioncode"] = $_POST["lngPayConditionCode"];
    $aryUpdate["lngdeliveryplacecode"] = $_POST["lngLocationCode"];
    $aryUpdate["lngdeliveryplacecode"] = $_POST["lngLocationCode"];
    $aryUpdate["strnote"] = mb_convert_encoding($_POST["strNote"], "EUC-JP", "auto");
    $aryUpdate["lngorderstatuscode"] = 2;
    for ($i = 0; $i < count($_POST["aryDetail"]); $i++) {
        $aryUpdateDetail[$i]["lngpurchaseorderdetailno"] = $i + 1;
        $aryUpdateDetail[$i]["lngorderdetailno"] = $_POST["aryDetail"][$i]["lngOrderDetailNo"];
        $aryUpdateDetail[$i]["lngsortkey"] = $_POST["aryDetail"][$i]["lngSortKey"];
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
    }

    $objDB->transactionBegin();
    // ȯ��ޥ�������
    if (!fncUpdateOrder($aryUpdate, $aryUpdateDetail, $objDB)) {return false;}
    // ȯ�����ٹ���
    if (!fncUpdateOrderDetail($aryUpdate, $aryUpdateDetail, $objDB)) {return false;}
    // ȯ���ޥ�������
    //if(!fncUpdatePurchaseOrder($aryUpdate, $aryUpdateDetail, $objAuth, $objDB)){ return false; }
    $aryResult = fncInsertPurchaseOrderByDetail($aryUpdate, $aryUpdateDetail, $objAuth, $objDB);
    $objDB->transactionCommit();

    // ������ȯ���ǡ�������
    $aryPurcharseOrder = fncGetPurchaseOrder($aryResult, $objDB);
    if (!$aryPurcharseOrder) {
        fncOutputError(9051, DEF_ERROR, "ȯ���μ����˼��Ԥ��ޤ�����", true, "", $objDB);
        return false;
    }

    $strHtml = fncCreatePurchaseOrderHtml($aryPurcharseOrder, $aryData["strSessionID"]);
    $aryData["aryPurchaseOrder"] = $strHtml;

    // �ƥ�ץ졼���ɤ߹���
    $objTemplate = new clsTemplate();

//        header("Content-type: text/plain; charset=EUC-JP");
    $objTemplate->getTemplate("/po/finish/parts.tmpl");

    // �ƥ�ץ졼������
    $objTemplate->replace($aryData);
    // $objTemplate->complete();

    // HTML����
    echo $objTemplate->strTemplate;

    return true;
}
// �ɲåܥ��󲡲����������ޤ�

// ���ٹԤ����
for ($i = 0; $i < count($_POST); $i++) {
    list($strKeys, $strValues) = each($_POST);
    if ($strKeys != "aryPoDitail") {
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
    $_POST["aryDetail"][$i]["strStockItemName"] = fncGetMasterValue("m_stockitem", "lngstockitemcode", "strstockitemname", $_POST["aryDetail"][$i]["strStockItemCode"], "lngstocksubjectcode = " . $_POST["aryDetail"][$i]["lngStockSubjectCode"], $objDB);

/*
// �ܵ�����
$_POST["aryDetail"][$i]["strGoodsName"] = fncGetMasterValue( "m_product", "strproductcode", "strGoodsCode", $_POST["aryDetail"][$i]["strProductCode"].":str", "bytinvalidflag = false", $objDB );
 */
    // ������ˡ
    $_POST["aryDetail"][$i]["strCarrierName"] = fncGetMasterValue("m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $_POST["aryDetail"][$i]["lngDeliveryMethodCode"], '', $objDB);
    // ñ��
    $_POST["aryDetail"][$i]["strProductUnitName"] = fncGetMasterValue("m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["aryDetail"][$i]["lngProductUnitCode"], '', $objDB);

    // ���ٹ����ͤ��ü�ʸ���Ѵ�
    $_POST["aryDetail"][$i]["strDetailNote"] = fncHTMLSpecialChars(mb_convert_encoding($_POST["aryDetail"][$i]["strDetailNote"], "EUC-JP", "auto"));

    // �̲ߵ���
    $_POST["aryDetail"][$i]["strMonetarySign"] = ($_POST["aryDetail"][0]["lngMonetaryUnitCode"] == 1) ? "\\\\" : fncGetMasterValue("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $_POST["aryDetail"][$i]["lngMonetaryUnitCode"], '', $objDB);
    //2004/03/17 watanabe update start
    $strProductName = "";
    if ($strProductName = fncGetMasterValue("m_product", "strproductcode", "strproductname", $_POST["strProductCode"] . ":str", '', $objDB)) {
        $_POST["aryDetail"][$i]["strproductname"] = $strProductName;
    }
    // watanabe end

    // 2004/03/11 number_format watanabe
    // ñ��
    $_POST["aryDetail"][$i]["curproductprice_DIS"] = ($_POST["aryDetail"][$i]["curProductPrice"] != "") ? number_format((double) (str_replace(",", "", $_POST["aryDetail"][$i]["curProductPrice"])), 4) : "";
    $_POST["aryDetail"][$i]["lnggoodsquantity_DIS"] = ($_POST["aryDetail"][$i]["lngProductQuantity"] != "") ? number_format(str_replace(",", "", $_POST["aryDetail"][$i]["lngProductQuantity"])) : "";
    $_POST["aryDetail"][$i]["curtotalprice_DIS"] = ($_POST["aryDetail"][$i]["curSubtotalPrice"] != "") ? number_format((double) (str_replace(",", "", $_POST["aryDetail"][$i]["curSubtotalPrice"])), 4) : "";
    $allPrice = $allPrice + (double) (str_replace(",", "", $_POST["aryDetail"][$i]["curSubtotalPrice"]));
    // watanabe update end

    // 2004/03/19 watanabe update �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
    $_POST["aryDetail"][$i]["strproductcode_DISCODE"] = ($_POST["strProductCode"] != "") ? "[" . $_POST["strProductCode"] . "]" : "";
    $_POST["aryDetail"][$i]["strstockitemcode_DISCODE"] = ($_POST["aryDetail"][$i]["strStockItemCode"] != "") ? "[" . $_POST["aryDetail"][$i]["strStockItemCode"] . "]" : "";
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

$aryData["strMode"] = "insert";

// ��Ͽ��
$aryData["dtminsertdate"] = date('Y/m/d', time());

// ���ϼ�
$aryData["lngInputUserCode"] = $UserDisplayCode;
$aryData["strInputUserName"] = $UserDisplayName;
// �̲�
$aryData["strMonetaryUnitName"] = fncGetMasterValue("m_monetaryunit", "lngMonetaryUnitCode", "strmonetaryunitname", $_POST["aryDetail"][0]["lngMonetaryUnitCode"] . ":str", '', $objDB);
// ��ʧ���
$strPayConditionName = fncGetMasterValue("m_paycondition", "lngpayconditioncode", "strpayconditionname", $_POST["lngPayConditionCode"], '', $objDB);
// �����ƥϥ��ե�ä��Ρ�
$aryData["strPayConditionName"] = ($strPayConditionName == "��") ? "" : $strPayConditionName;

// Ǽ�ʾ��
$aryData["strLocationName"] = fncGetMasterValue("m_company", "strcompanydisplaycode", "strcompanydisplayname", $aryData["lngLocationCode"] . ":str", '', $objDB);

// ����
$aryData["strAction"] = "/po/confirm/index.php?strSessionID=" . $aryData["strSessionID"];

//�إå����ͤ��ü�ʸ���Ѵ�
$aryData["strNote"] = fncHTMLSpecialChars(mb_convert_encoding($aryData["strNote"], "EUC-JP", "auto"));

// �̲ߵ���+��׶��
$aryData["strMonetarySign"] = $_POST["aryDetail"][0]["strMonetarySign"];
$aryData["curAllTotalPrice_DIS"] = number_format($aryData["curAllTotalPrice"], 2); // ��׶��

// �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������

// ���ϼ�
$aryData["lngInputUserCode_DISCODE"] = ($aryData["lngInputUserCode"] != "") ? "[" . $aryData["lngInputUserCode"] . "]" : "";
// ������
$aryData["strCustomerCode"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode", $_POST["aryDetail"][0]["lngCustomerCompanyCode"] . ":str", '', $objDB);
$aryData["strCustomerName"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplayname", $_POST["aryDetail"][0]["lngCustomerCompanyCode"] . ":str", '', $objDB);
$aryData["lngCustomerCode_DISCODE"] = ($_POST["aryDetail"][0]["lngCustomerCompanyCode"] != "") ? "[" . $aryData["strCustomerCode"] . "]" : "";

// Ǽ�ʾ��
$aryData["lngLocationCode_DISCODE"] = ($aryData["lngLocationCode"] != "") ? "[" . $aryData["lngLocationCode"] . "]" : "";

// watanabe update end

// ��ʧ��������������å�
$aryData["lngMonetaryUnitCode"] = $_POST["aryDetail"][0]["lngMonetaryUnitCode"];
$aryData["lngCustomerCode"] = $_POST["aryDetail"][0]["lngCustomerCompanyCode"];
$aryData = fncPayConditionCodeMatch($aryData, $aryHeadColumnNames, $_POST["aryDetail"], $objDB);

$objDB->close();

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
//var_dump($aryData);

$objTemplate->getTemplate("po/confirm/parts.tmpl");

// �ƥ�ץ졼������

$objTemplate->replace($aryHeadColumnNames);
$objTemplate->replace($aryData);
$objTemplate->complete();

// HTML���� ���ٹԤ�_%strDetailTable%_�Ǽ����Ϥ�
echo $objTemplate->strTemplate;

return true;
