<?php
// ----------------------------------------------------------------------------
/**
 *       ������� ������̤γ���ܥ���
 *
 *       ��������
 *         �������о����ٹ������������򤷤��Ԥ���ꤹ�����
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

// �ɤ߹���
include 'conf.inc';
require LIB_FILE;

//PHPɸ���JSON�Ѵ��᥽�åɤϥ��顼�ˤʤ�Τǳ����Υ饤�֥��(���餯���󥳡��ɤ�����)
include 'JSON.php';

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
$objAuth = fncIsSession($_GET["strSessionID"], $objAuth, $objDB);

$aryQuery = array();
$aryQuery[] = "SELECT ";
$aryQuery[] = "rd.lngReceiveNo as lngReceiveNo, rd.lngRevisionNo as lngRevisionNo";
$aryQuery[] = ", r.strReceiveCode";
$aryQuery[] = ", r.strcustomerreceivecode";
$aryQuery[] = ", r.strMonetaryUnitSign";
$aryQuery[] = ", rd.lngreceivedetailno as lngreceivedetailno";
$aryQuery[] = ", rd.strProductCode as strProductCode"; // ���ʥ����ɡ�̾��
$aryQuery[] = ", p.strProductName as strProductName";
$aryQuery[] = ", r.strCompanyDisplayCode as strCustomerDisplayCode"; // �ܵҥ����ɡ�̾��
$aryQuery[] = ", r.strCompanyDisplayName as strCustomerDisplayName";
$aryQuery[] = ", p.lngproductno as lngproductno";
$aryQuery[] = ", sd.lngsalesdivisioncode"; // ����ʬ
$aryQuery[] = ", sd.strsalesdivisionname";
$aryQuery[] = ", rd.lngSalesClassCode as lngSalesClassCode"; // ����ʬ
$aryQuery[] = ", ss.strSalesClassName as strSalesClassName";
// �����ֹ�
$aryQuery[] = " , p.lngProductNo";
$aryQuery[] = " , p.strrevisecode";
$aryQuery[] = " , p.lngRevisionNo as lngProductRevisionNo";
$aryQuery[] = ", p.strGoodsCode as strGoodsCode"; // �ܵ�����
// ����
$aryQuery[] = ", p.lnginchargegroupcode as lngInChargeGroupCode";
$aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
$aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
// ô����
$aryQuery[] = ", p.lnginchargeusercode as lngInChargeUserCode";
$aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
$aryQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName";
// ��ȯô����
$aryQuery[] = ", p.lngdevelopusercode as lngdevelopusercode";
$aryQuery[] = ", delp_u.strUserDisplayCode as strdevelopuserdisplaycode";
$aryQuery[] = ", delp_u.strUserDisplayName as strdevelopuserdisplayname";

$aryQuery[] = ", rd.dtmDeliveryDate as dtmDeliveryDate"; // Ǽ��
$aryQuery[] = ", To_char( rd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice"; // ñ��
$aryQuery[] = ", rd.lngProductUnitCode as lngProductUnitCode"; // ñ��
$aryQuery[] = ", pu.strProductUnitName as strProductUnitName";
$aryQuery[] = ", p.lngcartonquantity"; // �����ȥ�����
$aryQuery[] = ", To_char( rd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice"; // ��ȴ���
$aryQuery[] = ", rd.strNote as strDetailNote"; // ��������
$aryQuery[] = ", ed.lngproductquantity as lngproductquantity"; // ���ʿ���
$aryQuery[] = " FROM t_ReceiveDetail rd";
$aryQuery[] = "  INNER JOIN ( ";
$aryQuery[] = "    SELECT";
$aryQuery[] = "      r1.*";
$aryQuery[] = "      , m_MonetaryUnit.strMonetaryUnitSign";
$aryQuery[] = "      , cust_c.strCompanyDisplayCode";
$aryQuery[] = "      , cust_c.strshortname as strCompanyDisplayName ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_Receive r1 ";
$aryQuery[] = "      inner join ( ";
$aryQuery[] = "        select";
$aryQuery[] = "          max(lngrevisionno) lngrevisionno";
$aryQuery[] = "          , strReceiveCode ";
$aryQuery[] = "        from";
$aryQuery[] = "          m_Receive ";
$aryQuery[] = "        group by";
$aryQuery[] = "          strReceiveCode";
$aryQuery[] = "      ) r2 ";
$aryQuery[] = "        on r1.lngrevisionno = r2.lngrevisionno ";
$aryQuery[] = "        and r1.strReceiveCode = r2.strReceiveCode ";
$aryQuery[] = "      LEFT JOIN m_MonetaryUnit ";
$aryQuery[] = "        USING (lngMonetaryUnitCode) ";
$aryQuery[] = "      LEFT JOIN m_Company cust_c ";
$aryQuery[] = "        ON r1.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
$aryQuery[] = "    WHERE";
$aryQuery[] = "      r1.lngreceivestatuscode = " . DEF_RECEIVE_APPLICATE ." ";
if ($aryData["lngCustomerCode"] != "") {
    $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . $aryData["lngCustomerCode"] . "' ";
}
$aryQuery[] = "     and r1.lngcustomercompanycode != 0 ";
$aryQuery[] = " ) r USING (lngReceiveNo, lngRevisionNo)";    
$aryQuery[] = "        LEFT JOIN (";
$aryQuery[] = "            select p1.*  from m_product p1 ";
$aryQuery[] = "        	inner join (select max(lngrevisionno) lngrevisionno, strproductcode, strrevisecode from m_Product group by strProductCode, strrevisecode) p2";
$aryQuery[] = "            on p1.lngrevisionno = p2.lngrevisionno and p1.strproductcode = p2.strproductcode and p1.strrevisecode = p2.strrevisecode";
$aryQuery[] = "          ) p ";
$aryQuery[] = "          ON rd.strProductCode = p.strProductCode AND rd.strrevisecode = p.strrevisecode ";
$aryQuery[] = " LEFT JOIN m_Group inchg_g ON p.lnginchargegroupcode = inchg_g.lngGroupCode";
$aryQuery[] = " LEFT JOIN m_User inchg_u ON p.lnginchargeusercode = inchg_u.lngUserCode";
$aryQuery[] = " LEFT JOIN m_User delp_u ON p.lngdevelopusercode = delp_u.lngUserCode";
$aryQuery[] = " LEFT JOIN m_SalesClass ss on rd.lngSalesClassCode = ss.lngSalesClassCode";
$aryQuery[] = " LEFT JOIN m_salesclassdivisonlink ssdl on ssdl.lngSalesClassCode = ss.lngSalesClassCode";
$aryQuery[] = " LEFT JOIN m_salesdivision sd on sd.lngsalesdivisioncode = ssdl.lngsalesdivisioncode";
$aryQuery[] = " LEFT JOIN m_ProductUnit pu ON rd.lngProductUnitCode = pu.lngProductUnitCode";
$aryQuery[] = " LEFT JOIN t_estimatedetail ed on ed.lngestimateno = rd.lngestimateno";
$aryQuery[] = " and ed.lngrevisionno = rd.lngestimaterevisionno";
$aryQuery[] = " and ed.lngestimatedetailno = rd.lngestimatedetailno";
$aryQuery[] = " WHERE not exists (select strreceivecode from m_receive where lngrevisionno < 0  and strreceivecode = r.strreceivecode)";

if ($aryData["strProductCode"] != "") {
    if (strpos($aryData["strProductCode"], '_') !== false) {
        $aryQuery[] = " AND rd.strProductCode = '" . explode("_", $aryData["strProductCode"])[0] . "'";
        $aryQuery[] = " AND rd.strrevisecode = '" . explode("_", $aryData["strProductCode"])[1] . "'";
    } else {
        $aryQuery[] = " AND rd.strProductCode = '" . $aryData["strProductCode"] . "'";
    }
}
if ($aryData["lngSalesDivisionCode"] != "") {
    $aryQuery[] = " AND ed.lngSalesDivisionCode = '" . $aryData["lngSalesDivisionCode"] . "' ";
}
if ($aryData["lngSalesClassCode"] != "") {
    $aryQuery[] = " AND rd.lngSalesClassCode = '" . $aryData["lngSalesClassCode"] . "' ";
}
if ($aryData["From_dtmDeliveryDate"] != "") {
    $aryQuery[] = " AND rd.dtmDeliveryDate >= '" . $aryData["From_dtmDeliveryDate"] . "' ";
}
if ($aryData["To_dtmDeliveryDate"] != "") {
    $aryQuery[] = " AND rd.dtmDeliveryDate <= '" . $aryData["To_dtmDeliveryDate"] . "' ";
}
$aryQuery[] = " ORDER BY rd.lngSortKey ASC ";

$strQuery = implode("\n", $aryQuery);
// echo $strQuery;
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
$aryDetailResult = array();
if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryDetailResult[] = $objDB->fetchArray($lngResultID, $i);
    }
}
$objDB->freeResult($lngResultID);


$strQuery = "SELECT lngproductunitcode, strproductunitname FROM m_productunit";
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
// �������������ξ��
if ($lngResultNum) {
    // ���������Ǥ�����̾����
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryProductUnit[] = $objDB->fetchArray($lngResultID, $i);
    }
}

$objDB->freeResult($lngResultID);
$objDB->close();

$lngReceiveNos = explode(",", $aryData["lngReceiveNo"]);
if ($lngReceiveNos) {
    // ɽ�����ܤ����
    foreach ($lngReceiveNos as $key) {
        $lngReceiveNos[$key] = $key;
    }
}

// ������̥ơ��֥������ΰ�DOMDocument�����
$doc = new DOMDocument();
// ���پ���ν���
$detailNum = 0;
$decideNum = 0;
$tblA_chkbox_body_html = "";
$tblA_detail_body_html = "";
$tblB_no_html = "";
$tblB_body_html = "";
foreach ($aryDetailResult as $detailResult) {
    $isdecideObj = false;
    if (array_key_exists($detailResult["lngreceiveno"], $lngReceiveNos)) {
        $isdecideObj = true;
    }

    if (!$isdecideObj) {
        $detailNum += 1;
        // tbody > tr���Ǻ���
        $trChkBox = $doc->createElement("tr");
        // ��������å��ܥå���
        $chkBox = $doc->createElement("input");
        $chkBox->setAttribute("type", "checkbox");
        $id = $detailResult["lngreceiveno"] . "_" . $detailResult["lngreceivedetailno"] . "_" . $detailResult["lngrevisionno"];
        $chkBox->setAttribute("id", $id);
        $chkBox->setAttribute("style", "width: 10px;");
        $tdChkBox = $doc->createElement("td");
        $tdChkBox->setAttribute("style", "width: 20px;text-align:center;");
        $tdChkBox->appendChild($chkBox);
        $trChkBox->appendChild($tdChkBox);
        // tbody > tr
        $tblA_chkbox_body_html .= $doc->saveXML($trChkBox);

    } else {
        $decideNum += 1;
        // tbody > tr���Ǻ���
        $trBody = $doc->createElement("tr");
        // No.
        $td = $doc->createElement("td", $decideNum);
        $td->setAttribute("style", "width: 25px;");
        $trBody->appendChild($td);
        // tbody > tr
        $tblB_no_html .= $doc->saveXML($trBody);
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");

    // if (!$isdecideObj) {
        // No.
        $td = $doc->createElement("td", $detailNum);
        $td->setAttribute("style", "width: 25px;");
        if ($isdecideObj) {
            $td->setAttribute("style", "display:none");
        }
        $trBody->appendChild($td);
    // }

    // ���ٹ��ֹ�
    $td = $doc->createElement("td", $detailResult["lngreceivedetailno"]);
    $td->setAttribute("id", "lngreceivedetailno");
    $trBody->appendChild($td);

    // �ܵҼ����ֹ�
    $td = $doc->createElement("td");
    $td->setAttribute("id", "strcustomerreceivecode");
    $text = $doc->createElement("input");
    $text->setAttribute("type", "text");
    $text->setAttribute("class", "form-control form-control-sm txt-kids");
    $text->setAttribute("style", "width:90px;");
    $text->setAttribute("value", $detailResult["strcustomerreceivecode"]);
    $td->appendChild($text);
    // if (!$isdecideObj) {
    //     $td->setAttribute("style", "display:none");
    // }
    $trBody->appendChild($td);

    // �ܵ�
    if ($detailResult["strcustomerdisplaycode"] != "") {
        $textContent = "[" . $detailResult["strcustomerdisplaycode"] . "]" . " " . $detailResult["strcustomerdisplayname"];
    } else {
        $textContent = "";
    }
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "strcompanydisplaycode");
    $trBody->appendChild($td);

    // Ǽ��
    $td = $doc->createElement("td", $detailResult["dtmdeliverydate"]);
    $td->setAttribute("id", "dtmdeliverydate");
    $trBody->appendChild($td);

    // ���ʬ��
    $textContent = "[" . $detailResult["lngsalesdivisioncode"] . "]" . " " . $detailResult["strsalesdivisionname"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngsalesdivisioncode");
    $trBody->appendChild($td);

    // ����ʬ
    $textContent = "[" . $detailResult["lngsalesclasscode"] . "]" . " " . $detailResult["strsalesclassname"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngsalesclasscode");
    $trBody->appendChild($td);

    // ñ��
    $textContent = toMoneyFormat($detailResult["lngmonetaryunitcode"], $detailResult["strmonetaryunitsign"], $detailResult["curproductprice"]);
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "curproductprice");
    $trBody->appendChild($td);

    // ñ��
    $td = $doc->createElement("td");
    $td->setAttribute("id", "lngproductunitcode");
    $select = $doc->createElement("select");
    foreach ($aryProductUnit as $productunit) {
        $option = $doc->createElement("option", $productunit["strproductunitname"]);
        $option->setAttribute("value", $productunit["lngproductunitcode"]);
        if ($productunit["lngproductunitcode"] == $detailResult["lngproductunitcode"]) {
            $option->setAttribute("selected", "true");
        }
        $select->appendChild($option);
    }
    $td->appendChild($select);
    $trBody->appendChild($td);

    // ����
    $lngunitquantity = 1;
    $detailResult["lngcartonquantity"] = $detailResult["lngcartonquantity"] == null ? 0 : $detailResult["lngcartonquantity"];
    $detailResult["lngproductquantity"] = $detailResult["lngproductquantity_est"] == null ? 0 : $detailResult["lngproductquantity_est"];
    $lngproductquantity = $detailResult["lngproductquantity"] / $lngunitquantity;
    if ($detailResult["lngproductunitcode"] == 2) {
        $lngunitquantity = $detailResult["lngcartonquantity"];
        $lngproductquantity = $detailResult["lngproductquantity"] / $lngunitquantity;   
        $td = $doc->createElement("td");
        $text = $doc->createElement("input");
        $text->setAttribute("type", "text");
        $text->setAttribute("class", "form-control form-control-sm txt-kids");
        $text->setAttribute("style", "width:90px;");
        $text->setAttribute("value", $lngunitquantity);
        $td->appendChild($text);
    } else {
        $td = $doc->createElement("td", $lngunitquantity);
    }
    $td->setAttribute("id", "lngunitquantity");
    $td->setAttribute("style", "width:100px;");
    $trBody->appendChild($td);


    // ����
    $textContent = number_format($lngproductquantity);
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngproductquantity_re");
    $trBody->appendChild($td);

    // ����
    $textContent = toMoneyFormat($detailResult["lngmonetaryunitcode"], $detailResult["strmonetaryunitsign"], $detailResult["cursubtotalprice"]);
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "cursubtotalprice");
    $trBody->appendChild($td);

    // ����
    $td = $doc->createElement("td");
    $td->setAttribute("id", "strdetailnote");
    $text = $doc->createElement("input");
    $text->setAttribute("type", "text");
    $text->setAttribute("class", "form-control form-control-sm txt-kids");
    $text->setAttribute("style", "width:240px;");
    $text->setAttribute("value", toUTF8($detailResult["strdetailnote"]));
    $td->appendChild($text);
    $trBody->appendChild($td);

    // ���ʥ�����
    $textContent = $detailResult["strproductcode"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "strproductcode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ����̾��
    $textContent = "[" . $detailResult["strproductcode"] . "]" . " " . $detailResult["strproductname"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "strproductname");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // �����ֹ�
    $textContent = $detailResult["lngreceiveno"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngreceiveno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ��������
    $textContent = $detailResult["strreceivecode"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "strreceivecode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ��ӥ�����ֹ�
    $textContent = $detailResult["lngrevisionno"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngrevisionno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // �����ȥ�����
    $textContent = $detailResult["lngcartonquantity"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngcartonquantity");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ����
    $textContent = $detailResult["lngproductquantity"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngproductquantity");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ���ʥ�ӥ�����ֹ�
    $textContent = $detailResult["strrevisecode"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "strrevisecode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);
    
    if (!$isdecideObj) {
        // tbody > tr
        // $tbodyDetail->appendChild($trBody);
        
        $tblA_detail_body_html .= $doc->saveXML($trBody);
    } else {
        // tbody > tr
        // $tbodyDecideBody->appendChild($trBody);
        
        $tblB_body_html .= $doc->saveXML($trBody);

    }
}

// �����ǡ�����Ĵ��
$aryResult = array();
$aryResult["strProductCode"] = $aryDetailResult[0]["strproductcode"] . "_" . $aryDetailResult[0]["strrevisecode"];
$aryResult["strProductName"] = $aryDetailResult[0]["strproductname"];
$aryResult["strGoodsCode"] = $aryDetailResult[0]["strgoodscode"];
$aryResult["lngProductNo"] = $aryDetailResult[0]["lngproductno"];
$aryResult["lngProductRevisionNo"] = $aryDetailResult[0]["lngproductrevisionno"];
$aryResult["strReviseCode"] = $aryDetailResult[0]["strrevisecode"];
$aryResult["lngInChargeGroupCode"] = $aryDetailResult[0]["strinchargegroupdisplaycode"];
$aryResult["strInChargeGroupName"] = $aryDetailResult[0]["strinchargegroupdisplayname"];
$aryResult["lngInChargeUserCode"] = $aryDetailResult[0]["strinchargeuserdisplaycode"];
$aryResult["strInChargeUserName"] = $aryDetailResult[0]["strinchargeuserdisplayname"];
$aryResult["lngDevelopUserCode"] = $aryDetailResult[0]["strdevelopuserdisplaycode"];
$aryResult["strDevelopUserName"] = $aryDetailResult[0]["strdevelopuserdisplayname"];
$aryResult["strSessionID"] = $_GET["strSessionID"];
$aryResult["tblA_chkbox_result"] = $tblA_chkbox_body_html;
$aryResult["tblA_detail_result"] = $tblA_detail_body_html;
$aryResult["tblB_no_result"] = $tblB_no_html;
$aryResult["tblB_body_result"] = $tblB_body_html;
$aryResult["count"] = count($aryDetailResult);

//��̽���
mb_convert_variables('UTF-8', 'EUC-JP', $aryResult);
echo $s->encodeUnsafe($aryResult);
