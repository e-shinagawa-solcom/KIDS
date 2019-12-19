<?php

// ----------------------------------------------------------------------------
/**
 *       ȯ�����  ����
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
 *         ��������̲���ɽ������
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
require SRC_ROOT . "po/cmn/lib_pos.php";
require SRC_ROOT . "search/cmn/lib_search.php";
require SRC_ROOT . "po/cmn/column.php";
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
$isDisplay = UtilSearchForm::extractArrayByIsDisplay($_REQUEST);
$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
$to = UtilSearchForm::extractArrayByTo($_REQUEST);
$searchValue = $_REQUEST;
$errorFlag = false;

// ���������Ω�˻��Ѥ���ե�����ǡ��������
$optionColumns = array();
$searchColumns = array();
$displayColumns = array();
$conditions = array();

// ���ץ������ܤ����
foreach ($options as $key => $flag) {
    if ($flag == "on") {
        $optionColumns[$key] = $key;
    }
}
// ɽ�����ܤ����
foreach ($isDisplay as $key => $flag) {
    if ($flag == "on") {
        $displayColumns[$key] = $key;
    }
}

// �������ܤ����
foreach ($isSearch as $key => $flag) {
    if ($flag == "on") {
        $searchColumns[$key] = $key;
    }
}

//////////////////////////////////////////////////////////////////////////
// ���å���󡢸��³�ǧ
//////////////////////////////////////////////////////////////////////////
// ���å�����ǧ
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

// ���³�ǧ
// 502 ȯ�������ȯ������
if (!fncCheckAuthority(DEF_FUNCTION_PO2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

// �������˰��פ���ȯ�����ɤ��������SQLʸ�κ���
$strQuery = fncGetSearchPurchaseSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns);

// �ͤ�Ȥ� =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    // ���������������ʾ�ξ�票�顼��å�������ɽ������
    if ($lngResultNum > DEF_SEARCH_MAX) {
        $strMessage = fncOutputError(9057, DEF_WARNING, DEF_SEARCH_MAX, false, "../po/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

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

    for ($i = 0; $i < $lngResultNum; $i++) {
        $records[] = $objDB->fetchArray($lngResultID, $i);
	}
	
} else {
    $strMessage = fncOutputError(503, DEF_WARNING, "", false, "../po/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

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
$objTemplate->getTemplate("/po/result/po_search_result.html");

$aryResult["displayColumns"] = implode(",", $displayColumns);
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

// ����ʸ�����ʸ�����Ѵ�
$displayColumns = array_change_key_case($displayColumns, CASE_LOWER);
// -------------------------------------------------------
// �Ƽ�ܥ���ɽ�������å�/���¥����å�
// -------------------------------------------------------
$aryAuthority = fncGetAryAuthority('po', $objAuth);

// �����ԥ⡼�ɥ����å�
$isadmin = array_key_exists("admin", $optionColumns);

// -------------------------------------------------------
// �ơ��֥�إå�����
// -------------------------------------------------------
// thead > tr���Ǻ���
// -------------------------------------------------------
// �ơ��֥�إå�����
// -------------------------------------------------------
// thead > tr���Ǻ���
$trHead = $doc->createElement("tr");
fncSetTheadData($doc, $trHead, $aryTableHeadBtnName, $aryTableBackBtnName, $aryTableHeaderName_PO, null, $displayColumns);
$thead->appendChild($trHead);
// return;
// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
// ������̷��ʬ����
foreach ($records as $i => $record) {
    $index = $index + 1;

    $bgcolor = fncSetBgColor('po', $record["strordercode"], true, $objDB);

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");

    $trBody->setAttribute("id", $record["strordercode"] . "_" . $record["lngorderdetailno"]);

    // ��Ƭ�ܥ�������
    fncSetHeadBtnToTr($doc, $trBody, $bgcolor, $aryTableHeadBtnName, $displayColumns, $record, $aryAuthority, true, $isadmin, $index, 'po', null);

    // �إå������ǡ�������
    fncSetHeadDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName_PO, $displayColumns, $record, true);

    // �եå����ܥ���ɽ��
    fncSetBackBtnToTr($doc, $trBody, $bgcolor, $aryTableBackBtnName, $displayColumns, $record, $aryAuthority, true, $isadmin, 'po');

    // tbody > tr
    $tbody->appendChild($trBody);

}

// HTML����
echo $doc->saveHTML();
