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
// �ܺ٥�����ɽ��
$existsDetail = array_key_exists("btndetail", $displayColumns);
// ���ꥫ����ɽ��
$existsDecide = array_key_exists("btndecide", $displayColumns);
// ���򥫥���ɽ��
$existsHistory = array_key_exists("btnhistory", $displayColumns);
// �����å�����ɽ��
$existsCancel = array_key_exists("btncancel", $displayColumns);

// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_SO3, $objAuth);
// ����ܥ����ɽ��
$allowedDecide = fncCheckAuthority(DEF_FUNCTION_SO4, $objAuth);
// �����å�����ɽ��
$allowedCancel = fncCheckAuthority(DEF_FUNCTION_SO5, $objAuth);

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

// �ܺ٤�ɽ��
if ($existsDetail) {
    // �ܺ٥����
    $thDetail = $doc->createElement("th", toUTF8("�ܺ�"));
    $thDetail->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thDetail);
}

// ������ܤ�ɽ��
if ($existsDecide) {
    // ���ꥫ���
    $thDecide = $doc->createElement("th", toUTF8("����"));
    $thDecide->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thDecide);
}

// ������ܤ�ɽ��
if ($existsHistory) {
    // ���򥫥��
    $thHistory = $doc->createElement("th", toUTF8("����"));
    $thHistory->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thHistory);
}
$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "��Ͽ��";
$aryTableHeaderName["lnginputusercode"] = "���ϼ�";
$aryTableHeaderName["strordercode"] = "ȯ��Σ�.";
$aryTableHeaderName["lngrevisionno"] = "��ӥ�����ֹ�";
$aryTableHeaderName["strproductcode"] = "���ʥ�����";
$aryTableHeaderName["strproductname"] = "����̾";
$aryTableHeaderName["strproductenglishname"] = "����̾�ʱѸ��";
$aryTableHeaderName["lnginchargegroupcode"] = "�Ķ�����";
$aryTableHeaderName["lnginchargeusercode"] = "��ȯô����";
$aryTableHeaderName["lngcustomercode"] = "������";
$aryTableHeaderName["lngstocksubjectcode"] = "��������";
$aryTableHeaderName["lngstockitemcode"] = "��������";
$aryTableHeaderName["dtmdeliverydate"] = "Ǽ��";
$aryTableHeaderName["lngorderstatuscode"] = "����";
$aryTableHeaderName["strgoodscode"] = "�ܵ�����";
$aryTableHeaderName["lngcustomercompanycode"] = "�ܵ�";
$aryTableHeaderName["lngreceivestatuscode"] = "����";
$aryTableHeaderName["lngrecordno"] = "���ٹ��ֹ�";
$aryTableHeaderName["curproductprice"] = "ñ��";
$aryTableHeaderName["lngproductquantity"] = "����";
$aryTableHeaderName["cursubtotalprice"] = "��ȴ���";
$aryTableHeaderName["strdetailnote"] = "��������";
// TODO �ץ�ե��������
// ���ꤵ�줿�ơ��֥���ܤΥ������������
foreach ($aryTableHeaderName as $key => $value) {
    if (array_key_exists($key, $displayColumns)) {
        $th = $doc->createElement("th", toUTF8($value));
        $trHead->appendChild($th);
    }
}

// ������ܤ�ɽ��
if ($existsCancel) {
    // ��������
    $thCancel = $doc->createElement("th", toUTF8("������"));
    $thCancel->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thCancel);
}

// thead > tr
$thead->appendChild($trHead);

// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
// ������̷��ʬ����
foreach ($records as $i => $record) {
    unset($aryQuery);
    // �����оݥե饰
    $decideObjFlag = false;
    // ����̵ͭ�ե饰
    $historyFlag = false;

    // Ʊ������NO,Ʊ�������ֹ�κǿ�����ǡ����Υ�ӥ�����ֹ���������
    $aryQuery[] = "SELECT";
    $aryQuery[] = " o.lngorderno, o.lngrevisionno ";
    $aryQuery[] = "FROM m_order o inner join t_orderdetail od ";
    $aryQuery[] = "on o.lngorderno = od.lngorderno ";
    $aryQuery[] = "AND o.lngrevisionno = od.lngrevisionno ";
    $aryQuery[] = "WHERE o.strordercode='" . $record["strordercode"] . "' ";
    $aryQuery[] = "and od.lngorderdetailno=" . $record["lngorderdetailno"] . " ";
    $aryQuery[] = "and o.lngrevisionno >= 0";
    $aryQuery[] = "and o.bytInvalidFlag = FALSE ";
    $aryQuery[] = "order by o.lngorderno desc, o.lngrevisionno desc";

    // �������ʿ�פ�ʸ������Ѵ�
    $strQuery = implode("\n", $aryQuery);
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    // �������������ξ��
    if ($lngResultNum > 0) {
        if ($lngResultNum > 1) {
            $historyFlag = true;
        }
    }

    $objDB->freeResult($lngResultID);

    $decideObjFlag = fncCheckData($record["strordercode"], $objDB);

    // �طʿ�����
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else {
        $bgcolor = "background-color: #FFB2B2;";
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strordercode"] . "_" . $record["lngorderdetailno"]);

    // ����
    $index = $index + 1;
    $tdIndex = $doc->createElement("td", $index);
    $tdIndex->setAttribute("class", $exclude);
    $tdIndex->setAttribute("style", $bgcolor);
    $trBody->appendChild($tdIndex);

    // �ܺ٤�ɽ��
    if ($existsDetail) {
        // �ܺ٥���
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);
        $tdDetail->setAttribute("style", $bgcolor . "text-align: center;");

        // �ܺ٥ܥ����ɽ��
        if ($allowedDetail and $record["lngrevisionno"] >= 0) {
            // �ܺ٥ܥ���
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngorderno"]);
            $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDetail->setAttribute("class", "detail button");
            // td > img
            $tdDetail->appendChild($imgDetail);
        }
        // tr > td
        $trBody->appendChild($tdDetail);
    }

    // ������ܤ�ɽ��
    if ($existsDecide) {
        // ���ꥻ��
        $tdDecide = $doc->createElement("td");
        $tdDecide->setAttribute("class", $exclude);
        $tdDecide->setAttribute("style", $bgcolor . "text-align: center;");

        // ����ܥ����ɽ��
        if ($allowedDecide and $record["lngrevisionno"] >= 0 and $record["lngorderstatuscode"] == DEF_ORDER_APPLICATE and $decideObjFlag) {
            // ����ܥ���
            $imgDecide = $doc->createElement("img");
            $imgDecide->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgDecide->setAttribute("id", $record["lngorderno"]);
            $imgDecide->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDecide->setAttribute("class", "decide button");
            // td > img
            $tdDecide->appendChild($imgDecide);
        }
        // tr > td
        $trBody->appendChild($tdDecide);
    }

    // ������ܤ�ɽ��
    if ($existsHistory) {
        // ���򥻥�
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");
        if ($historyFlag and array_key_exists("admin", $optionColumns)) {
            // ����ܥ���
            $imgHistory = $doc->createElement("img");
            $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgHistory->setAttribute("id", $record["strordercode"] . "_" . $record["lngorderdetailno"]);
            $imgHistory->setAttribute("lngrevisionno", $record["lngrevisionno"]);
            $imgHistory->setAttribute("rownum", $index);
            $imgHistory->setAttribute("class", "history button");
            // td > img
            $tdHistory->appendChild($imgHistory);
        }
        // tr > td
        $trBody->appendChild($tdHistory);
    }

    // �إå������ǡ�������
    fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName, $displayColumns, $record, true);

    // �����ù��ܤ�ɽ��
    if ($existsCancel) {
        // �����å���
        $tdCancel = $doc->createElement("td");
        $tdCancel->setAttribute("class", $exclude);
        $tdCancel->setAttribute("style", $bgcolor . "text-align: center;");

        // �����åܥ����ɽ��
        if ($allowedCancel and $record["lngrevisionno"] >= 0 and $record["lngorderstatuscode"] == DEF_ORDER_ORDER and $decideObjFlag) {
            // �����åܥ���
            $imgCancel = $doc->createElement("img");
            $imgCancel->setAttribute("src", "/img/type01/so/cancel_off_bt.gif");
            $imgCancel->setAttribute("id", $record["lngorderno"]);
            $imgCancel->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgCancel->setAttribute("class", "cancel button");
            // td > img
            $tdCancel->appendChild($imgCancel);
        }
        // tr > td
        $trBody->appendChild($tdCancel);
    }

    // tbody > tr
    $tbody->appendChild($trBody);

}

// HTML����
echo $doc->saveHTML();
