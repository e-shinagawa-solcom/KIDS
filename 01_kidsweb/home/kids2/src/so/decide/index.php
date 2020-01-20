<?php

// ----------------------------------------------------------------------------
/**
 *       ������� ����
 *
 *       ��������
 *         ����������ֹ�ǡ����γ������
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

// �����ɤ߹���
include_once 'conf.inc';

// �饤�֥���ɤ߹���
require LIB_FILE;
require LIB_DEBUGFILE;
require LIB_EXCLUSIVEFILE;
require SRC_ROOT . "so/cmn/lib_so.php";
// DB��³
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// GET�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ($_GET) {
    $aryData = $_GET;
} else if ($_POST) {
    $aryData = $_POST;
}

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);
// ���³�ǧ
// 402 ��������ʼ�������
if (!fncCheckAuthority(DEF_FUNCTION_SO2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}
// 404 ��������ʳ����
if (!fncCheckAuthority(DEF_FUNCTION_SO4, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

//�ܺٲ��̤�ɽ��
$lngReceiveNo = $aryData["lngReceiveNo"];
$lngRevisionNo = $aryData["revisionNo"];
$lngestimateno = $aryData["estimateNo"];

$lngReceiveNoList = explode(",", $lngReceiveNo);

if( !is_null($aryData["mode"] ) )
{
    // ��¾��å��β���
    $objDB->transactionBegin();
    $result = unlockExclusive($objAuth, $objDB);
    $objDB->transactionCommit();
    return true; 
}


// ��¾��å��μ���
$objDB->transactionBegin();
if( isEstimateModified($lngestimateno, $lngRevisionNo, $objDB) )
{
    fncOutputError(401, DEF_ERROR,  "¾�Υ桼���ˤ�äƹ����ޤ��Ϻ������Ƥ��ޤ���", true, "../so/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

// ����ǡ�����å�
if(!lockReceiveFix($lngestimateno, DEF_FUNCTION_SO4, $objDB, $objAuth)){
    fncOutputError(401, DEF_ERROR, "�����ǡ�������å�����Ƥ��ޤ���", true, "../so/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

foreach($lngReceiveNoList as $eachLngReceiveNo)
{
    if( !lockReceive($eachLngReceiveNo, $objDB)){
	    fncOutputError( 401, DEF_ERROR, "�����ǡ����Υ�å��˼��Ԥ��ޤ���", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
    }
    // ����ǡ�������̵ͭ�����å�
    if( isReceiveModified($eachLngReceiveNo, DEF_RECEIVE_APPLICATE, $objDB)){
	    fncOutputError( 404, DEF_ERROR, "", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
    }
}

$objDB->transactionCommit();

//�ܺٲ��̤�ɽ��
// $lngReceiveNo = $aryData["lngReceiveNo"];
// $lngRevisionNo = $aryData["lngRevisionNo"];
// ��������ֹ�μ���ǡ���������SQLʸ�κ���
$strQuery = fncGetReceiveHeadInfoSQL($aryData["lngReceiveNo"], $lngestimateno);

// �ܺ٥ǡ����μ���
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult = pg_fetch_all($lngResultID);
    }
} else {
    fncOutputError(403, DEF_ERROR, "�����ǡ����μ����˼��Ԥ��ޤ���", true, "../so/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

$objDB->freeResult($lngResultID);
// �����ǡ�����Ĵ��
$aryNewResult = array();
$aryNewResult["strProductCode"] = $aryResult[0]["strproductcode"] . "_" . $aryResult[0]["strrevisecode"];
$aryNewResult["strProductName"] = $aryResult[0]["strproductname"];
$aryNewResult["strGoodsCode"] = $aryResult[0]["strgoodscode"];
$aryNewResult["lngProductNo"] = $aryResult[0]["lngproductno"];
$aryNewResult["lngProductRevisionNo"] = $aryResult[0]["lngproductrevisionno"];
$aryNewResult["strReviseCode"] = $aryResult[0]["strrevisecode"];
$aryNewResult["lngInChargeGroupCode"] = $aryResult[0]["strinchargegroupdisplaycode"];
$aryNewResult["strInChargeGroupName"] = $aryResult[0]["strinchargegroupdisplayname"];
$aryNewResult["lngInChargeUserCode"] = $aryResult[0]["strinchargeuserdisplaycode"];
$aryNewResult["strInChargeUserName"] = $aryResult[0]["strinchargeuserdisplayname"];
$aryNewResult["lngDevelopUserCode"] = $aryResult[0]["strdevelopuserdisplaycode"];
$aryNewResult["strDevelopUserName"] = $aryResult[0]["strdevelopuserdisplayname"];
$aryNewResult["strSessionID"] = $aryData["strSessionID"];

for ($i = 0; $i < $lngResultNum; $i++) {
    if ($i == 0) {
        $lngReceiveNo = $aryResult[$i]["lngreceiveno"];
    } else {
        $lngReceiveNo .= "," . $aryResult[$i]["lngreceiveno"];
    }
}

////////// ���ٹԤμ��� ////////////////////
// ��������ֹ�μ������٥ǡ���������SQLʸ�κ���
$strQuery = fncGetReceiveDetailNoToInfoSQL($lngReceiveNo, $aryResult[0]["lngrevisionno"]);
// echo $strQuery;
// ���٥ǡ����μ���
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryDetailResult[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    fncOutputError(403, DEF_WARNING, "�����ֹ���Ф������پ��󤬸��Ĥ���ޤ���", true, "../so/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
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

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/so/decide/so_decide.html");
$objTemplate->replace($aryNewResult);
$strTemplate = $objTemplate->strTemplate;
// ������̥ơ��֥������ΰ�DOMDocument�����
$doc = new DOMDocument();
// �ѡ������顼����
libxml_use_internal_errors(true);
// DOM�ѡ���
$doc->loadHTML(mb_convert_encoding($strTemplate, "utf8", "eucjp-win"));
// �ѡ������顼���ꥢ
libxml_clear_errors();
// �ѡ������顼�������
libxml_use_internal_errors(false);

// ������̥ơ��֥�μ���
$tableChkBox = $doc->getElementById("tbl_detail_chkbox");
$tbodyChkBox = $tableChkBox->getElementsByTagName("tbody")->item(0);

$tableDetail = $doc->getElementById("tbl_detail");
$tbodyDetail = $tableDetail->getElementsByTagName("tbody")->item(0);

$lngReceiveNos = explode(",", $aryData["lngReceiveNo"]);

if ($lngReceiveNos) {
    // ɽ�����ܤ����
    foreach ($lngReceiveNos as $key) {
        $lngReceiveNos[$key] = $key;
    }
}

// ������̥ơ��֥�μ���
$tabledecideno = $doc->getElementById("tbl_decide_no");
$tbodyDecideNO = $tabledecideno->getElementsByTagName("tbody")->item(0);

$tabledecidebody = $doc->getElementById("tbl_decide_body");
$tbodyDecideBody = $tabledecidebody->getElementsByTagName("tbody")->item(0);

// ���پ���ν���
$detailNum = 0;
$decideNum = 0;
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
        $tbodyChkBox->appendChild($trChkBox);

    } else {
        $decideNum += 1;
        // tbody > tr���Ǻ���
        $trBody = $doc->createElement("tr");
        // No.
        $td = $doc->createElement("td", $decideNum);
        $td->setAttribute("style", "width: 25px;");
        $trBody->appendChild($td);
        // tbody > tr
        $tbodyDecideNO->appendChild($trBody);
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
    if ($aryResult[0]["strcustomerdisplaycode"] != "") {
        $textContent = "[" . $aryResult[0]["strcustomerdisplaycode"] . "]" . " " . $aryResult[0]["strcustomerdisplayname"];
    } else {
        $textContent = "";
    }
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strcompanydisplaycode");
    $trBody->appendChild($td);

    // Ǽ��
    $td = $doc->createElement("td", $detailResult["dtmdeliverydate"]);
    $td->setAttribute("id", "dtmdeliverydate");
    $trBody->appendChild($td);

    // ���ʬ��
    $textContent = "[" . $detailResult["lngsalesdivisioncode"] . "]" . " " . $detailResult["strsalesdivisionname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngsalesdivisioncode");
    $trBody->appendChild($td);

    // ����ʬ
    $textContent = "[" . $detailResult["lngsalesclasscode"] . "]" . " " . $detailResult["strsalesclassname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngsalesclasscode");
    $trBody->appendChild($td);

    // ñ��
    $textContent = toMoneyFormat($aryResult[0]["lngmonetaryunitcode"], $aryResult[0]["strmonetaryunitsign"], $detailResult["curproductprice"]);
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
    $textContent = toMoneyFormat($aryResult[0]["lngmonetaryunitcode"], $aryResult[0]["strmonetaryunitsign"], $detailResult["cursubtotalprice"]);
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
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strproductcode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ����̾��
    $textContent = "[" . $detailResult["strproductcode"] . "]" . " " . $detailResult["strproductname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strproductname");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // �����ֹ�
    $textContent = $detailResult["lngreceiveno"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngreceiveno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ��������
    $textContent = $aryResult[0]["strreceivecode"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strreceivecode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ��ӥ�����ֹ�
    $textContent = $detailResult["lngrevisionno"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngrevisionno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // �����ȥ�����
    $textContent = $detailResult["lngcartonquantity"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngcartonquantity");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ����
    $textContent = $detailResult["lngproductquantity"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngproductquantity");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // ���ʥ�ӥ�����ֹ�
    $textContent = $detailResult["strrevisecode"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strrevisecode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);
    
    if (!$isdecideObj) {
        // tbody > tr
        $tbodyDetail->appendChild($trBody);
    } else {
        // tbody > tr
        $tbodyDecideBody->appendChild($trBody);

    }
}

$objDB->close();

// HTML����
echo $doc->saveHTML();
