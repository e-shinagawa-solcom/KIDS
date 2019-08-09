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

// ���������Ω��
$aryQuery = array();
$aryQuery[] = "SELECT distinct";
$aryQuery[] = "  p.lngProductNo as lngProductNo";
$aryQuery[] = "  , p.lngInChargeGroupCode as lngGroupCode";
$aryQuery[] = "  , p.bytInvalidFlag as bytInvalidFlag";
$aryQuery[] = "  , to_char(p.dtmInsertDate, 'YYYY/MM/DD') as dtmInsertDate";
$aryQuery[] = "  , p.strProductCode as strProductCode";
$aryQuery[] = "  , p.strProductName as strProductName";
$aryQuery[] = "  , p.strproductenglishname as strproductenglishname";
$aryQuery[] = "  , p.lngInputUserCode as lngInputUserCode";
$aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
$aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
$aryQuery[] = "  , p.lngInChargeGroupCode as lngInChargeGroupCode";
$aryQuery[] = "  , inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
$aryQuery[] = "  , inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
$aryQuery[] = "  , p.lngInChargeUserCode as lngInChargeUserCode";
$aryQuery[] = "  , inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
$aryQuery[] = "  , inchg_u.strUserDisplayName as strInChargeUserDisplayName ";
$aryQuery[] = "  , p.lngdevelopusercode as lngdevelopusercode";
$aryQuery[] = "  , devp_u.strUserDisplayCode as strDevelopUserDisplayCode";
$aryQuery[] = "  , devp_u.strUserDisplayName as strDevelopUserDisplayName ";
$aryQuery[] = "FROM";
$aryQuery[] = "  m_Product p ";
$aryQuery[] = "  LEFT JOIN m_User input_u ";
$aryQuery[] = "    ON p.lngInputUserCode = input_u.lngUserCode ";
$aryQuery[] = "  LEFT JOIN m_Group inchg_g ";
$aryQuery[] = "    ON p.lngInChargeGroupCode = inchg_g.lngGroupCode ";
$aryQuery[] = "  LEFT JOIN m_User inchg_u ";
$aryQuery[] = "    ON p.lngInChargeUserCode = inchg_u.lngUserCode ";
$aryQuery[] = "  LEFT JOIN m_User devp_u ";
$aryQuery[] = "    ON p.lngDevelopUsercode = devp_u.lngUserCode ";
$aryQuery[] = "WHERE";
$aryQuery[] = "  p.lngProductNo >= 0 ";
// ��Ͽ��
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) &&
    array_key_exists("dtmInsertDate", $to)) {
    $aryQuery[] = " AND p.dtmInsertDate" .
        " between '" . $from["dtmInsertDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmInsertDate"] . " 23:59:59.99999'";
}
// ���ʥ�����
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $searchValue)) {
    $strProductCodeArray = explode(",", $searchValue["strProductCode"]);
    $aryQuery[] = " AND (";
    $count = 0;
    foreach ($strProductCodeArray as $strProductCode) {
        $count += 1;
        if ($count != 1) {
            $aryQuery[] = " OR ";
        }
        $aryQuery[] = "p.strProductCode = '" . $strProductCode . "'";
    }
    $aryQuery[] = ")";
}
// ����̾��
if (array_key_exists("strProductName", $searchColumns) &&
    array_key_exists("strProductName", $searchValue)) {
    $aryQuery[] = "UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
}
// ����̾��(�Ѹ�)
if (array_key_exists("strProductEnglishName", $searchColumns) &&
    array_key_exists("strProductEnglishName", $searchValue)) {
    $aryQuery[] = "UPPER(p.strproductenglishname) like UPPER('%" . pg_escape_string($searchValue["strProductEnglishName"]) . "%')";
}
// ���ϼ�
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND input_u.strUserDisplayCode ~ * '" . $searchValue["lngInputUserCode"] . "'";
}
// �Ķ�����
if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
    array_key_exists("lngInChargeGroupCode", $searchValue)) {
    $aryQuery[] = "inchg_g.strGroupDisplayCode = '" . pg_escape_string($searchValue["lngInChargeGroupCode"]) . "'";
}

// ô����
if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
    array_key_exists("lngInChargeUserCode", $searchValue)) {
    $aryQuery[] = "inchg_u.strUserDisplayCode = '" . pg_escape_string($searchValue["lngInChargeUserCode"]) . "'";
}

// ��ȯô����
if (array_key_exists("lngDevelopUsercode", $searchColumns) &&
    array_key_exists("lngDevelopUsercode", $searchValue)) {
    $aryQuery[] = "devp_u.strUserDisplayCode = '" . pg_escape_string($searchValue["lngDevelopUsercode"]) . "'";
}
$aryQuery[] = "ORDER BY";
$aryQuery[] = "  p.lngProductNo ASC";

// �������ʿ�פ�ʸ������Ѵ�
$strQuery = implode("\n", $aryQuery);

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
    $lngErrorCode = 9057;
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
// ����������ɽ��
$existsModify = array_key_exists("btnmodify", $displayColumns);
// ���Τ�ɽ��
$existsResale = array_key_exists("btnresale", $displayColumns);
// ���򥫥���ɽ��
$existsRecord = array_key_exists("btnrecord", $displayColumns);

// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_SO3, $objAuth);
// �����ܥ����ɽ��
$allowedModify = fncCheckAuthority(DEF_FUNCTION_SO4, $objAuth);
// ���Υ�����ɽ��
$allowedResale = fncCheckAuthority(DEF_FUNCTION_SO5, $objAuth);

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

