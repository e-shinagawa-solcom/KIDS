<?php

// ----------------------------------------------------------------------------
/**
 *       LC����  LC�����ѹ�����
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// �� �饤�֥��ե������ɹ�
//-------------------------------------------------------------------------
// �ɤ߹���
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
$aryData = $_POST;

$aryData["strSessionID"] = $_REQUEST["strSessionID"]; // ���å����ID
$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"]; // ���쥳����

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

//�桼����ID����(Ⱦ�ѥ��ڡ��������뤿��)
$usrId = trim($objAuth->UserID);

// //�桼����ID����(Ⱦ�ѥ��ڡ��������뤿��)
// $user_id = trim($objAuth->UserID);

// 2100 LC����
// if ( !fncCheckAuthority( DEF_FUNCTION_LC0, $objAuth ) )
// {
//         fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
// }

// // 2102 LC�����ѹ�
// if ( !fncCheckAuthority( DEF_FUNCTION_LC2, $objAuth ) )
// {
//         fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
// }

//�������֥����ƥ�DB��³
$lcModel = new lcModel();

//����������κ�������ֹ�μ���
$maxLgno = $lcModel->getMaxLoginStateNum();

// �������Ȼ���μ���
$acloginstate = $lcModel->getAcLoginstateBylgno($maxLgno);
$lgoutymd = $acloginstate->lgoutymd;

//�桼�������¤μ���
$loginUserAuth = $lcModel->getUserAuth($usrId);

$userAuth = substr($loginUserAuth, 1, 1);

//���������Ƚ�����
$logined_flg = false;
$loginState = $lcModel->getLoginState($usrId);

$objDB->transactionBegin();
// ackids�Υǡ�����kidscore2����Ͽ
// ackids�ζ�Ծ���μ���
$bankArry = $lcModel->getBankInfo();
// kidscore2�ζ�Ծ���κ��
$deltedNum = fncDeleteBank($objDB);
if ($deltedNum >= 0) {
    // kidscore2�ζ�Ծ������Ͽ
    if (count($bankArry) > 0) {
        foreach ($bankArry as $bank) {
            fncInsertBank($objDB, $bank);
        }
    }
}

// ackids�λ�ʧ�����μ���
$payfArry = $lcModel->getPayfInfo();
// kidscore2�λ�ʧ�����κ��
$deltedNum = fncDeletePayfinfo($objDB);
if ($deltedNum >= 0) {
// kidscore2�λ�ʧ��������Ͽ
    if (count($payfArry) > 0) {
        foreach ($payfArry as $payf) {
            fncInsertPayf($objDB, $payf);
        }
    }
}

$objDB->transactionCommit();
$objDB->close();
$lcModel->close();

//HTML�ؤΰ����Ϥ��ǡ���
$aryData["login_state"] = $loginState;

echo fncGetReplacedHtmlWithBase("lc/base_lc.html", "lc/set/parts.tmpl", $aryData, $objAuth);

//��������¹�
//js�ؤΰ����Ϥ��ǡ���
$arr = array(
    "login_state" => $loginState,
    "session_id" => $aryData["strSessionID"],
    "lgoutymd" => $lgoutymd,
    "userAuth" => $userAuth,
);
mb_convert_variables('UTF-8', 'EUC-JP', $arr);
echo "<script>$(function(){lcInit('" . json_encode($arr) . "');});</script>";
return true;
