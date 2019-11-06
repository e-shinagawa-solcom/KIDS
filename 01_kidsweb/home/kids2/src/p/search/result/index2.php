<?php
// ----------------------------------------------------------------------------
/**
 *       ���ʸ��� ����������٥��
 *
 *       ��������
 *         �����ʥ����ɡ���ӥ�����ֹ�ˤ�꾦�����������������
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------
// �ɤ߹���
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "p/cmn/lib_p.php";

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

// �������ܤ�����פ���ǿ��λ����ǡ������������SQLʸ�κ����ؿ�
$strQuery = fncGetProductsByStrProductCodeSQL($aryData["strProductCode"], $aryData["lngRevisionNo"]);

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
// ���򥫥���ɽ��
$existsHistory = array_key_exists("btnhistory", $displayColumns);
// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_P4, $objAuth);

// �ܺ�ɽ��������ǡ�����ɽ��
$allowedDetailDelete = fncCheckAuthority(DEF_FUNCTION_P5, $objAuth);

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
// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
$index = 0;
// ������̷��ʬ����
foreach ($records as $i => $record) {
    // �طʿ�����
    if ($record["strgroupdisplaycolor"]) {
        $bgcolor = "background-color: " . $record["strgroupdisplaycolor"] . ";";
    } else {
        $bgcolor = "background-color: #FFFFFF;";
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strproductcode"]. "_" . $record["lngrevisionno"] );
    
    // ����
    $index +=1;
    $tdIndex = $doc->createElement("td", $aryData["rownum"]. "." . $index);
    $tdIndex->setAttribute("style", $bgcolor);
    $trBody->appendChild($tdIndex);

    // �ܺ٤�ɽ��
    if ($existsDetail) {
        // �ܺ٥���
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);
        $tdDetail->setAttribute("style", $bgcolor . "text-align: center;");

        // �ܺ٥ܥ����ɽ��
        if (($allowedDetailDelete) or ($allowedDetail and $record["lngrevisionno"] >= 0)) {
            // �ܺ٥ܥ���
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngproductno"]);
            $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDetail->setAttribute("class", "detail button");
            $imgDetail->setAttribute("onclick", "alert('test');");
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
                    $td = $doc->createElement("td", $record["strgoodsplanprogressname"]);
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
                    $td = $doc->createElement("td", $record["strproductname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����̾�ʱѸ��
                case "strproductenglishname":
                    $td = $doc->createElement("td", $record["strproductenglishname"]);
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
                    $td = $doc->createElement("td", $textContent);
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
                    $td = $doc->createElement("td", $textContent);
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
                    $td = $doc->createElement("td", $textContent);
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
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ƥ���
                case "lngcategorycode":
                    $td = $doc->createElement("td", $record["strcategoryname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ܵ�����
                case "strgoodscode":
                    $td = $doc->createElement("td", $record["strgoodscode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����̾��
                case "strgoodsname":
                    $td = $doc->createElement("td", $record["strgoodsname"]);
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
                    $td = $doc->createElement("td", $textContent);
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
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ٻ�ñ��
                case "lngpackingunitcode":
                    $td = $doc->createElement("td", $record["strpackingunitname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����ñ��
                case "lngproductunitcode":
                    $td = $doc->createElement("td", $record["strproductunitname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʷ���
                case "lngproductformcode":
                    $td = $doc->createElement("td", $record["strproductformname"]);
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
                    $td = $doc->createElement("td", $textContent);
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
                    $td = $doc->createElement("td", $textContent);
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
                    $td = $doc->createElement("td", $textContent);
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
                    $td = $doc->createElement("td", $record["strtargetagename"]);
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
                    $td = $doc->createElement("td", $record["strcertificateclassname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ǹ���
                case "lngcopyrightcode":
                    $td = $doc->createElement("td", $record["strcopyrightname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ǹ�������
                case "strcopyrightnote":
                    $td = $doc->createElement("td", $record["strcopyrightnote"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ǹ�ɽ���ʹ����
                case "strcopyrightdisplaystamp":
                    $td = $doc->createElement("td", $record["strcopyrightdisplaystamp"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ǹ�ɽ���ʰ���ʪ��
                case "strcopyrightdisplayprint":
                    $td = $doc->createElement("td", $record["strcopyrightdisplayprint"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʹ���
                case "strproductcomposition":
                    $td = $doc->createElement("td", "��" . $record["strproductcomposition"] . "�異�å���֥�");
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���å���֥�����
                case "strassemblycontents":
                    $td = $doc->createElement("td", $record["strassemblycontents"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���;ܺ�
                case "strspecificationdetails":
                    $td = $doc->createElement("td", $record["strspecificationdetails"]);
                    $td->setAttribute("style", $bgcolor . "white-space: pre; ");
                    // $td->setAttribute("style", "white-space: pre; ");
                    $trBody->appendChild($td);
                    break;

            }
        }
    }

    $strHtml .= $doc->saveXML($trBody);

}

// // HTML����
echo $strHtml;
