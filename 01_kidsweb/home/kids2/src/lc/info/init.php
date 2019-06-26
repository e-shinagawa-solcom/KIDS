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
	include('conf.inc');
	//���̥ե������ɤ߹���
	require_once '../lcModel/lcModelCommon.php';
	require_once '../lcModel/db_common.php';
	require_once '../lcModel/kidscore_common.php';
	require_once '../lcModel/lcinfo.php';
	require (LIB_FILE);
	
	//PHPɸ���JSON�Ѵ��᥽�åɤϥ��顼�ˤʤ�Τǳ����Υ饤�֥��(���餯���󥳡��ɤ�����)
	require_once '../lcModel/JSON.php';

	//-------------------------------------------------------------------------
	// �� ���֥�����������
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	//LC��DB��³���󥹥�������
    $db			= new lcConnect();

	//-------------------------------------------------------------------------
	// �� DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");

	//-------------------------------------------------------------------------
	// �� �ѥ�᡼������
	//-------------------------------------------------------------------------
	$aryData = $_POST;

	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];   // ���å����ID
	$aryData["reSearchFlg"]    = $_REQUEST["reSearchFlg"];   // �Ƹ����ե饰

	setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	//�桼����ID����(Ⱦ�ѥ��ڡ��������뤿��)
	$usrId = trim($objAuth->UserID);

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
	$lcModel		= new lcModel();

	//�桼�������¤μ���
	$loginUserAuth = $lcModel->getUserAuth($usrId);

	$userAuth = substr($loginUserAuth, 1, 1);

	//����������κ�������ֹ�μ���
	$maxLgno = $lcModel->getMaxLoginStateNum();

	//��¾����
	$chkEpRes = $lcModel->chkEp($maxLgno, $userAuth, $usrId);

	//������Ԥ�̵ͭ
	$loginCount = $lcModel->getUserCount();


	//�������
	$result = array();
	$result["strSessionID"] = $aryData["strSessionID"];
	$lcGetDate = "";
	$result["userAuth"] = $userAuth;
	$result["loginCount"] = $loginCount;
	$result["maxLgno"] = $maxLgno;
	//t_aclcinfo�ǡ�������Ͽ����������
	// �桼�������usrAuth�Σ����ܤ�ʸ����"1" ���뤤�ϡʥ桼�������usrAuth�Σ����ܤ�ʸ����"1"�ǤϤʤ������ġ������������� =1�ξ���
	if ($userAuth == "1" || ($userAuth != "1" && $loginCount == 1)) {
		// lcgetdate���������
		$acloginstate = $lcModel->getAcLoginstateBylgno($maxLgno);

		$result["lcgetdate"] = $acloginstate->lcgetdate;
	}

	$objDB->close();

	//JSON���饹���󥹥��󥹲�
	$s = new Services_JSON();
	//��̽���
	mb_convert_variables('UTF-8' , 'EUC-JP' , $result );
	echo $s->encodeUnsafe($result);
?>