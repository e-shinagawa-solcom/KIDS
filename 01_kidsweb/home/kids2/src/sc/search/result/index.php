<?php

// ----------------------------------------------------------------------------
/**
 *       ������  ����
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
require LIB_DEBUGFILE;
require SRC_ROOT . "sc/cmn/lib_sc.php";

// DB��³
$objDB = new clsDB();
$objAuth = new clsAuth();
$objCache = new clsCache();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// ���å���󡢸��³�ǧ
//////////////////////////////////////////////////////////////////////////
// ���å�����ǧ
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

// ������桼���������ɤμ���
$lngInputUserCode = $objAuth->UserCode;

// ���³�ǧ
// 602 �������ʼ�������
if (!fncCheckAuthority(DEF_FUNCTION_SC1, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

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


// �������ܤ�����פ���ǿ��λ����ǡ������������SQLʸ�κ����ؿ�
$subStrQuery = fncGetMaxSalesSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns);

$strQuery = fncGetSalesByStrSalesCodeSQL($subStrQuery);


// �ͤ�Ȥ� =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
// �������������ξ��
if ($lngResultNum > 0) {
    // ������ʾ�ξ�票�顼��å�������ɽ������
    if ($lngResultNum > DEF_SEARCH_MAX) {
        $errorFlag = true;
        $lngErrorCode = 9057;
        $aryErrorMessage = DEF_SEARCH_MAX;
    }
} else {
    $errorFlag = true;
    $lngErrorCode = 603;
    $aryErrorMessage = "";
}

if ($errorFlag) {
    // ���顼���̤������
    $strReturnPath = "../sc/search/index.php?strSessionID=" . $aryData["strSessionID"];

    $strMessage = fncOutputError($lngErrorCode, DEF_WARNING, $aryErrorMessage, false, $strReturnPath, $objDB);

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
    $records = pg_fetch_all($lngResultID);
}

$objDB->freeResult($lngResultID);

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/sc/search/sc_search_result.html");

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
// ���򥫥���ɽ��
$existsHistory = array_key_exists("btnhistory", $displayColumns);

// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_SC11, $objAuth);

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

// �����ɽ��
if ($existsHistory) {
    // ���򥫥��
    $thHistory = $doc->createElement("th", toUTF8("����"));
    $thHistory->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thHistory);
}

$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "��Ͽ��";
$aryTableHeaderName["dtmappropriationdate"] = "������";
$aryTableHeaderName["strsalescode"] = "���NO.";
$aryTableHeaderName["lngrevisionno"] = "��ӥ�����ֹ�";
$aryTableHeaderName["strcustomerreceivecode"] = "�ܵҼ����ֹ�";
$aryTableHeaderName["strslipcode"] = "Ǽ�ʽ�NO.";
$aryTableHeaderName["lnginputusercode"] = "���ϼ�";
$aryTableHeaderName["lngcustomercompanycode"] = "�ܵ�";
$aryTableHeaderName["lngsalesstatuscode"] = "����";
$aryTableHeaderName["strnote"] = "����";
$aryTableHeaderName["curtotalprice"] = "��׶��";
$aryTableDetailHeaderName["lngrecordno"] = "���ٹ��ֹ�";
$aryTableDetailHeaderName["strproductcode"] = "���ʥ�����";
$aryTableDetailHeaderName["lnginchargegroupcode"] = "�Ķ�����";
$aryTableDetailHeaderName["lnginchargeusercode"] = "��ȯô����";
$aryTableDetailHeaderName["strproductname"] = "����̾";
$aryTableDetailHeaderName["lngsalesclasscode"] = "����ʬ";
$aryTableDetailHeaderName["strgoodscode"] = "�ܵ�����";
$aryTableDetailHeaderName["curproductprice"] = "ñ��";
$aryTableDetailHeaderName["lngproductunitcode"] = "ñ��";
$aryTableDetailHeaderName["lngproductquantity"] = "����";
$aryTableDetailHeaderName["cursubtotalprice"] = "��ȴ���";
$aryTableDetailHeaderName["lngtaxclasscode"] = "�Ƕ�ʬ";
$aryTableDetailHeaderName["curtax"] = "��Ψ";
$aryTableDetailHeaderName["curtaxprice"] = "�ǳ�";
$aryTableDetailHeaderName["strdetailnote"] = "��������";
// TODO �ץ�ե��������
// ���ꤵ�줿�ơ��֥���ܤΥ������������
foreach ($aryTableHeaderName as $key => $value) {
    if (array_key_exists($key, $displayColumns)) {
        $th = $doc->createElement("th", toUTF8($value));
        $trHead->appendChild($th);
    }
}

// ���ꤵ�줿�ơ��֥���ܤΥ������������
foreach ($aryTableDetailHeaderName as $key => $value) {
    if (array_key_exists($key, $displayColumns)) {
        $th = $doc->createElement("th", toUTF8($value));
        $trHead->appendChild($th);

        if ($key == "strproductname") {
            $th = $doc->createElement("th", toUTF8("����̾�ʱѸ��"));
            $trHead->appendChild($th);
        }
    }
}

// thead > tr
$thead->appendChild($trHead);

// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
// ������̷��ʬ����
foreach ($records as $i => $record) {
    unset($aryQuery);
    // ��ӥ�����ֹ�
    $revisionNos = "";
    // �����ֹ�
    $detailnos = "";
    // �ǿ���夫�ɤ����Υե饰
    $isMaxSales = false;
    // ����̵ͭ�ե饰
    $historyFlag = false;

    // Ʊ�����NO�κǿ����ǡ����Υ�ӥ�����ֹ���������
    $aryQuery[] = "SELECT";
    $aryQuery[] = " lngsalesno, lngrevisionno ";
    $aryQuery[] = "FROM m_sales";
    $aryQuery[] = "WHERE strsalescode='" . $record["strsalescode"] . "' ";
    $aryQuery[] = "and lngrevisionno >= 0";
    $aryQuery[] = "and bytInvalidFlag = FALSE ";
    $aryQuery[] = "order by lngrevisionno desc";

    // �������ʿ�פ�ʸ������Ѵ�
    $strQuery = implode("\n", $aryQuery);
// echo $strQuery;return;
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    // �������������ξ��
    if ($lngResultNum > 0) {
        if ($lngResultNum > 1) {
            $historyFlag = true;
        }
        for ($j = 0; $j < $lngResultNum; $j++) {
            if ($j == 0) {
                $maxSalesInfo = $objDB->fetchArray($lngResultID, $j);
                if ($maxSalesInfo["lngrevisionno"] == $record["lngrevisionno"]) {
                    $isMaxSales = true;
                }
            } else {
                $salesInfo = $objDB->fetchArray($lngResultID, $j);
                if ($revisionNos == "") {
                    $revisionNos = $salesInfo["lngrevisionno"];
                } else {
                    $revisionNos = $revisionNos . "," . $salesInfo["lngrevisionno"];
                }
            }
        }
    }

    $objDB->freeResult($lngResultID);

    // �ܺ٥ǡ������������
    $detailData = fncGetDetailData($record["lngsalesno"], $record["lngrevisionno"], $objDB);
    $rowspan = count($detailData);

    // �طʿ�����
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else if ($isMaxSales) {
        $bgcolor = "background-color: #FFB2B2;";
    } else {
        $bgcolor = "background-color: #FEEF8B;";
    }

    // �����ֹ����
    for ($i = $rowspan; $i > 0; $i--) {
        if ($detailnos == "") {
            $detailnos = $detailData[$i]["lngsalesdetailno"];
        } else {
            $detailnos = $detailnos . "," . $detailData[$i]["lngsalesdetailno"];
        }
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    if (!$isMaxSales) {
        $trBody->setAttribute("style", "display: none;");
        $trBody->setAttribute("id", $record["strsalescode"] . "_" . $record["lngrevisionno"]);
    } else {
        $trBody->setAttribute("id", $record["strsalescode"]);
    }
    $trBody->setAttribute("detailnos", $detailnos);

    // ����
    if ($isMaxSales) {
        $index = $index + 1;
        $subnum = 1;
        $tdIndex = $doc->createElement("td", $index);
    } else {
        $subindex = $index . "." . ($subnum++);
        $tdIndex = $doc->createElement("td", $subindex);
    }
    $tdIndex->setAttribute("class", $exclude);
    $tdIndex->setAttribute("style", $bgcolor);
    $tdIndex->setAttribute("rowspan", $rowspan);
    $trBody->appendChild($tdIndex);

    // �ܺ٤�ɽ��
    if ($existsDetail) {
        // �ܺ٥���
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);
        $tdDetail->setAttribute("style", $bgcolor. "text-align: center;");
        $tdDetail->setAttribute("rowspan", $rowspan);

        // �ܺ٥ܥ����ɽ��
        if ($allowedDetail and $record["lngrevisionno"] >= 0) {
            // �ܺ٥ܥ���
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngsalesno"]);
            $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDetail->setAttribute("class", "detail button");
            // td > img
            $tdDetail->appendChild($imgDetail);
        }
        // tr > td
        $trBody->appendChild($tdDetail);
    }

    // ������ܤ�ɽ��
    if ($existsHistory) {
        // ���򥻥�
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor. "text-align: center;");
        $tdHistory->setAttribute("rowspan", $rowspan);

        if ($isMaxSales and $historyFlag and array_key_exists("admin", $optionColumns)) {
            // ����ܥ���
            $imgHistory = $doc->createElement("img");
            $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgHistory->setAttribute("id", $record["strsalescode"]);
            $imgHistory->setAttribute("revisionnos", $revisionNos);
            $imgHistory->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgHistory->setAttribute("maxdetailno", $detailData[$rowspan - 1]["lngsalesdetailno"]);
            $imgHistory->setAttribute("class", "history button");
            // td > img
            $tdHistory->appendChild($imgHistory);
        }
        // tr > td
        $trBody->appendChild($tdHistory);
    }

    // TODO �ץ�ե��������
    // ���ꤵ�줿�ơ��֥���ܤΥ�����������
    foreach ($aryTableHeaderName as $key => $value) {
        // ɽ���оݤΥ����ξ��
        if (array_key_exists($key, $displayColumns)) {
            // �����̤�ɽ���ƥ����Ȥ�����
            switch ($key) {
                // ��Ͽ��
                case "dtminsertdate":
                    $td = $doc->createElement("td", $record["dtminsertdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ������
                case "dtmappropriationdate":
                    $td = $doc->createElement("td", $record["dtmappropriationdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ���NO.
                case "strsalescode":
                    $td = $doc->createElement("td", $record["strsalescode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ��ӥ�����ֹ�
                case "lngrevisionno":
                    $td = $doc->createElement("td", $record["lngrevisionno"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // �ܵҼ����ֹ�
                case "strcustomerreceivecode":
                    $td = $doc->createElement("td", $record["strcustomerreceivecode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // Ǽ�ʽ�NO.
                case "strslipcode":
                    $td = $doc->createElement("td", $record["strslipcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // �ܵҼ����ֹ�
                case "strcustomerreceivecode":
                    $td = $doc->createElement("td", $record["strcustomerreceivecode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // [���ϼ�ɽ��������] ���ϼ�ɽ��̾
                case "lnginputusercode":
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // [�ܵ�ɽ��������] ���ϼ�ɽ��̾
                case "lngcustomercompanycode":
                    $textContent = "[" . $record["strcustomercompanycode"] . "]" . " " . $record["strcustomercompanyname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "lngsalesstatuscode":
                    $td = $doc->createElement("td", toUTF8($record["strsalesstatusname"]));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "strnote":
                    $td = $doc->createElement("td", toUTF8($record["strnote"]));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ��׶��
                case "curtotalprice":
                    $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curtotalprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
            }
        }
    }

    // ���٥ǡ���������
    fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[0]);

    // tbody > tr
    $tbody->appendChild($trBody);

    // ���ٹԤ�tr���ɲ�
    for ($i = 1; $i < $rowspan; $i++) {
        $trBody = $doc->createElement("tr");
        if (!$isMaxSales) {
            $trBody->setAttribute("style", "display: none;");
        }
        $trBody->setAttribute("id", $record["strsalescode"] . "_" . $record["lngrevisionno"] . "_" . $detailData[$i]["lngsalesdetailno"]);

        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[$i]);

        $tbody->appendChild($trBody);

    }

}

// HTML����
echo $doc->saveHTML();
