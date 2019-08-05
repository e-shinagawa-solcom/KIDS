<?php

// ----------------------------------------------------------------------------
/**
*       �������  ��˥塼����
*
*       ��������
*         ����˥塼���̤�ɽ��
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



	// �ɤ߹���
	include('conf.inc');
	require (LIB_FILE);
	
	
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	$objDB->open("", "", "", "");

	$aryData["strSessionID"] = $_POST["strSessionID"];

	
	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );
	
	// ���å�����ǧ
	$objAuth = fncIsSession( $_POST["strSessionID"], $objAuth, $objDB );


	// 400	�������
	if ( !fncCheckAuthority( DEF_FUNCTION_SO0, $objAuth ) )
	{
	        fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	
	// 402 ��������ʼ�������
	if ( fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
	{
		$aryData["strSearchURL"]   = "search/index.php?strSessionID=" . $aryData["strSessionID"];
	}

	// �إ���б�
	$aryData["lngFunctionCode"] = DEF_FUNCTION_SO0;

	echo fncGetReplacedHtml( "so/parts.tmpl", $aryData ,$objAuth );

	$objDB->close();
	return true;
?>