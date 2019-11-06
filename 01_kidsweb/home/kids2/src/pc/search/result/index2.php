<?php
// ----------------------------------------------------------------------------
/**
 *       �������� ����������٥��
 *
 *       ��������
 *         �����������ɡ���ӥ�����ֹ�ˤ��������������������
 *
 *       ��������
 *
 */

 // �ɤ߹���
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "pc/cmn/lib_pcs.php";

//�ͤμ���
$postdata = file_get_contents("php://input");
$aryData = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//JSON���饹���󥹥��󥹲�
$s = new Services_JSON();
//�ͤ�¸�ߤ��ʤ������̾�� POST �Ǽ�����
if ($aryData == null) {
    $aryData = $_POST;
}

$displayColumns = array();
// ɽ�����ܤ����
foreach ($aryData["displayColumns"] as $key) {
    $displayColumns[$key] = $key;
}

// ���������ɤˤ������������SQL
$strQuery = fncGetStocksByStrStockCodeSQL($aryData["strStockCode"], $aryData["lngRevisionNo"]);
echo $strQuery;
// �ͤ�Ȥ� =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

// ���������Ǥ�����̾����
for ($i = 0; $i < $lngResultNum; $i++) {
    $records = pg_fetch_all($lngResultID);
}

$objDB->freeResult($lngResultID);

// ������̥ơ��֥������ΰ�DOMDocument�����
$doc = new DOMDocument();
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

$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "��Ͽ��";
$aryTableHeaderName["dtmappropriationdate"] = "������";
$aryTableHeaderName["strstockcode"] = "�����Σ�.";
$aryTableHeaderName["lngrevisionno"] = "��ӥ�����ֹ�";
$aryTableHeaderName["strordercode"] = "ȯ���Σ�.";
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

// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
$index = 0;
// ������̷��ʬ����
foreach ($records as $i => $record) {

    unset($aryQuery);
    
    // �ܺ٥ǡ������������
    $detailData = fncGetDetailData($record["lngstockno"], $record["lngrevisionno"], $objDB);
    $rowspan = count($detailData);

    // �طʿ�����
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
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

    if ($rowspan == 0) {
        $rowspan = 1;
        $detailnos = "";
    }
    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strstockcode"] . "_" . $record["lngrevisionno"]);
    $trBody->setAttribute("detailnos", $detailnos);

    // ����
    $index = $index + 1;
    $tdIndex = $doc->createElement("td", $aryData["rownum"]. "." . $index);
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
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // [������ɽ��������] ���ϼ�ɽ��̾
                case "lngcustomercode":
                    $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "lngstockstatuscode":
                    $td = $doc->createElement("td", $record["strstockstatusname"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ��ʧ���
                case "lngpayconditioncode":
                    $td = $doc->createElement("td", $record["strpayconditionname"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ����������
                case "dtmexpirationdate":
                    $td = $doc->createElement("td", $record["dtmexpirationdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "strnote":
                    $td = $doc->createElement("td", $record["strnote"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // ��׶��
                case "curtotalprice":
                    $td = $doc->createElement("td", $record["curtotalprice"]);
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
    // $strHtml .= $doc->saveXML($trBody);


    // ������ܤ�ɽ��
    if ($existsDelete) {
        // �������
        $tdDelete = $doc->createElement("td");
        $tdDelete->setAttribute("class", $exclude);
        $tdDelete->setAttribute("style", $bgcolor . "text-align: center;");
        $tdDelete->setAttribute("rowspan", $rowspan);
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
        // tr > td
        $trBody->appendChild($tdInvalid);
    }

    // tbody > tr
    $strHtml .= $doc->saveXML($trBody);

    
    // ���ٹԤ�tr���ɲ�
    for ($i = 1; $i < $rowspan; $i++) {
        $trBody = $doc->createElement("tr");

        $trBody->setAttribute("id", $record["strstockcode"] . "_" . $record["lngrevisionno"] . "_" . $detailData[$i]["lngstockdetailno"]);

        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[$i]);

        $strHtml .= $doc->saveXML($trBody);

    }
}

// HTML����
echo $strHtml;
