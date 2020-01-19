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

//$lngestimateno = fucGetEstimateNo(explode(",", $lngReceiveNo)[0], $objDB);


// ��¾��å��μ���
$objDB->transactionBegin();
if( isEstimateModified($lngestimateno, $lngRevisionNo, $objDB) )
{
    fncOutputError(501, DEF_ERROR,  "¾�Υ桼���ˤ�äƹ����ޤ��Ϻ������Ƥ��ޤ���", true, "../so/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

// ����ǡ�����å�
if(!lockReceiveFix($lngestimateno, DEF_FUNCTION_SO4, $objDB, $objAuth)){
    fncOutputError(501, DEF_ERROR, "�����ǡ�������å�����Ƥ��ޤ���", true, "../so/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
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


// ��������ֹ�μ���ǡ���������SQLʸ�κ���
$strQuery = fncGetReceiveHeadNoToInfoSQL($lngReceiveNo, $lngRevisionNo);

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
$aryNewResult["strproductcode"] = $aryResult[0]["strproductcode"]. "_".$aryResult[0]["strrevisecode"];
$aryNewResult["strproductname"] = $aryResult[0]["strproductname"];
$aryNewResult["strreceivecode"] = $aryResult[0]["strreceivecode"];
$aryNewResult["strnote"] = $aryResult[0]["strnote"];
$aryNewResult["strSessionID"] = $aryData["strSessionID"];
////////// ���ٹԤμ��� ////////////////////
// ��������ֹ�μ������٥ǡ���������SQLʸ�κ���
$strQuery = fncGetReceiveDetailNoToInfoSQL($lngReceiveNo, "");
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

// ���پ���ν���
$num = 0;
foreach ($aryDetailResult as $detailResult) {
    $num += 1;
    // tbody > tr���Ǻ���
    $trChkBox = $doc->createElement("tr");
    // ��������å��ܥå���
    $chkBox = $doc->createElement("input");
    $chkBox->setAttribute("type", "checkbox");
    $id = $detailResult["lngreceiveno"] . "_" . $detailResult["lngreceivedetailno"] . "_" . $detailResult["lngrevisionno"];
    $chkBox->setAttribute("id", $id);
    $chkBox->setAttribute("style", "width: 10px;");
    $tdChkBox = $doc->createElement("td");
    $tdChkBox->setAttribute("style", "width: 30px;");
    $tdChkBox->appendChild($chkBox);
    $trChkBox->appendChild($tdChkBox);
    // tbody > tr
    $tbodyChkBox->appendChild($trChkBox);

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    // No.
    $td = $doc->createElement("td", $num);
    $td->setAttribute("style", "width: 25px;");
    $trBody->appendChild($td);

    // ���ٹ��ֹ�
    $td = $doc->createElement("td", $detailResult["lngreceivedetailno"]);
    $trBody->appendChild($td);

    // �ܵ�
    if ($aryResult[0]["strcustomerdisplaycode"] != "") {
        $textContent = "[" . $aryResult[0]["strcustomerdisplaycode"] . "]" . " " . $aryResult[0]["strcustomerdisplayname"];
    } else {
        $textContent = "";
    }
    $td = $doc->createElement("td", toUTF8($textContent));
    $trBody->appendChild($td);

    // ���ʬ��
    $textContent = "[" . $detailResult["lngsalesdivisioncode"] . "]" . " " . $detailResult["strsalesdivisionname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $trBody->appendChild($td);

    // ����ʬ
    $textContent = "[" . $detailResult["lngsalesclasscode"] . "]" . " " . $detailResult["strsalesclassname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $trBody->appendChild($td);

    // Ǽ��
    $td = $doc->createElement("td", toUTF8($detailResult["dtmdeliverydate"]));
    $trBody->appendChild($td);
    // tbody > tr
    $tbodyDetail->appendChild($trBody);

}

$objDB->close();

// HTML����
echo $doc->saveHTML();
