<?php
// ----------------------------------------------------------------------------
/**
 *       �Ƹ��� ����������٥��
 *
 *       ��������
 *         �������ɡ���ӥ�����ֹ�ˤ�����������������
 *
 *       ��������
 *
 */

// �ɤ߹���
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "search/cmn/lib_search.php";
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

// �ѥ�᡼������
$type = $aryData["type"];
$strCode = $aryData["strCode"];
$lngRevisionNo = $aryData["lngRevisionNo"];
$lngDetailNo = $aryData["lngDetailNo"];
$displayColumns = array();
// ɽ�����ܤ����
foreach ($aryData["displayColumns"] as $key) {
    $displayColumns[$key] = $key;
}
// ����ʸ�����ʸ�����Ѵ�
$displayColumns = array_change_key_case($displayColumns, CASE_LOWER);
// ���å�����ǧ
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);
// �����ɡ�����ǡ����ˤ���������SQL
$records = fncGetHistoryDataByPKSQL($type, $strCode, $lngRevisionNo, $lngDetailNo, $objDB);

// ������̥ơ��֥������ΰ�DOMDocument�����
$doc = new DOMDocument();

// -------------------------------------------------------
// �Ƽ�ܥ��󸢸¥����å�
// -------------------------------------------------------
$aryAuthority = fncGetAryAuthority('so', $objAuth);

// �إå���������
if ($type == 'purchaseorder') { // ȯ���
    $aryTableHeaderName = $aryTableHeaderName_PURORDER;
} else if ($type == 'po') { // ȯ��
    $aryTableHeaderName = $aryTableHeaderName_PO;
} else if ($type == 'so') { // ����
    $aryTableHeaderName = $aryTableHeaderName_SO;
} else if ($type == 'sc') { // ���    
    $aryTableHeaderName = $aryTableHeaderName_SC;
    $aryTableDetailHeaderName = $aryTableDetailHeaderName_SC;
} else if ($type == 'slip') { //Ǽ�ʽ�
    $aryTableHeadBtnName = $aryTableHeadBtnName_SLIP;
    $aryTableHeaderName = $aryTableHeaderName_SLIP;
    $aryTableDetailHeaderName = $aryTableDetailHeaderName_SLIP;
    $aryTableBackBtnName = $aryTableBackBtnName_SLIP;
    $displayColumns = null;
} else if ($type == 'pc') { // ����   
    $aryTableHeaderName = $aryTableHeaderName_PC;
    $aryTableDetailHeaderName = $aryTableDetailHeaderName_PC;
} else if ($type == 'inv') {
    $aryTableHeadBtnName = $aryTableHeadBtnName_INV;
    $aryTableHeaderName = $aryTableHeaderName_INV;
    $aryTableDetailHeaderName = $aryTableDetailHeaderName_INV;
    $aryTableBackBtnName = $aryTableBackBtnName_INV;
    $displayColumns = null;
} else if ($type == 'estimate') {
}
// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
$index = 0;
// ������̷��ʬ����
foreach ($records as $i => $record) {
    if ($type == 'slip') {
        $strcode = $record["lngpkno"];
    } else {
        $strcode = $record["strcode"];
    }
    $lngrevisionno = $record["lngrevisionno"];
    $lngpkno = $record["lngpkno"];
    // �طʿ�����
    $bgcolor = fncSetBgColor($type, $strcode, false, $objDB);

    $detailData = array();
    $rowspan == 0;

    // ����񡦻�������塦Ǽ�ʽ�ξ��ܺ٥ǡ������������
    if ($type == 'inv' || $type == 'pc' || $type == 'sc' || $type == 'slip') {
        $detailData = fncGetDetailData($type, $lngpkno, $lngrevisionno, $objDB);
        $rowspan = count($detailData);
    }

    if ($rowspan == 0) {
        $rowspan = 1;
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    if ($type == 'so' || $type == 'po') {
        $trBody->setAttribute("id", $strcode . "_" . $record["lngdetailno"] . "_" . $lngrevisionno);
    } else {
        $trBody->setAttribute("id", $strcode . "_" . $lngrevisionno);
    }
    $trBody->setAttribute("class", 'detail');

    $aryTableHeaderName;
    // ����
    $index = $index + 1;
    $subindex = $aryData["rownum"] . "." . $index;

    fncSetHeadBtnToTr($doc, $trBody, $bgcolor, $aryTableHeadBtnName, $displayColumns, $record, $rowspan, $aryAuthority, false, false, $subindex, 'sc', null);

    // �إå����ǡ���������
    fncSetHeadDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName, $displayColumns, $record, $rowspan, false);

    // ���٥ǡ���������
    if (count($detailData) > 0) {
        $detailData[0]["lngmonetaryunitcode"] = $record["lngmonetaryunitcode"];
        $detailData[0]["strmonetaryunitsign"] = $record["strmonetaryunitsign"];
        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[0], false);

    }

    fncSetBackBtnToTr($doc, $trBody, $bgcolor, $aryTableBackBtnName, $displayColumns, $record, $rowspan, $aryAuthority, false, false, $type);

    // tbody > tr
    $strHtml .= $doc->saveXML($trBody);

    if (count($detailData) > 0) {
        // ���ٹԤ�tr���ɲ�
        for ($i = 1; $i < $rowspan; $i++) {
            $trBody = $doc->createElement("tr");
            $trBody->setAttribute("id", $strcode . "_" . $lngrevisionno . "_" . $detailData[$i]["lngrecodeno"]);
            $trBody->setAttribute("class", "tablesorter-childRow");
            $detailData[$i]["lngmonetaryunitcode"] = $record["lngmonetaryunitcode"];
            $detailData[$i]["strmonetaryunitsign"] = $record["strmonetaryunitsign"];
            fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[$i], false);
            $strHtml .= $doc->saveXML($trBody);
        }
    }
}

// HTML����
echo $strHtml;