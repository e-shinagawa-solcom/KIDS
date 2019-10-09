<?php

// ----------------------------------------------------------------------------
/**
 *      ��������  ��������
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// �� �饤�֥��ե������ɹ�
//-------------------------------------------------------------------------
include 'conf.inc';
require LIB_FILE;
require SRC_ROOT . "pc/cmn/lib_pc.php";

//-------------------------------------------------------------------------
// �� ���֥�����������
//-------------------------------------------------------------------------
$objDB = new clsDB();
$objAuth = new clsAuth();

//-------------------------------------------------------------------------
// �� DB�����ץ�
//-------------------------------------------------------------------------
$objDB->open("", "", "", "");

//-------------------------------------------------------------------------
// �� �ѥ�᡼������
//-------------------------------------------------------------------------
$aryData = $_GET;

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// ���顼���̤Ǥ����URL
$strReturnPath = "../pc/search/index.php?strSessionID=" . $aryData["strSessionID"];

// 700 ��������
if (!fncCheckAuthority(DEF_FUNCTION_PC0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, $strReturnPath, $objDB);
}

// 705 ���������� ����������
if (!fncCheckAuthority(DEF_FUNCTION_PC5, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "�����������¤�����ޤ���", true, $strReturnPath, $objDB);
}

// �����ֹ�μ���
$lngStockNo = $aryData["lngStockNo"];
$lngRevisionNo = $aryData["lngRevisionNo"];

// �����оݤλ���NO�λ����������
$strQuery = fncGetStockHeadNoToInfoSQL($lngStockNo, $lngRevisionNo);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryStock = $objDB->fetchArray($lngResultID, 0);
        // ���������ξ��֤�������ѡפξ��֤Ǥ����
        if ($aryStock["lngstockstatuscode"] == DEF_STOCK_CLOSED) {
            fncOutputError(711, DEF_WARNING, "", true, $strReturnPath, $objDB);
        }
    } else {
        fncOutputError(703, DEF_ERROR, "�����ǡ����μ����˼��Ԥ��ޤ���", true, $strReturnPath, $objDB);
    }
} else {
    fncOutputError(703, DEF_ERROR, "�ǡ������۾�Ǥ�", true, $strReturnPath, $objDB);
}

// ��������ֹ�λ������٥ǡ���������SQLʸ�κ���
$strQuery = fncGetStockDetailNoToInfoSQL($lngStockNo, $lngRevisionNo);

// ���٥ǡ����μ���
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryStockDetail[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    fncOutputError(703, DEF_WARNING, "�����ֹ���Ф������پ��󤬸��Ĥ���ޤ���", true, $strReturnPath, $objDB);
}

$objDB->freeResult($lngResultID);

// ȯ���������
$aryOrderDetail = fncGetPoInfoSQL($aryStock["strrealordercode"], $objDB);

// �����Ǿ�������
$taxObj = fncGetTaxInfo($aryStock["dtmstockappdate"], $objDB);

// �����Ƕ�ʬ�����
$aryTaxclass = fncGetTaxClassAry($objDB);

if ($taxObj == null) {
    fncOutputError(703, DEF_ERROR, "�����Ǿ���μ����˼��Ԥ��ޤ�����", true, $strReturnPath, $objDB);
}

$aryTaxclass = fncGetTaxClassAry($objDB);

// �̲�
$aryStock["lngmonetaryunitcode"] = fncGetPulldown("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitname", $aryStock["lngmonetaryunitcode"], '', $objDB);
// �졼�ȥ�����
$aryStock["lngmonetaryratecode"] = fncGetPulldown("m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryStock["lngmonetaryratecode"], '', $objDB);
// ��ʧ���
$aryStock["lngpayconditioncode"] = fncGetPulldown("m_paycondition", "lngpayconditioncode", "strpayconditionname", $aryStock["lngpayconditioncode"], '', $objDB);
// �ե�����URL
$aryStock["actionUrl"] = "/pc/modify/modify_confirm.php";
$objDB->close();

// var_dump($aryStock);
// return;

// �ƥ�ץ졼���ɤ߹���
// $objTemplate = new clsTemplate();
// $objTemplate->getTemplate("pc/modify/pc_modify.html");
// mb_convert_variables('EUC-JP', 'UTF-8', $aryData);
// // �ƥ�ץ졼������
// $objTemplate->replace($aryData);
// $objTemplate->replace($aryNewResult);
// $objTemplate->complete();

$strTemplate = fncGetReplacedHtmlWithBase("pc/base.html", "pc/modify/pc_modify.html", $aryStock, $objAuth);

// ������̥ơ��֥������ΰ�DOMDocument�����
$doc = new DOMDocument();
// �ѡ������顼����
libxml_use_internal_errors(true);
// DOM�ѡ���
$doc->loadHTML(mb_convert_encoding($strTemplate, "utf8", "eucjp-win"));
// $doc->loadHTML($strTemplate);
// �ѡ������顼���ꥢ
libxml_clear_errors();
// �ѡ������顼�������
libxml_use_internal_errors(false);

// ������̥ơ��֥�μ���
$tbodyDetail = $doc->getElementById("tbl_order_detail");
// $tbodyDetail = $tableDetail->getElementsByTagName("tbody")->item(0);

$aryData["lngGroupCode"] = $aryNewResult["lngGroupCode"];
$aryData["lngUserCode"] = $aryNewResult["lngUserCode"];

// ���پ���ν���
$num = 0;
foreach ($aryOrderDetail as $orderDetail) {
    $num += 1;
    // ������Ͽ�ѥե饰
    $isStocked = false;
    // �����ǥ�����
    $lngtaxcode = $orderDetail["lngtaxcode"];
    // �����Ƕ�ʬ������
    $lngtaxclasscode = DEF_TAXCLASS_HIKAZEI;
    // �����Ƕ��
    $curtaxprice = 0;
    // ������Ψ
    $curtax = 0;
    if ($orderDetail["lngcountrycode"] == 81) {
        $curtax = $taxObj->curtax;
        $lngtaxclasscode = DEF_TAXCLASS_SOTOZEI;
    }
    // ���������
    if ($lngtaxclasscode == DEF_TAXCLASS_HIKAZEI) {
        $curtaxprice = 0;
        //��2:����
    } else if ($lngtaxclasscode == DEF_TAXCLASS_SOTOZEI) {
        $curtaxprice = floor($orderDetail["cursubtotalprice"] * (1 + $curtax));
        // 3:����
    } else {
        $curtaxprice = $orderDetail["cursubtotalprice"] - floor(($orderDetail["cursubtotalprice"] / (1 + $curtax)) * $curtax);
    }

    // �������٤�롼�פ���ȯ���ֹ桢ȯ�������ֹ椬Ʊ���ξ�硢������Ͽ�ѥե饰��true
    foreach ($aryStockDetail as $stockDetail) {
        if ($stockDetail["lngorderno"] == $orderDetail["lngorderno"]
            && $stockDetail["lngorderdetailno"] == $orderDetail["lngorderdetailno"]) {
            $isStocked = true;
            $lngtaxclasscode = $stockDetail["lngtaxclasscode"];
            $lngtaxcode = $stockDetail["lngtaxcode"];
            $curtax = $stockDetail["curtax"];
            $curtaxprice = $stockDetail["curtaxprice_comm"];
        }
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");

    // No.
    $td = $doc->createElement("td", $num);
    $td->setAttribute("class", "col1");
    $trBody->appendChild($td);

    // �о�
    $td = $doc->createElement("td");
    $td->setAttribute("class", "col2");
    $chkBox = $doc->createElement("input");
    $chkBox->setAttribute("type", "checkbox");
    $chkBox->setAttribute("style", "width: 10px;");
    if ($isStocked) {
        $chkBox->setAttribute('checked', 'checked');
    }
    $td->appendChild($chkBox);
    $trBody->appendChild($td);

    // ����
    $textContent = "[". $orderDetail["strproductcode"]. "] ". substr($orderDetail["strproductname"], 1, 28);
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("class", "col3");
    $trBody->appendChild($td);

    // ��������
    $textContent = "[". $orderDetail["lngstocksubjectcode"]. "] ". $orderDetail["strstocksubjectname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("class", "col4");
    $trBody->appendChild($td);

    // ��������
    $textContent = "[". $orderDetail["lngstockitemcode"]. "] ". $orderDetail["strstockitemname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("class", "col5");
    $trBody->appendChild($td);

    // ñ��
    $td = $doc->createElement("td", toMoneyFormat($orderDetail["lngmonetaryunitcode"], $orderDetail["strmonetaryunitsign"], $orderDetail["curproductprice"]));
    $td->setAttribute("class", "col6");
    $trBody->appendChild($td);

    // ñ��
    $td = $doc->createElement("td", toUTF8($orderDetail["strmonetaryunitname"]));
    $td->setAttribute("class", "col7");
    $trBody->appendChild($td);

    // ����
    $td = $doc->createElement("td", number_format($orderDetail["lngproductquantity"]));
    $td->setAttribute("class", "col8");
    $trBody->appendChild($td);

    // ��ȴ���
    $td = $doc->createElement("td", toMoneyFormat($orderDetail["lngmonetaryunitcode"], $orderDetail["strmonetaryunitsign"], $orderDetail["cursubtotalprice"]));
    $td->setAttribute("class", "col9");
    $trBody->appendChild($td);

    // �����Ƕ�ʬ
    $td = $doc->createElement("td");
    $td->setAttribute("class", "col10");
    $select = $doc->createElement("select");
    $select->setAttribute("onchange", "resetTaxPrice(this)");
    $select->setAttribute("style", "width: 90px;");
    foreach ($aryTaxclass as $taxclass) {
        $option = $doc->createElement("option", toUTF8($taxclass["strtaxclassname"]));
        $option->setAttribute("value", $taxclass["lngtaxclasscode"]);
        if ($lngtaxclasscode == $taxclass["lngtaxclasscode"]) {
            $option->setAttribute("selected", "selected");
        }
        $select->appendChild($option);
    }
    if ($isStocked) {
        $chkBox->setAttribute('checked', 'checked');
    }
    $td->appendChild($select);
    $trBody->appendChild($td);

    
    // ������Ψ
    $td = $doc->createElement("td", $curtax);
    $td->setAttribute("class", "col11");
    $trBody->appendChild($td);

    // �����ǳ�
    $td = $doc->createElement("td", toMoneyFormat($orderDetail["lngmonetaryunitcode"], $orderDetail["strmonetaryunitsign"], $curtaxprice));
    $td->setAttribute("class", "col12");
    $trBody->appendChild($td);

    // Ǽ��
    $td = $doc->createElement("td", toUTF8($orderDetail["dtmdeliverydate"]));
    $td->setAttribute("class", "col13");
    $trBody->appendChild($td);

    // ����
    $td = $doc->createElement("td", toUTF8($orderDetail["strnote"]));
    $td->setAttribute("class", "col14");
    $trBody->appendChild($td);

    // ��ȴ��ۡʶ�ۥե����ޥå��Ѵ�����
    $td = $doc->createElement("td", $orderDetail["cursubtotalprice"]);
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ��Ψ
    $td = $doc->createElement("td", $taxObj->curtax);
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ñ�̥�����
    $td = $doc->createElement("td", $orderDetail["lngmonetaryunitcode"]);
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ñ�̵���
    $td = $doc->createElement("td", $orderDetail["strmonetaryunitsign"]);
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // �ǳ�
    $td = $doc->createElement("td", $curtaxprice);
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ȯ���ֹ�
    $td = $doc->createElement("td", $orderDetail["lngorderno"]);
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ȯ���ӥ�����ֹ�
    $td = $doc->createElement("td", $orderDetail["lngrevisionno"]);
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ȯ�������ֹ�
    $td = $doc->createElement("td", $orderDetail["lngorderdetailno"]);
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // �����ǥ�����
    $td = $doc->createElement("td", $taxObj->lngtaxcode);
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);
    
    // tbody > tr
    $tbodyDetail->appendChild($trBody);
}

$objDB->close();

// HTML����
echo $doc->saveHTML();
