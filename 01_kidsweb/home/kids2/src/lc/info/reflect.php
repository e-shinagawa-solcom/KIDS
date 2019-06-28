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

	//�������֥����ƥ�DB��³
	$lcModel		= new lcModel();

	//����������κ�������ֹ�μ���
	$maxLgno = $lcModel->getMaxLoginStateNum();

	// �������Ȼ���μ���
	$acloginstate = $lcModel->getAcLoginstateBylgno($maxLgno);
	$lgoutymd = $acloginstate->lgoutymd;
	
	$objDB->close();

	//�������
	$result = array();
	$result["strSessionID"] = $aryData["strSessionID"];
	$result["lgoutymd"] = $lgoutymd;

	//JSON���饹���󥹥��󥹲�
	$s = new Services_JSON();
	//��̽���
	mb_convert_variables('UTF-8' , 'EUC-JP' , $result );
	echo $s->encodeUnsafe($result);
?>