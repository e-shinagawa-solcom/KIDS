<?php
// ----------------------------------------------------------------------------
/**
 *       ������ ����������٥��
 *
 *       ��������
 *         ���������ɡ���ӥ�����ֹ�ˤ��������������������
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------
// �ɤ߹���
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "so/cmn/lib_sos.php";

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

// ���å�����ǧ
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

// ���������������SQL
$strQuery = fncGetReceivesByStrReceiveCodeSQL($aryData["strReceiveCode"], $aryData["lngReceiveDetailNo"], $aryData["lngRevisionNo"]);

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
// ���ꥫ����ɽ��
$existsDecide = array_key_exists("btndecide", $displayColumns);
// ���򥫥���ɽ��
$existsHistory = array_key_exists("btnhistory", $displayColumns);
// �����å�����ɽ��
$existsCancel = array_key_exists("btncancel", $displayColumns);

// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_SO3, $objAuth);

$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "��Ͽ��";
$aryTableHeaderName["lnginputusercode"] = "���ϼ�";
$aryTableHeaderName["strcustomerreceivecode"] = "�ܵҼ����ֹ�";
$aryTableHeaderName["strreceivecode"] = "����Σ�.";
$aryTableHeaderName["lngrevisionno"] = "��ӥ�����ֹ�";
$aryTableHeaderName["strproductcode"] = "���ʥ�����";
$aryTableHeaderName["strproductname"] = "����̾";
$aryTableHeaderName["strproductenglishname"] = "����̾�ʱѸ��";
$aryTableHeaderName["lnginchargegroupcode"] = "�Ķ�����";
$aryTableHeaderName["lnginchargeusercode"] = "��ȯô����";
$aryTableHeaderName["lngsalesclasscode"] = "����ʬ";
$aryTableHeaderName["strgoodscode"] = "�ܵ�����";
$aryTableHeaderName["lngcustomercompanycode"] = "�ܵ�";
$aryTableHeaderName["dtmdeliverydate"] = "Ǽ��";
$aryTableHeaderName["lngreceivestatuscode"] = "����";
// $aryTableHeaderName["strnote"] = "����";
$aryTableHeaderName["lngrecordno"] = "���ٹ��ֹ�";
$aryTableHeaderName["curproductprice"] = "ñ��";
$aryTableHeaderName["lngproductunitcode"] = "ñ��";
$aryTableHeaderName["lngproductquantity"] = "����";
$aryTableHeaderName["cursubtotalprice"] = "��ȴ���";
$aryTableHeaderName["strdetailnote"] = "��������";
// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
$index = 0;
// ������̷��ʬ����
foreach ($records as $i => $record) {
    
    // �طʿ�����
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else {
        $bgcolor = "background-color: #FEEF8B;";
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strreceivecode"] . "_" . $record["lngreceivedetailno"] . "_" . $record["lngrevisionno"]);

    // ����
    $index +=1;
    $tdIndex = $doc->createElement("td", $aryData["rownum"]. "." . $index);
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
            $imgDetail->setAttribute("id", $record["lngreceiveno"]);
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
        // tr > td
        $trBody->appendChild($tdDecide);
    }

    // ������ܤ�ɽ��
    if ($existsHistory) {
        // ���򥻥�
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");
        // tr > td
        $trBody->appendChild($tdHistory);
    }

    
    // �إå������ǡ�������
    fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName, $displayColumns, $record, false);

    // �����ù��ܤ�ɽ��
    if ($existsCancel) {
        // �����å���
        $tdCancel = $doc->createElement("td");
        $tdCancel->setAttribute("class", $exclude);
        $tdCancel->setAttribute("style", $bgcolor . "text-align: center;");
        // tr > td
        $trBody->appendChild($tdCancel);
    }


    $strHtml .= $doc->saveXML($trBody);

}

// // HTML����
echo $strHtml;
