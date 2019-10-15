<?php

// ----------------------------------------------------------------------------
/**
 *       ��������  ����
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
require SRC_ROOT . "pc/cmn/lib_pcs.php";

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
// 702 ���������ʻ���������
if (!fncCheckAuthority(DEF_FUNCTION_PC2, $objAuth)) {
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
$subStrQuery = fncGetMaxStockSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns);

$strQuery = fncGetStocksByStrStockCodeSQL($subStrQuery);

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
    $lngErrorCode = 703;
    $aryErrorMessage = "";
}

if ($errorFlag) {
    // ���顼���̤������
    $strReturnPath = "../pc/search/index.php?strSessionID=" . $aryData["strSessionID"];

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
$objTemplate->getTemplate("/pc/search/pc_search_result.html");

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
$existsFix = array_key_exists("btnfix", $displayColumns);
// ���򥫥���ɽ��
$existsHistory = array_key_exists("btnhistory", $displayColumns);
// �����ɽ��
$existsDelete = array_key_exists("btndelete", $displayColumns);
// ̵��������ɽ��
$existsInvalid = array_key_exists("btninvalid", $displayColumns);

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

// �ܺ٤�ɽ��
if ($existsDetail) {
    // �ܺ٥����
    $thDetail = $doc->createElement("th", toUTF8("�ܺ�"));
    $thDetail->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thDetail);
}
// ������ɽ��
if ($existsFix) {
    // ���ꥫ���
    $thFix = $doc->createElement("th", toUTF8("����"));
    $thFix->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thFix);
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
$aryTableHeaderName["strstockcode"] = "�����Σ�.";
$aryTableHeaderName["lngrevisionno"] = "��ӥ�����ֹ�";
$aryTableHeaderName["strordercode"] = "ȯ��Σ�.";
$aryTableHeaderName["strslipcode"] = "Ǽ�ʽ�Σ�.";
$aryTableHeaderName["lnginputusercode"] = "���ϼ�";
$aryTableHeaderName["lngcustomercode"] = "������";
$aryTableHeaderName["lngstockstatuscode"] = "����";
$aryTableHeaderName["lngpayconditioncode"] = "��ʧ���";
$aryTableHeaderName["dtmexpirationdate"] = "����������";
$aryTableHeaderName["strnote"] = "����";
$aryTableHeaderName["curtotalprice"] = "��׶��";
$aryTableDetailHeaderName["lngrecordno"] = "���ٹ��ֹ�";
$aryTableDetailHeaderName["strproductcode"] = "���ʥ�����";
$aryTableDetailHeaderName["lnginchargegroupcode"] = "�Ķ�����";
$aryTableDetailHeaderName["lnginchargeusercode"] = "��ȯô����";
$aryTableDetailHeaderName["strproductname"] = "����̾";
$aryTableDetailHeaderName["lngstocksubjectcode"] = "��������";
$aryTableDetailHeaderName["lngstockitemcode"] = "��������";
$aryTableDetailHeaderName["strmoldno"] = "�Σ";
$aryTableDetailHeaderName["strgoodscode"] = "�ܵ�����";
$aryTableDetailHeaderName["lngdeliverymethodcode"] = "������ˡ";
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
// ���٥إå������������
foreach ($aryTableDetailHeaderName as $key => $value) {
    if (array_key_exists($key, $displayColumns)) {
        $th = $doc->createElement("th", toUTF8($value));
        $trHead->appendChild($th);
    }
}
// ������ܤ�ɽ��
if ($existsDelete) {
    // ��������
    $thDelete = $doc->createElement("th", toUTF8("���"));
    $thDelete->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thDelete);
}

// ̵�����ܤ�ɽ��
if ($existsInvalid) {
    // ̵�������
    $thInvalid = $doc->createElement("th", toUTF8("̵��"));
    $thInvalid->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thInvalid);
}

// thead > tr
$thead->appendChild($trHead);

// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
// ������̷��ʬ����
foreach ($records as $i => $record) {
    unset($aryQuery);
    // ����ե饰
    $deletedFlag = false;
    // ��Х���̵ͭ�ե饰
    $revisedFlag = false;
    // �ǿ��������ɤ����Υե饰
    $isMaxStock = false;
    // ����̵ͭ�ե饰
    $historyFlag = false;
    // ��ӥ�����ֹ�
    $revisionNos = "";

    // Ʊ������NO�κǿ������ǡ����Υ�ӥ�����ֹ���������
    $aryQuery[] = "SELECT";
    $aryQuery[] = " lngstockno, lngrevisionno ";
    $aryQuery[] = "FROM m_stock";
    $aryQuery[] = "WHERE strstockcode='" . $record["strstockcode"] . "' ";
    $aryQuery[] = "and lngrevisionno >= 0";
    $aryQuery[] = "and bytInvalidFlag = FALSE ";
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
            if ($j == 0) {
                $maxStockInfo = $objDB->fetchArray($lngResultID, $j);
                // �������ʤΥ�ӥ�����ֹ�<0�ξ�硢����ѤȤʤ�
                if ($maxStockInfo["lngrevisionno"] < 0) {
                    $deletedFlag = true;
                }

                if ($maxStockInfo["lngrevisionno"] != 0) {
                    $revisedFlag = true;
                }
                if ($maxStockInfo["lngrevisionno"] == $record["lngrevisionno"]) {
                    $isMaxStock = true;
                }
            } else {
                $stockInfo = $objDB->fetchArray($lngResultID, $j);
                if ($revisionNos == "") {
                    $revisionNos = $stockInfo["lngrevisionno"];
                } else {
                    $revisionNos = $revisionNos . "," . $stockInfo["lngrevisionno"];
                }
            }
            
        }
    }

    $objDB->freeResult($lngResultID);

    // �ܺ٥ǡ������������
    $detailData = fncGetDetailData($record["lngstockno"], $record["lngrevisionno"], $objDB);
    $rowspan = count($detailData);

    // �طʿ�����
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else if ($isMaxStock) {
        $bgcolor = "background-color: #FFB2B2;";
    } else {
        $bgcolor = "background-color: #FEEF8B;";
    }
    // �����ֹ����
    for ($i = $rowspan; $i > 0; $i--) {
        if ($detailnos == "") {
            $detailnos = $detailData[$i]["lngstockdetailno"];
        } else {
            $detailnos = $detailnos . "," . $detailData[$i]["lngstockdetailno"];
        }
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    if (!$isMaxStock) {
        $trBody->setAttribute("id", $record["strstockcode"] . "_" . $record["lngrevisionno"]);
        $trBody->setAttribute("style", "display: none;");
    } else {
        $trBody->setAttribute("id", $record["strstockcode"]);
    }
    $trBody->setAttribute("detailnos", $detailnos);

    // ����
    if ($isMaxStock) {
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
        $tdDetail->setAttribute("style", $bgcolor . "text-align: center;");
        $tdDetail->setAttribute("rowspan", $rowspan);

        // �ܺ٥ܥ����ɽ��
        if ($allowedDetail && $record["lngrevisionno"] >= 0) {
            // �ܺ٥ܥ���
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/pc/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngstockno"]);
            $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDetail->setAttribute("class", "detail button");
            // td > img
            $tdDetail->appendChild($imgDetail);
        }
        // tr > td
        $trBody->appendChild($tdDetail);
    }

    // �������ܤ�ɽ��
    if ($existsFix) {
        // ��������
        $tdFix = $doc->createElement("td");
        $tdFix->setAttribute("class", $exclude);
        $tdFix->setAttribute("style", $bgcolor . "text-align: center;");
        $tdFix->setAttribute("rowspan", $rowspan);

        // �����ܥ����ɽ��
        if ($allowedFix && $isMaxStock && $record["lngrevisionno"] >= 0 && $record["lngstockstatuscode"] != DEF_STOCK_CLOSED && !$deletedFlag) {
            // �����ܥ���
            $imgFix = $doc->createElement("img");
            $imgFix->setAttribute("src", "/img/type01/pc/renew_off_bt.gif");
            $imgFix->setAttribute("id", $record["lngstockno"]);
            $imgFix->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgFix->setAttribute("class", "fix button");
            // td > img
            $tdFix->appendChild($imgFix);
        }
        // tr > td
        $trBody->appendChild($tdFix);
    }

    // ������ܤ�ɽ��
    if ($existsHistory) {
        // ���򥻥�
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");
        $tdHistory->setAttribute("rowspan", $rowspan);

        if ($isMaxStock and $historyFlag and array_key_exists("admin", $optionColumns)) {
            // ����ܥ���
            $imgHistory = $doc->createElement("img");
            $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgHistory->setAttribute("id", $record["strstockcode"]);
            $imgHistory->setAttribute("revisionnos", $revisionNos);
            $imgHistory->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgHistory->setAttribute("maxdetailno", $detailData[$rowspan - 1]["lngstockdetailno"]);
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
                // �����Σ�.
                case "strstockcode":
                    $td = $doc->createElement("td", $record["strstockcode"]);
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
                // ȯ��Σ�.
                case "strordercode":
                    $td = $doc->createElement("td", $record["strordercode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // Ǽ�ʽ�Σ�.
                case "strslipcode":
                    $td = $doc->createElement("td", $record["strslipcode"]);
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
                // [������ɽ��������] ���ϼ�ɽ��̾
                case "lngcustomercode":
                    $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "lngstockstatuscode":
                    $td = $doc->createElement("td", toUTF8($record["strstockstatusname"]));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ��ʧ���
                case "lngpayconditioncode":
                    $td = $doc->createElement("td", toUTF8($record["strpayconditionname"]));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ����������
                case "dtmexpirationdate":
                    $td = $doc->createElement("td", toUTF8($record["dtmexpirationdate"]));
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
                    $td = $doc->createElement("td", toUTF8($record["curtotalprice"]));
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


    // ������ܤ�ɽ��
    if ($existsDelete) {
        // �������
        $tdDelete = $doc->createElement("td");
        $tdDelete->setAttribute("class", $exclude);
        $tdDelete->setAttribute("style", $bgcolor . "text-align: center;");
        $tdDelete->setAttribute("rowspan", $rowspan);

        $showDeleteFlag = false;
        if ($allowedDelete) {
            if (!$revisedFlag) {
                if ($record["lngstockstatuscode"] != DEF_STOCK_CLOSED && !$deletedFlag) {
                    $showDeleteFlag = true;
                }
            } else {
                if ($isMaxStock) {
                    if ($record["lngstockstatuscode"] != DEF_STOCK_CLOSED && !$deletedFlag) {
                        $showDeleteFlag = true;
                    }
                }
            }
        }

        // ����ܥ����ɽ��
        if ($showDeleteFlag && $isMaxStock) {
            // ����ܥ���
            $imgDelete = $doc->createElement("img");
            $imgDelete->setAttribute("src", "/img/type01/pc/delete_off_bt.gif");
            $imgDelete->setAttribute("id", $record["lngstockno"]);
            $imgDelete->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDelete->setAttribute("class", "delete button");
            // td > img
            $tdDelete->appendChild($imgDelete);
        }
        // tr > td
        $trBody->appendChild($tdDelete);
    }

    // ̵�����ܤ�ɽ��
    if ($existsInvalid) {
        // ̵������
        $tdInvalid = $doc->createElement("td");
        $tdInvalid->setAttribute("class", $exclude);
        $tdInvalid->setAttribute("style", $bgcolor . "text-align: center;");
        $tdInvalid->setAttribute("rowspan", $rowspan);

        // ̵���ܥ����ɽ��
        if ($allowedInvalid && $isMaxStock && $record["lngstockstatuscode"] != DEF_STOCK_CLOSED) {
            // ̵���ܥ���
            $imgInvalid = $doc->createElement("img");
            $imgInvalid->setAttribute("src", "/img/type01/pc/invalid_off_bt.gif");
            $imgInvalid->setAttribute("id", $record["lngstockno"]);
            $imgInvalid->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgInvalid->setAttribute("class", "invalid button");
            // td > img
            $tdInvalid->appendChild($imgInvalid);
        }
        // tr > td
        $trBody->appendChild($tdInvalid);
    }

    // tbody > tr
    $tbody->appendChild($trBody);

    
    // ���ٹԤ�tr���ɲ�
    for ($i = 1; $i < $rowspan; $i++) {
        $trBody = $doc->createElement("tr");
        if (!$isMaxStock) {
            $trBody->setAttribute("style", "display: none;");
        }
        $trBody->setAttribute("id", $record["strstockcode"] . "_" . $record["lngrevisionno"] . "_" . $detailData[$i]["lngstockdetailno"]);

        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[$i]);

        $tbody->appendChild($trBody);

    }
}

// HTML����
echo $doc->saveHTML();
