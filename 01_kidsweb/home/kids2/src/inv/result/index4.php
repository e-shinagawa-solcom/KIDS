<?php
// ----------------------------------------------------------------------------
/**
 *       ����񸡺� ����������٥��
 *
 *       ��������
 *         ������񥳡��ɡ���ӥ�����ֹ�ˤ���������������������
 *
 *       ��������
 *
 */

 // �ɤ߹���
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "inv/cmn/lib_regist.php";

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

// ���å�����ǧ
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

// ���ᥳ���ɤˤ������������SQL
$strQuery = fncGetInvoicesByStrInvoiceCodeSQL($aryData["strInvoiceCode"], $aryData["lngRevisionNo"]);
// �ͤ�Ȥ� =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

// ���������Ǥ�����̾����
for ($i = 0; $i < $lngResultNum; $i++) {
    $records = pg_fetch_all($lngResultID);
}

$objDB->freeResult($lngResultID);

// ������̥ơ��֥������ΰ�DOMDocument�����
$doc = new DOMDocument();
// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_PC4, $objAuth);
// ������ɽ��
$allowedFix = fncCheckAuthority(DEF_FUNCTION_PC5, $objAuth);
// �����ɽ��
$allowedDelete = fncCheckAuthority(DEF_FUNCTION_PC6, $objAuth);

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

// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
// ������̷��ʬ����
foreach ($records as $i => $record) {

    // �ܺ٥ǡ������������
    $detailData = fncGetDetailData($record["lnginvoiceno"], $record["lngrevisionno"], $objDB);

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
            $detailnos = $detailData[$i]["lnginvoicedetailno"];
        } else {
            $detailnos = $detailnos . "," . $detailData[$i]["lnginvoicedetailno"];
        }
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strinvoicecode"] . "_" . $record["lngrevisionno"]);
    $trBody->setAttribute("detailnos", $detailnos);

    // ����
    $index = $index + 1;
    $tdIndex = $doc->createElement("td", $aryData["rownum"]. "." . $index);
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
    // tr > td
    $trBody->appendChild($tdFix);

    // ���򥻥�
    $tdHistory = $doc->createElement("td");
    $tdHistory->setAttribute("class", $exclude);
    $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");
    $tdHistory->setAttribute("rowspan", $rowspan);
    // tr > td
    $trBody->appendChild($tdHistory);

    // �إå������ǡ���������
    fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $rowspan, $aryTableHeaderName, $record, false);

    // ���٥ǡ���������
    fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $detailData[0], $record, false);

    // �������
    $tdDelete = $doc->createElement("td");
    $tdDelete->setAttribute("class", $exclude);
    $tdDelete->setAttribute("style", $bgcolor . "text-align: center;");
    $tdDelete->setAttribute("rowspan", $rowspan);
    // tr > td
    $trBody->appendChild($tdDelete);

    // tbody > tr
    $strHtml .= $doc->saveXML($trBody);

    // ���ٹԤ�tr���ɲ�
    for ($i = 1; $i < $rowspan; $i++) {
        $trBody = $doc->createElement("tr");

        $trBody->setAttribute("id", $record["strinvoicecode"] . "_" . $record["lngrevisionno"] . "_" . $detailData[$i]["lnginvoicedetailno"]);

        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $detailData[$i], $record, false);
        
		// tbody > tr
        $strHtml .= $doc->saveXML($trBody);
        

    }
}
// HTML����
echo $strHtml;