// �������ܤ�ɽ��
if ($existsModify) {
    // ���ꥫ���
    $thModify = $doc->createElement("th", toUTF8("����"));
    $thModify->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thModify);
}

// ������ܤ�ɽ��
if ($existsRecord) {
    // �ץ�ӥ塼�����
    $thRecord = $doc->createElement("th", toUTF8("����"));
    $thRecord->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thRecord);
}

// ���ι��ܤ�ɽ��
if ($existsResale) {
    // �ץ�ӥ塼�����
    $thResale = $doc->createElement("th", toUTF8("����"));
    $thResale->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thResale);
}

$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "������";
$aryTableHeaderName["lnginputusercode"] = "���ϼ�";
$aryTableHeaderName["strproductcode"] = "���ʥ�����";
$aryTableHeaderName["strproductname"] = "����̾";
$aryTableHeaderName["strproductenglishname"] = "����̾�ʱѸ��";
$aryTableHeaderName["lnginchargegroupcode"] = "�Ķ�����";
$aryTableHeaderName["lnginchargeusercode"] = "ô����";
$aryTableHeaderName["lngdevelopusercode"] = "��ȯô����";
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
// ������̷��ʬ����
foreach ($records as $i => $record) {
    $index = $i + 1;

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");

    // ����
    $tdIndex = $doc->createElement("td", $index);
    $tdIndex->setAttribute("class", $exclude);
    $trBody->appendChild($tdIndex);

    // �ܺ٤�ɽ��
    if ($existsDetail) {
        // �ܺ٥���
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);

        // �ܺ٥ܥ����ɽ��
        if ($allowedDetail) {
            // �ܺ٥ܥ���
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngproductno"]);
            $imgDetail->setAttribute("class", "detail button");
            // td > img
            $tdDetail->appendChild($imgDetail);
        }
        // tr > td
        $trBody->appendChild($tdDetail);
    }

    // �������ܤ�ɽ��
    if ($existsModify) {
        // ��������
        $tdModify = $doc->createElement("td");
        $tdModify->setAttribute("class", $exclude);

        // �����ܥ����ɽ��
        if ($allowedModify) {
            // �����ܥ���
            $imgModify = $doc->createElement("img");
            $imgModify->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgModify->setAttribute("id", $record["lngproductno"]);
            $imgModify->setAttribute("class", "modify button");
            // td > img
            $tdModify->appendChild($imgModify);
        }
        // tr > td
        $trBody->appendChild($tdModify);
    }

    // ������ܤ�ɽ��
    if ($existsRecord) {
        // ���򥻥�
        $tdRecord = $doc->createElement("td");
        $tdRecord->setAttribute("class", $exclude);
        // ����ܥ���
        $imgRecord = $doc->createElement("img");
        $imgRecord->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
        $imgRecord->setAttribute("id", $record["lngproductno"]);
        $imgRecord->setAttribute("class", "record button");
        // td > img
        $tdRecord->appendChild($imgRecord);
        // tr > td
        $trBody->appendChild($tdRecord);
    }

    // ���ι��ܤ�ɽ��
    if ($existsResale) {
        // ���Υ���
        $tdResale = $doc->createElement("td");
        $tdResale->setAttribute("class", $exclude);

        // ���Υܥ����ɽ��
        if ($allowedResale) {
            // ���Υܥ���
            $imgResale = $doc->createElement("img");
            $imgResale->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgResale->setAttribute("id", $record["lngproductno"]);
            $imgResale->setAttribute("class", "resale button");
            // td > img
            $tdResale->appendChild($imgResale);
        }
        // tr > td
        $trBody->appendChild($tdResale);
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
                    $trBody->appendChild($td);
                    break;
                // [���ϼ�ɽ��������] ���ϼ�ɽ��̾
                case "lnginputusercode":
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $trBody->appendChild($td);
                    break;
                // ���ʥ�����
                case "strproductcode":
                    $td = $doc->createElement("td", $record["strproductcode"]);
                    $trBody->appendChild($td);
                    break;
                // ���ʥޥ���.���ʥ�����(���ܸ�)
                case "strproductname":
                    $td = $doc->createElement("td", toUTF8($record["strproductname"]));
                    $trBody->appendChild($td);
                    break;
                // ���ʥޥ���.����̾��(�Ѹ�)
                case "strproductenglishname":
                    $td = $doc->createElement("td", toUTF8($record["strproductenglishname"]));
                    $trBody->appendChild($td);
                    break;
                // [�Ķ�����ɽ��������] �Ķ�����ɽ��̾
                case "lnginchargegroupcode":
                    $textContent = "[" . $record["strinchargegroupdisplaycode"] . "]" . " " . $record["strinchargegroupdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $trBody->appendChild($td);
                    break;
                // [ô����ɽ��������] ô����ɽ��̾
                case "lnginchargeusercode":
                    $textContent = "[" . $record["strinchargeuserdisplaycode"] . "]" . " " . $record["strinchargeuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $trBody->appendChild($td);
                    break;
                // [��ȯô����ɽ��������] ��ȯô����ɽ��̾
                case "lngdevelopusercode":
                    $textContent = "[" . $record["strdevelopUserdisplaycode"] . "]" . " " . $record["strdevelopUserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
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

function toUTF8($str)
{
    return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"), ENT_QUOTES, 'utf-8');
}

