<?php

// ----------------------------------------------------------------------------
/**
 *       Ǽ�ʽ����
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
 *         ��Ǽ�ʽ�ǡ���������̲���ɽ������
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
require SRC_ROOT . "sc/cmn/lib_scd.php";
// require SRC_ROOT . "sc/cmn/column_scd.php";
require SRC_ROOT . "search/cmn/lib_search.php";
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
// 602 ����������帡����
if (!fncCheckAuthority(DEF_FUNCTION_SC2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}
// 602 ����������帡����
if (!fncCheckAuthority(DEF_FUNCTION_SC2, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}
// ��������  $arySearchColumn�˳�Ǽ
$arySearchColumn = $isSearch;

if (!$bytSearchFlag) {
    reset($arySearchColumn);
}
reset($aryData);

// ����SQL��¹Ԥ������ʥҥåȡ˷�����������
$strQuery = fncGetSearchSlipSQL($arySearchColumn, $aryData, $objDB, "", 0, $aryData["strSessionID"]);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    // ���������������ʾ�ξ�票�顼��å�������ɽ������
    if ($lngResultNum > DEF_SEARCH_MAX) {
        $strMessage = fncOutputError(9057, DEF_WARNING, DEF_SEARCH_MAX, false, "../sc/search2/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

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
        $records[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    $strMessage = fncOutputError(603, DEF_WARNING, "", false, "../sc/search2/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

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
$objTemplate->getTemplate("/sc/result2/sc_search_result.html");

// // �ƥ�ץ졼������
// $objTemplate->replace($aryResult);

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



// -------------------------------------------------------
// �Ƽ�ܥ���ɽ�������å�/���¥����å�
// -------------------------------------------------------
$aryAuthority = fncGetAryAuthority('slip', $objAuth);

// �����ԥ⡼�ɥ����å�
$isadmin = !array_key_exists("admin", $optionColumns);

// �ơ��֥�إå�����
// -------------------------------------------------------
// thead > tr���Ǻ���
$trHead = $doc->createElement("tr");
fncSetTheadData($doc, $trHead, $aryTableHeadBtnName_SLIP, $aryTableBackBtnName_SLIP, $aryTableHeaderName_SLIP, $aryTableDetailHeaderName_SLIP, null);
$thead->appendChild($trHead);
// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
// ������̷��ʬ����
foreach ($records as $i => $record) {
    $index = $index + 1;

    $bgcolor = fncSetBgColor('slip', $record["lngslipno"], true, $objDB);

    $detailData = array();
    $rowspan == 0;

    // �ܺ٥ǡ������������
    $detailData = fncGetDetailData('slip', $record["lngslipno"], $record["lngrevisionno"], $objDB);
    $rowspan = count($detailData);

    if ($rowspan == 0) {
        $rowspan = 1;
    }
    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");

    $trBody->setAttribute("id", $record["lngslipno"]);

    $maxdetailno = $detailData[$rowspan - 1]["lngslipdetailno"];

    // ��Ƭ�ܥ�������
    fncSetHeadBtnToTr($doc, $trBody, $bgcolor, $aryTableHeadBtnName_SLIP, null, $record, $aryAuthority, true, $isadmin, $index, 'slip', $maxdetailno);

    // �إå������ǡ�������
    fncSetHeadDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName_SLIP, null, $record, true);

    // �������ǡ�������
    fncSetDetailTable($doc, $trBody, $bgcolor, $aryTableDetailHeaderName_SLIP, null, $record, $detailData, true, true);
    
    // �եå����ܥ���ɽ��
    fncSetBackBtnToTr($doc, $trBody, $bgcolor, $aryTableBackBtnName_SLIP, null, $record, $aryAuthority, true, $isadmin, 'slip');

    // tbody > tr
    $tbody->appendChild($trBody);

}

// HTML����
echo $doc->saveHTML();
