<?php

// ----------------------------------------------------------------------------
/**
 *       LC����  LC�Խ�����
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// �� �饤�֥��ե������ɹ�
//-------------------------------------------------------------------------
include 'conf.inc';
//���̥ե������ɤ߹���
require_once '../lcModel/lcModelCommon.php';
//���饹�ե�������ɤ߹���
require_once '../lcModel/db_common.php';
require_once '../lcModel/kidscore_common.php';
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

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);


//�桼����ID����(Ⱦ�ѥ��ڡ��������뤿��)
$usrId = trim($objAuth->UserID);

// // 2100 LC����
// if ( !fncCheckAuthority( DEF_FUNCTION_LC0, $objAuth ) )
// {
//         fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
// }

// // 2101 LC�Խ�
// if ( !fncCheckAuthority( DEF_FUNCTION_LC2, $objAuth ) )
// {
//         fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
// }

$objDB->close();

//�������֥����ƥ�DB��³
$lcModel = new lcModel();

//���������Ƚ�����
$logined_flg = false;
$login_state = $lcModel->getLoginState($usrId);

//�桼�������¤μ���
$loginUserAuth = $lcModel->getUserAuth($usrId);

$userAuth = substr($loginUserAuth, 1, 1);

//HTML�ؤΰ����Ϥ��ǡ���
$aryData["login_state"] = $login_state;

echo fncGetReplacedHtmlWithBase("lc/base_lc.html", "lc/edit/parts.tmpl", $aryData, $objAuth);

//��������¹�
//js�ؤΰ����Ϥ��ǡ���
$arr = array(
    "login_state" => $login_state,
    "session_id" => $aryData["strSessionID"],
    "userAuth" => $userAuth,
    "pono" => $_REQUEST["pono"],
    "poreviseno" => $_REQUEST["poreviseno"],
    "polineno" => $_REQUEST["polineno"],
);
mb_convert_variables('UTF-8', 'EUC-JP', $arr);
echo "<script>$(function(){lcInit('" . json_encode($arr) . "');});</script>";
return true;
