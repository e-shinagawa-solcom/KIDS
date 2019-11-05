<?php

// ----------------------------------------------------------------------------
/**
 *       ���ʴ���  ����
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
require SRC_ROOT . "p/cmn/lib_ps.php";
require SRC_ROOT . "p/cmn/lib_p.php";
require LIB_DEBUGFILE;

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

// 302 ���ʴ����ʾ��ʸ�����
if (!fncCheckAuthority(DEF_FUNCTION_P2, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
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
// ����ɽ�����ܼ���
if (empty($isDisplay)) {
    $strMessage = fncOutputError(9058, DEF_WARNING, "", false, "../so/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

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

// �������ܤ�����פ���ǿ��λ����ǡ������������SQLʸ�κ����ؿ�
$strQuery = fncGetMaxProductSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns);
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
    $lngErrorCode = 303;
    $aryErrorMessage = "";
}

if ($errorFlag) {
    // ���顼���̤������
    $strReturnPath = "../p/search/index.php?strSessionID=" . $aryData["strSessionID"];

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
$objTemplate->getTemplate("/p/search/p_search_result.html");

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
// ���򥫥���ɽ��
$existsHistory = array_key_exists("btnhistory", $displayColumns);

// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_P4, $objAuth);
// �ܺ�ɽ��������ǡ�����ɽ����
$allowedDetailDelete = fncCheckAuthority(DEF_FUNCTION_P5, $objAuth);
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
$thIndex->setAttribute("unselectable", "off");

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
if ($existsHistory) {
    // �ץ�ӥ塼�����
    $thHistory = $doc->createElement("th", toUTF8("����"));
    $thHistory->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thHistory);
}

$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "������";
$aryTableHeaderName["lnggoodsplanprogresscode"] = "���ʹԾ���";
$aryTableHeaderName["dtmupdatedate"] = "��������";
$aryTableHeaderName["strproductcode"] = "���ʥ�����";
$aryTableHeaderName["lngrevisionno"] = "��ӥ�����ֹ�";
$aryTableHeaderName["strproductname"] = "����̾";
$aryTableHeaderName["strproductenglishname"] = "����̾�ʱѸ��";
$aryTableHeaderName["lnginputusercode"] = "���ϼ�";
$aryTableHeaderName["lnginchargegroupcode"] = "�Ķ�����";
$aryTableHeaderName["lnginchargeusercode"] = "ô����";
$aryTableHeaderName["lngdevelopusercode"] = "��ȯô����";
$aryTableHeaderName["lngcategorycode"] = "���ƥ���";
$aryTableHeaderName["strgoodscode"] = "�ܵ�����";
$aryTableHeaderName["strgoodsname"] = "����̾��";
$aryTableHeaderName["lngcustomercompanycode"] = "�ܵ�";
$aryTableHeaderName["lngcustomerusercode"] = "�ܵ�ô����";
$aryTableHeaderName["lngpackingunitcode"] = "�ٻ�ñ��";
$aryTableHeaderName["lngproductunitcode"] = "����ñ��";
$aryTableHeaderName["lngproductformcode"] = "���ʷ���";
$aryTableHeaderName["lngboxquantity"] = "��Ȣ���ޡ�����";
$aryTableHeaderName["lngcartonquantity"] = "�����ȥ�����";
$aryTableHeaderName["lngproductionquantity"] = "����ͽ���";
$aryTableHeaderName["lngfirstdeliveryquantity"] = "���Ǽ�ʿ�";
$aryTableHeaderName["lngfactorycode"] = "��������";
$aryTableHeaderName["lngassemblyfactorycode"] = "���å���֥깩��";
$aryTableHeaderName["lngdeliveryplacecode"] = "Ǽ�ʾ��";
$aryTableHeaderName["dtmdeliverylimitdate"] = "Ǽ��";
$aryTableHeaderName["curproductprice"] = "Ǽ��";
$aryTableHeaderName["curretailprice"] = "����";
$aryTableHeaderName["lngtargetagecode"] = "�о�ǯ��";
$aryTableHeaderName["lngroyalty"] = "�����ƥ�";
$aryTableHeaderName["lngcertificateclasscode"] = "�ڻ�";
$aryTableHeaderName["lngcopyrightcode"] = "�Ǹ���";
$aryTableHeaderName["strcopyrightnote"] = "�Ǹ�������";
$aryTableHeaderName["strcopyrightdisplaystamp"] = "�Ǹ�ɽ���ʹ����";
$aryTableHeaderName["strcopyrightdisplayprint"] = "�Ǹ�ɽ���ʰ���ʪ��";
$aryTableHeaderName["strproductcomposition"] = "���ʹ���";
$aryTableHeaderName["strassemblycontents"] = "���å���֥�����";
$aryTableHeaderName["strspecificationdetails"] = "���;ܺ�";

// TODO �ץ�ե��������
// ���ꤵ�줿�ơ��֥���ܤΥ������������
foreach ($aryTableHeaderName as $key => $value) {
    if (array_key_exists($key, $displayColumns)) {
        $th = $doc->createElement("th", toUTF8($value));
        $trHead->appendChild($th);
    }
}

// thead > tr
$thead->appendChild($trHead);

// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
$index = 0;
// ������̷��ʬ����
foreach ($records as $i => $record) {
    unset($aryQuery);
    // ����ե饰
    $deletedFlag = false;
    // ����̵ͭ�ե饰
    $historyFlag = false;

    // Ʊ������NO�κǿ������ǡ����Υ�ӥ�����ֹ���������
    $aryQuery[] = "SELECT";
    $aryQuery[] = " lngproductno, lngrevisionno ";
    $aryQuery[] = "FROM m_product ";
    $aryQuery[] = "WHERE strproductcode='" . $record["strproductcode"] . "' ";
    $aryQuery[] = "order by lngrevisionno desc";

    // �������ʿ�פ�ʸ������Ѵ�
    $strQuery = implode("\n", $aryQuery);
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    // �������������ξ��
    if ($lngResultNum > 0) {
        if ($lngResultNum > 1) {
            $historyFlag = true;
        }
        for ($j = 0; $j < $lngResultNum; $j++) {
            $maxProductInfo = $objDB->fetchArray($lngResultID, 0);
            // �������ʤΥ�ӥ�����ֹ�<0�ξ�硢����ѤȤʤ�
            if ($maxProductInfo["lngrevisionno"] < 0) {
                $deletedFlag = true;
            }
        }
    }

    $objDB->freeResult($lngResultID);

    // �طʿ�����
    if ($record["strgroupdisplaycolor"]) {
        $bgcolor = "background-color: " . $record["strgroupdisplaycolor"] . ";";
    } else {
        $bgcolor = "background-color: #FFFFFF;";
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strproductcode"]);
    // if (!$isMaxproduct) {
    //     $trBody->setAttribute("id", $record["strproductcode"] . "_" . $record["lngrevisionno"]);
    //     $trBody->setAttribute("style", "display: none;");
    // }

    // ����
    // if ($isMaxproduct) {
    //     $index = $index + 1;
    //     $subnum = 1;
    //     $tdIndex = $doc->createElement("td", $index);
    // } else {
    //     $subindex = $index . "." . ($subnum++);
    //     $tdIndex = $doc->createElement("td", $subindex);
    // }
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
        // if (($allowedDetailDelete and $record["bytinvalidflag"] != "f") or ($allowedDetail and $record["bytinvalidflag"] == "f")) {
        if (($allowedDetailDelete) or ($allowedDetail and $record["lngrevisionno"] >= 0)) {
            // �ܺ٥ܥ���
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngproductno"]);
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
        $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");

        if ($historyFlag) {
            // ����ܥ���
            $imgHistory = $doc->createElement("img");
            $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgHistory->setAttribute("id", $record["strproductcode"]);
            $imgHistory->setAttribute("lngrevisionno", $record["lngrevisionno"]);
            $imgHistory->setAttribute("rownum", $index);
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
                // ������
                case "dtminsertdate":
                    $td = $doc->createElement("td", $record["dtminsertdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʹԾ���
                case "lnggoodsplanprogresscode":
                    $td = $doc->createElement("td", toUTF8($record["strgoodsplanprogressname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��������
                case "dtmupdatedate":
                    $td = $doc->createElement("td", $record["dtmupdatedate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʥ�����
                case "strproductcode":
                    $td = $doc->createElement("td", $record["strproductcode"] . "_" . $record["strrevisecode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��ӥ�����ֹ�
                case "lngrevisionno":
                    $td = $doc->createElement("td", $record["lngrevisionno"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ����̾
                case "strproductname":
                    $td = $doc->createElement("td", toUTF8($record["strproductname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����̾�ʱѸ��
                case "strproductenglishname":
                    $td = $doc->createElement("td", toUTF8($record["strproductenglishname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ϼ�
                case "lnginputusercode":
                    if ($record["strinputuserdisplaycode"] != "") {
                        $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ķ�����
                case "lnginchargegroupcode":
                    if ($record["strinchargegroupdisplaycode"] != "") {
                        $textContent = "[" . $record["strinchargegroupdisplaycode"] . "]" . " " . $record["strinchargegroupdisplayname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [ô����ɽ��������] ô����ɽ��̾
                case "lnginchargeusercode":
                    if ($record["strinchargeuserdisplaycode"] != "") {
                        $textContent = "[" . $record["strinchargeuserdisplaycode"] . "]" . " " . $record["strinchargeuserdisplayname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [��ȯô����ɽ��������] ��ȯô����ɽ��̾
                case "lngdevelopusercode":
                    if ($record["strdevelopuserdisplaycode"] != "") {
                        $textContent = "[" . $record["strdevelopuserdisplaycode"] . "]" . " " . $record["strdevelopuserdisplayname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ƥ���
                case "lngcategorycode":
                    $td = $doc->createElement("td", toUTF8($record["strcategoryname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ܵ�����
                case "strgoodscode":
                    $td = $doc->createElement("td", toUTF8($record["strgoodscode"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����̾��
                case "strgoodsname":
                    $td = $doc->createElement("td", toUTF8($record["strgoodsname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ܵ�
                case "lngcustomercompanycode":
                    if ($record["strcustomercompanycode"] != "") {
                        $textContent = "[" . $record["strcustomercompanycode"] . "]" . " " . $record["strcustomercompanyname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ܵ�ô����
                case "lngcustomerusercode":
                    if ($record["strcustomerusercode"] != "") {
                        $textContent = "[" . $record["strcustomerusercode"] . "]" . " " . $record["strcustomerusername"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ٻ�ñ��
                case "lngpackingunitcode":
                    $td = $doc->createElement("td", toUTF8($record["strpackingunitname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����ñ��
                case "lngproductunitcode":
                    $td = $doc->createElement("td", toUTF8($record["strproductunitname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʷ���
                case "lngproductformcode":
                    $td = $doc->createElement("td", toUTF8($record["strproductformname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��Ȣ���ޡ�����
                case "lngboxquantity":
                    $td = $doc->createElement("td", $record["lngboxquantity"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �����ȥ�����
                case "lngcartonquantity":
                    $td = $doc->createElement("td", $record["lngcartonquantity"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����ͽ���
                case "lngproductionquantity":
                    $td = $doc->createElement("td", $record["lngproductionquantity"] . " " . $record["strproductionunitname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���Ǽ�ʿ�
                case "lngfirstdeliveryquantity":
                    $td = $doc->createElement("td", $record["lngfirstdeliveryquantity"] . " " . $record["strfirstdeliveryunitname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��������
                case "lngfactorycode":
                    if ($record["strfactorycode"] != "") {
                        $textContent = "[" . $record["strfactorycode"] . "]" . " " . $record["strfactoryname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���å���֥깩��
                case "lngassemblyfactorycode":
                    if ($record["strassemblyfactorycode"] != "") {
                        $textContent = "[" . $record["strassemblyfactorycode"] . "]" . " " . $record["strassemblyfactoryname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // Ǽ�ʾ��
                case "lngdeliveryplacecode":
                    if ($record["strdeliveryplacecode"] != "") {
                        $textContent = "[" . $record["strdeliveryplacecode"] . "]" . " " . $record["strdeliveryplacename"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // Ǽ��
                case "dtmdeliverylimitdate":
                    $td = $doc->createElement("td", $record["dtmdeliverylimitdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // Ǽ��
                case "curproductprice":
                    if ($record["curproductprice"] != "") {
                        $textContent = "&yen;" . " " . $record["curproductprice"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "curretailprice":
                    if ($record["curretailprice"] != "") {
                        $textContent = "&yen;" . " " . $record["curretailprice"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �о�ǯ��
                case "lngtargetagecode":
                    $td = $doc->createElement("td", toUTF8($record["strtargetagename"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �����ƥ�
                case "lngroyalty":
                    $td = $doc->createElement("td", $record["lngroyalty"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ڻ�
                case "lngcertificateclasscode":
                    $td = $doc->createElement("td", toUTF8($record["strcertificateclassname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ǹ���
                case "lngcopyrightcode":
                    $td = $doc->createElement("td", toUTF8($record["strcopyrightname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ǹ�������
                case "strcopyrightnote":
                    $td = $doc->createElement("td", toUTF8($record["strcopyrightnote"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ǹ�ɽ���ʹ����
                case "strcopyrightdisplaystamp":
                    $td = $doc->createElement("td", toUTF8($record["strcopyrightdisplaystamp"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ǹ�ɽ���ʰ���ʪ��
                case "strcopyrightdisplayprint":
                    $td = $doc->createElement("td", toUTF8($record["strcopyrightdisplayprint"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʹ���
                case "strproductcomposition":
                    $td = $doc->createElement("td", toUTF8("��" . $record["strproductcomposition"] . "�異�å���֥�"));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���å���֥�����
                case "strassemblycontents":
                    $td = $doc->createElement("td", toUTF8($record["strassemblycontents"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���;ܺ�
                case "strspecificationdetails":
                    $td = $doc->createElement("td", toUTF8($record["strspecificationdetails"]));
                    $td->setAttribute("style", $bgcolor . "white-space: pre; ");
                    // $td->setAttribute("style", "white-space: pre; ");
                    $trBody->appendChild($td);
                    break;

            }
        }
    }

    // tbody > tr
    $tbody->appendChild($trBody);

}

// HTML����
echo $doc->saveHTML();
