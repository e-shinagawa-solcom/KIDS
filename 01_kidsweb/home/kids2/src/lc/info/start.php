<?php

// ----------------------------------------------------------------------------
/**
*       LC����  LC���󳫻�
*       initLcInfo��¹Ԥ���������ζ��β��̤Ǥ���
*/
// ----------------------------------------------------------------------------

	// �ɤ߹���
	include('conf.inc');
	//���̥ե������ɤ߹���
	require SRC_ROOT . "lc/lcModel/lcModelCommon.php";
	require (LIB_FILE);


	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	$objDB->open("", "", "", "");


	//-------------------------------------------------------------------------
	// �� �ѥ�᡼������
	//-------------------------------------------------------------------------
	$aryData = $_REQUEST;
	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	// ���å����˥��å����ID�򥻥å�
	setcookie("strSessionID",$aryData["strSessionID"]);

	//�桼����ID����(Ⱦ�ѥ��ڡ��������뤿��)
	$user_id = trim($objAuth->UserID);
	
	$objDB->close();
	
	//HTML�ؤΰ����Ϥ��ǡ���
	$aryData["session_id"] = $aryData["strSessionID"];

	echo fncGetReplacedHtmlWithBase("lc/base_lc.html", "lc/info/start.tmpl", $aryData ,$objAuth );

	return true;
?>