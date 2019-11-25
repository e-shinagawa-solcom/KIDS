<?php

// ----------------------------------------------------------------------------
/**
 *       LC����  LC�������
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// �� �饤�֥��ե������ɹ�
//-------------------------------------------------------------------------
// �ɤ߹���
include 'conf.inc';
//���̥ե������ɤ߹���
require_once '../lcModel/lcModelCommon.php';
require_once '../lcModel/db_common.php';
require_once '../lcModel/kidscore_common.php';
require_once '../lcModel/lcinfo.php';
require LIB_FILE;

//-------------------------------------------------------------------------
// �� ���֥�����������
//-------------------------------------------------------------------------
$objDB = new clsDB();
$objAuth = new clsAuth();
//LC��DB��³���󥹥�������
$db = new lcConnect();

//-------------------------------------------------------------------------
// �� DB�����ץ�
//-------------------------------------------------------------------------
$objDB->open("", "", "", "");

//-------------------------------------------------------------------------
// �� �ѥ�᡼������
//-------------------------------------------------------------------------
$aryData = $_GET;

$aryData["strSessionID"] = $_REQUEST["strSessionID"]; // ���å����ID
$aryData["aclcinitFlg"] = $_REQUEST["aclcinitFlg"]; // T_Aclcinfo������ե饰

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

//�桼����ID����(Ⱦ�ѥ��ڡ��������뤿��)
$usrId = trim($objAuth->UserID);
$usrName = trim($objAuth->UserDisplayName);

// // 2100 LC����
// if ( !fncCheckAuthority( DEF_FUNCTION_LC0, $objAuth ) )
// {
//         fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
// }

// // 2101 LC����
// if ( !fncCheckAuthority( DEF_FUNCTION_LC1, $objAuth ) )
// {
//         fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
// }

//�������֥����ƥ�DB��³
$lcModel = new lcModel();

//�桼�������¤μ���
$loginUserAuth = $lcModel->getUserAuth($usrId);

$userAuth = substr($loginUserAuth, 1, 1);

//����������κ�������ֹ�μ���
$maxLgno = $lcModel->getMaxLoginStateNum();


// T_Aclcinfo������ե饰��true�ξ��
if ($aryData["aclcinitFlg"] == "true") {
    // t_aclcinfo�ǡ�������Ͽ����������
    // kidscore2������֤����դ��������
    $curDate = fncGetCurDate($objDB);
//    $date = explode(" ", $curDate)[0];
//    $time = explode(" ", $curDate)[1];

    // L/C�ǡ������������
    fncGetLcData($objDB, $lcModel, $usrName, $curDate);
    // lcgetdate�򹹿�����
    $updCount = $lcModel->updateLcGetDate($maxLgno, date('Ymd h:m:s', strtotime($curDate)));

    if ($updCount < 0) {
        $lcModel->updateLgStateToInit($maxLgno);
    }

    // ackids�Υǡ�����kidscore2����Ͽ
    // �ȥ�󥶥������򳫻Ϥ���
    $objDB->transactionBegin();
    // L/C����ǡ����κ����Ԥ�
    fncDeleteLcInfo($objDB);
    //ACL/C����ǡ����μ���
    $acLcInfoArry = $lcModel->getAcLcInfo();
    foreach ($acLcInfoArry as $acLcInfo) {
        $data = array();
        $data["pono"] = $acLcInfo["pono"];
        $data["polineno"] = $acLcInfo["polineno"];
        $data["poreviseno"] = $acLcInfo["poreviseno"];
        $data["postate"] = $acLcInfo["postate"];
        $data["opendate"] = $acLcInfo["opendate"];
        $data["portplace"] = $acLcInfo["portplace"];
        $data["payfcd"] = $acLcInfo["payfcd"];
        $data["payfnameomit"] = $acLcInfo["payfnameomit"];
        $data["payfnameformal"] = $acLcInfo["payfnameformal"];
        $data["productcd"] = $acLcInfo["productcd"];
        $data["productname"] = $acLcInfo["productname"];
        $data["productnamee"] = $acLcInfo["productnamee"];
        $data["productnumber"] = $acLcInfo["productnumber"];
        $data["unitname"] = $acLcInfo["unitname"];
        $data["unitprice"] = $acLcInfo["unitprice"];
        $data["moneyprice"] = $acLcInfo["moneyprice"];
        $data["shipstartdate"] = $acLcInfo["shipstartdate"];
        $data["shipenddate"] = $acLcInfo["shipenddate"];
        $data["sumdate"] = $acLcInfo["sumdate"];
        $data["poupdatedate"] = $acLcInfo["poupdatedate"];
        $data["deliveryplace"] = $acLcInfo["deliveryplace"];
        $data["currencyclass"] = $acLcInfo["currencyclass"];
        $data["lcnote"] = $acLcInfo["lcnote"];
        $data["shipterm"] = $acLcInfo["shipterm"];
        $data["validterm"] = $acLcInfo["validterm"];
        $data["bankcd"] = $acLcInfo["bankcd"];
        $data["bankname"] = $acLcInfo["bankname"];
        $data["bankreqdate"] = $acLcInfo["bankreqdate"];
        $data["lcno"] = $acLcInfo["lcno"];
        $data["lcamopen"] = $acLcInfo["lcamopen"];
        $data["validmonth"] = $acLcInfo["validmonth"];
        $data["usancesettlement"] = $acLcInfo["usancesettlement"];
        $data["bldetail1date"] = $acLcInfo["bldetail1date"];
        $data["bldetail1money"] = $acLcInfo["bldetail1money"];
        $data["bldetail2date"] = $acLcInfo["bldetail2date"];
        $data["bldetail2money"] = $acLcInfo["bldetail2money"];
        $data["bldetail3date"] = $acLcInfo["bldetail3date"];
        $data["bldetail3money"] = $acLcInfo["bldetail3money"];
        $data["lcstate"] = $acLcInfo["lcstate"];
        $data["shipym"] = $acLcInfo["shipym"];
        fncInsertLcInfo($objDB, $data);
    }
    
    $objDB->transactionCommit;
    // $data["from"] = "201905";
    // $data["mode"] = "0";
    // $result = fncGetLcInfoData($objDB, $data);
    // var_dump($result);

}

//���ط��������
$background_color = $lcModel->getBackColor();


$objDB->close();
$lcModel->close();

//HTML�ؤΰ����Ϥ��ǡ���
//$aryData["chkEpRes"] = $chkEpRes;

echo fncGetReplacedHtmlWithBase("lc/base_lc.html", "lc/info/parts.tmpl", $aryData, $objAuth);

//��������¹�
//js�ؤΰ����Ϥ��ǡ���
$arr = array(
    "chkEpRes" => $chkEpRes,
    "background_color" => $background_color,
    "userAuth" => $userAuth,
    "session_id" => $aryData["strSessionID"],
    "reSearchFlg" => $aryData["reSearchFlg"],
);
mb_convert_variables('UTF-8', 'EUC-JP', $arr);
echo "<script>$(function(){lcInit('" . json_encode($arr) . "');});</script>";
return true;
