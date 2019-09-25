<?php

// ----------------------------------------------------------------------------
/**
*       LC����  LCĢɼ���ϲ���
*/
// ----------------------------------------------------------------------------

	//-------------------------------------------------------------------------
	// �� �饤�֥��ե������ɹ�
	//-------------------------------------------------------------------------
	// �ɤ߹���
	include('conf.inc');
	//���̥ե������ɤ߹���
	require_once '../lcModel/lcModelCommon.php';
	//���饹�ե�������ɤ߹���
	require_once '../lcModel/db_common.php';
	require (LIB_FILE);

	//-------------------------------------------------------------------------
	// �� ���֥�����������
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();

	//-------------------------------------------------------------------------
	// �� DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");
	//LC��DB��³���󥹥�������
    $db			= new lcConnect();

	//-------------------------------------------------------------------------
	// �� �ѥ�᡼������
	//-------------------------------------------------------------------------
	$aryData = $_POST;

	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];   // ���å����ID
	$aryData["lngLanguageCode"] = 1; // ���쥳����

	setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

	// ���å�����ǧ
	// $objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

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

	$objDB->close();

	//�������֥����ƥ�DB��³
	$lcModel		= new lcModel();

	//�桼�������¤μ���
	$login_user_auth = $lcModel->getUserAuth($user_id);

	//����������κ�������ֹ�μ���
	$maxLgno = $lcModel->getMaxLoginStateNum();

	//��¾����
	$chkEpRes = $lcModel->chkEp($maxLgno, substr($login_user_auth["usrauth"], 1, 1), $user_id);

	//������Ԥ�̵ͭ
	$loginCount = $lcModel->getUserCount();

	echo fncGetReplacedHtmlWithBase("lc/base_lc.html", "lc/report/parts.tmpl", $aryData ,$objAuth );
	
	//��������¹�
	//js�ؤΰ����Ϥ��ǡ���
	$arr = array(
		"chkEpRes" => $chkEpRes,
		"userAuth" => substr($login_user_auth["usrauth"], 1, 1),
		"session_id" => $aryData["strSessionID"],
		"openDate" => $_REQUEST["openDate"]
	);
	mb_convert_variables('UTF-8' , 'EUC-JP' , $arr );
	echo "<script>$(function(){lcInit('". json_encode($arr) ."');});</script>";
	
	return true;
?>