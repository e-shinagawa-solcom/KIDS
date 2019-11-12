<?php

// ----------------------------------------------------------------------------
/**
 *       �������  ����񸡺�����
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
 *         �������ǡ���������̲���ɽ������
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

// �����ɤ߹���
include_once 'conf.inc';

require_once SRC_ROOT . '/mold/lib/UtilSearchForm.class.php';

// �饤�֥���ɤ߹���
require LIB_FILE;
require LIB_ROOT . "clscache.php";
require SRC_ROOT . "inv/cmn/lib_regist.php";
require LIB_DEBUGFILE;

// DB��³
$objDB = new clsDB();
$objAuth = new clsAuth();
$objCache = new clsCache();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// POST(����GET)�ǡ�������
//////////////////////////////////////////////////////////////////////////
// �ե�����ǡ�������ƥ��ƥ���ο���ʬ����Ԥ�
$options = UtilSearchForm::extractArrayByOption($_REQUEST);
$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
$to = UtilSearchForm::extractArrayByTo($_REQUEST);
$searchValue = $_REQUEST;

$optionColumns = array();
// ���ץ������ܤ����
foreach ($options as $key => $flag) {
    if ($flag == "on") {
        $optionColumns[$key] = $key;
    }
}
$isSearch = array_keys($isSearch);
$aryData['SearchColumn'] = $isSearch;
foreach ($from as $key => $item) {
    $aryData[$key . 'From'] = $item;
}
foreach ($to as $key => $item) {
    $aryData[$key . 'To'] = $item;
}
foreach ($searchValue as $key => $item) {
    $aryData[$key] = $item;
}

// ���������ܼ���
// ������� $arySearchColumn�˳�Ǽ
if (empty($isSearch)) {
    //    fncOutputError( 502, DEF_WARNING, "�����оݹ��ܤ������å�����Ƥ��ޤ���",TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
    $bytSearchFlag = true;
}

//////////////////////////////////////////////////////////////////////////
// ���å���󡢸��³�ǧ
//////////////////////////////////////////////////////////////////////////
// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// ������桼���������ɤμ���
$lngInputUserCode = $objAuth->UserCode;

// ���³�ǧ
// 2200 �������
if (!fncCheckAuthority(DEF_FUNCTION_INV0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}
// 2202 ����񸡺�
if (!fncCheckAuthority(DEF_FUNCTION_INV2, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

// ��������  $arySearchColumn�˳�Ǽ
$arySearchColumn = $isSearch;

if (!$bytSearchFlag) {
    reset($arySearchColumn);
}
reset($aryData);

// ����SQL��¹Ԥ������ʥҥåȡ˷�����������
$strQuery = fncGetSearchInvoiceSQL($arySearchColumn, $aryData, $objDB, $aryData["strSessionID"]);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    // ���������������ʾ�ξ�票�顼��å�������ɽ������
    if ($lngResultNum > DEF_SEARCH_MAX) {
        $strMessage = fncOutputError(9057, DEF_WARNING, DEF_SEARCH_MAX, false, "../inv/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

        // [lngLanguageCode]�񤭽Ф�
        $aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

        // [strErrorMessage]�񤭽Ф�
        $aryHtml["strErrorMessage"] = $strMessage;

        // �ƥ�ץ졼���ɤ߹���
        $objTemplate = new clsTemplate();
        $objTemplate->getTemplate("/result/error/parts.tmpl");

        // �ƥ�ץ졼������
        $objTemplate->replace($aryHtml);
        $objTemplate->complete();

        // HTML����
        echo $objTemplate->strTemplate;

        exit;
    }

    // ���������Ǥ�����̾����
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    $strMessage = fncOutputError(9215, DEF_WARNING, "", false, "../inv/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

    // [lngLanguageCode]�񤭽Ф�
    $aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

    // [strErrorMessage]�񤭽Ф�
    $aryHtml["strErrorMessage"] = $strMessage;

    // �ƥ�ץ졼���ɤ߹���
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("/result/error/parts.tmpl");

    // �ƥ�ץ졼������
    $objTemplate->replace($aryHtml);
    $objTemplate->complete();

    // HTML����
    echo $objTemplate->strTemplate;

    exit;
}

$objDB->freeResult($lngResultID);

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/inv/result/search_result.html");

// �ƥ�ץ졼������
$objTemplate->replace($aryResult);

// ������̥ơ��֥������ΰ�DOMDocument�����
$doc = new DOMDocument();
// �ѡ������顼����
libxml_use_internal_errors(true);
// DOM�ѡ���
$doc->loadHTML(mb_convert_encoding($objTemplate->strTemplate, "utf8", "eucjp-win"));
// �ѡ������顼���ꥢ
libxml_clear_errors();
// �ѡ������顼�������
libxml_use_internal_errors(false);

// ������̥ơ��֥�μ���
$table = $doc->getElementById("result");
$thead = $table->getElementsByTagName("thead")->item(0);
$tbody = $table->getElementsByTagName("tbody")->item(0);

// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_PC4, $objAuth);
// ������ɽ��
$allowedFix = fncCheckAuthority(DEF_FUNCTION_PC5, $objAuth);
// �����ɽ��
$allowedDelete = fncCheckAuthority(DEF_FUNCTION_PC6, $objAuth);
// ̵��������ɽ��
$allowedInvalid = fncCheckAuthority(DEF_FUNCTION_PC7, $objAuth);

// -------------------------------------------------------
// �ơ��֥�إå�����
// -------------------------------------------------------
// thead > tr���Ǻ���
$trHead = $doc->createElement("tr");

// ����åץܡ��ɽ����оݥ��饹
$exclude = "exclude-in-clip-board-target";

// ���֥����
$thIndex = $doc->createElement("th");
$thIndex->setAttribute("class", $exclude);
// ���ԡ��ܥ���
$imgCopy = $doc->createElement("img");
$imgCopy->setAttribute("src", "/img/type01/cmn/seg/copy_off_bt.gif");
$imgCopy->setAttribute("class", "copy button");
// ���֥���� > ���ԡ��ܥ���
$thIndex->appendChild($imgCopy);
// �إå����ɲ�
$trHead->appendChild($thIndex);

// �ܺ٥����
$thDetail = $doc->createElement("th", toUTF8("�ܺ�"));
$thDetail->setAttribute("class", $exclude);
// �إå����ɲ�
$trHead->appendChild($thDetail);

// ���������
$thFix = $doc->createElement("th", toUTF8("����"));
$thFix->setAttribute("class", $exclude);
// �إå����ɲ�
$trHead->appendChild($thFix);

// ���򥫥��
$thHistory = $doc->createElement("th", toUTF8("����"));
$thHistory->setAttribute("class", $exclude);
// �إå����ɲ�
$trHead->appendChild($thHistory);

// �إå���
$aryTableHeaderName["lngCustomerCode"] = "�ܵ�";
$aryTableHeaderName["strInvoiceCode"] = "�����No";
$aryTableHeaderName["dtmInvoiceDate"] = "������";
$aryTableHeaderName["curLastMonthBalance"] = "�������ĳ�";
$aryTableHeaderName["curThisMonthAmount"] = "����������";
$aryTableHeaderName["curSubTotal1"] = "�����ǳ�";
$aryTableHeaderName["dtmInsertDate"] = "������";
$aryTableHeaderName["lngUserCode"] = "ô����";
$aryTableHeaderName["lngInsertUserCode"] = "���ϼ�";
$aryTableHeaderName["lngPrintCount"] = "�������";
$aryTableHeaderName["strNote"] = "����";

// ������
$aryTableDetailHeaderName["lngInvoiceDetailNo"] = "����������ֹ�";
$aryTableDetailHeaderName["dtmDeliveryDate"] = "Ǽ����";
$aryTableDetailHeaderName["strSlipCode"] = "Ǽ�ʽ�NO";
$aryTableDetailHeaderName["lngDeliveryPlaceCode"] = "Ǽ����";
$aryTableDetailHeaderName["curSubTotalPrice"] = "��ȴ���";
$aryTableDetailHeaderName["lngTaxClassCode"] = "���Ƕ�ʬ";
$aryTableDetailHeaderName["curDetailTax"] = "��Ψ";
$aryTableDetailHeaderName["curTaxPrice"] = "�����";
$aryTableDetailHeaderName["strDetailNote"] = "��������";

// TODO �ץ�ե��������
// ���ꤵ�줿�ơ��֥���ܤΥ������������
foreach ($aryTableHeaderName as $key => $value) {
    $th = $doc->createElement("th", toUTF8($value));
    $trHead->appendChild($th);
}
// ���٥إå������������
foreach ($aryTableDetailHeaderName as $key => $value) {
    $th = $doc->createElement("th", toUTF8($value));
    $trHead->appendChild($th);
}
// ������ܤ�ɽ��
// ��������
$thDelete = $doc->createElement("th", toUTF8("���"));
$thDelete->setAttribute("class", $exclude);
// �إå����ɲ�
$trHead->appendChild($thDelete);

// thead > tr
$thead->appendChild($trHead);

// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
// ������̷��ʬ����
foreach ($aryResult as $i => $record) {
    unset($aryQuery);
    // ����ե饰
    $deletedFlag = false;

    $deletedFlag = fncCheckData($record["strinvoicecode"], $objDB);

    // �ܺ٥ǡ������������
    $detailData = fncGetDetailData($record["lnginvoiceno"], $record["lngrevisionno"], $objDB);

    $rowspan = count($detailData);
    // �طʿ�����
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else {
        $bgcolor = "background-color: #FFB2B2;";
    }
    // �����ֹ����
    for ($i = $rowspan; $i > 0; $i--) {
        if ($detailnos == "") {
            $detailnos = $detailData[$i]["lnginvoicedetailno"];
        } else {
            $detailnos = $detailnos . "," . $detailData[$i]["lnginvoicedetailno"];
        }
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strinvoicecode"]);
    $trBody->setAttribute("detailnos", $detailnos);

    // ����
    $index = $index + 1;
    $tdIndex = $doc->createElement("td", $index);
    $tdIndex->setAttribute("class", $exclude);
    $tdIndex->setAttribute("style", $bgcolor);
    $tdIndex->setAttribute("rowspan", $rowspan);
    $trBody->appendChild($tdIndex);

    // �ܺ٥���
    $tdDetail = $doc->createElement("td");
    $tdDetail->setAttribute("class", $exclude);
    $tdDetail->setAttribute("style", $bgcolor . "text-align: center;");
    $tdDetail->setAttribute("rowspan", $rowspan);

    // �ܺ٥ܥ����ɽ��
    if ($allowedDetail && $record["lngrevisionno"] >= 0) {
        // �ܺ٥ܥ���
        $imgDetail = $doc->createElement("img");
        $imgDetail->setAttribute("src", "/img/type01/pc/detail_off_bt.gif");
        $imgDetail->setAttribute("lnginvoiceno", $record["lnginvoiceno"]);
        $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
        $imgDetail->setAttribute("class", "detail button");
        // td > img
        $tdDetail->appendChild($imgDetail);
    }
    // tr > td
    $trBody->appendChild($tdDetail);

    // ��������
    $tdFix = $doc->createElement("td");
    $tdFix->setAttribute("class", $exclude);
    $tdFix->setAttribute("style", $bgcolor . "text-align: center;");
    $tdFix->setAttribute("rowspan", $rowspan);

    // �����ܥ����ɽ��
    if ($allowedFix && $record["lngrevisionno"] >= 0 && !$deletedFlag) {
        // �����ܥ���
        $imgFix = $doc->createElement("img");
        $imgFix->setAttribute("src", "/img/type01/pc/renew_off_bt.gif");
        $imgFix->setAttribute("lnginvoiceno", $record["lnginvoiceno"]);
        $imgFix->setAttribute("revisionno", $record["lngrevisionno"]);
        $imgFix->setAttribute("class", "renew button");
        // td > img
        $tdFix->appendChild($imgFix);
    }
    // tr > td
    $trBody->appendChild($tdFix);

    // ���򥻥�
    $tdHistory = $doc->createElement("td");
    $tdHistory->setAttribute("class", $exclude);
    $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");
    $tdHistory->setAttribute("rowspan", $rowspan);

    if ($record["lngrevisionno"] <> 0 and array_key_exists("admin", $optionColumns)) {
        // ����ܥ���
        $imgHistory = $doc->createElement("img");
        $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
        $imgHistory->setAttribute("id", $record["strinvoicecode"]);
        $imgHistory->setAttribute("lngrevisionno", $record["lngrevisionno"]);
        $imgHistory->setAttribute("rownum", $index);
        $imgHistory->setAttribute("maxdetailno", $detailData[$rowspan - 1]["lnginvoicedetailno"]);
        $imgHistory->setAttribute("class", "history button");
        // td > img
        $tdHistory->appendChild($imgHistory);
    }
    // tr > td
    $trBody->appendChild($tdHistory);

    // �إå������ǡ���������
    fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $rowspan, $aryTableHeaderName, $record, true);
    

    // ���٥ǡ���������
    fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $detailData[0], $record, true);

    // tbody > tr
    $tbody->appendChild($trBody);

    // �������
    $tdDelete = $doc->createElement("td");
    $tdDelete->setAttribute("class", $exclude);
    $tdDelete->setAttribute("style", $bgcolor . "text-align: center;");
    $tdDelete->setAttribute("rowspan", $rowspan);

    // ����ܥ����ɽ��
    if (!$deletedFlag) {
        // ����ܥ���
        $imgDelete = $doc->createElement("img");
        $imgDelete->setAttribute("src", "/img/type01/pc/delete_off_bt.gif");
        $imgDelete->setAttribute("lnginvoiceno", $record["lnginvoiceno"]);
        $imgDelete->setAttribute("revisionno", $record["lngrevisionno"]);
        $imgDelete->setAttribute("class", "delete button");
        // td > img
        $tdDelete->appendChild($imgDelete);
    }
    // tr > td
    $trBody->appendChild($tdDelete);

    // tbody > tr
    $tbody->appendChild($trBody);

    // ���ٹԤ�tr���ɲ�
    for ($i = 1; $i < $rowspan; $i++) {
        $trBody = $doc->createElement("tr");

        $trBody->setAttribute("id", $record["strinvoicecode"] . "_" . $record["lngrevisionno"] . "_" . $detailData[$i]["lnginvoicedetailno"]);

        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $detailData[$i], $record, true);

        $tbody->appendChild($trBody);

    }
}

// HTML����
echo $doc->saveHTML();
