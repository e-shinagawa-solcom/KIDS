<?php

// ----------------------------------------------------------------------------
/**
*       �ⷿ�������  ��ǽ�������
*
*       ��������
*         ����˥塼���̤ˤƶⷿ��������ܥ����ľ�ܲ��������ݤ˵�ǽ������̤�ɽ������
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
	// ���å����˥��å����ID�򥻥å�
	setcookie("strSessionID",$_POST["strSessionID"]);

	// 1800 �ⷿ�������
	if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
	{
	        fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	$objDB->close();

	echo fncGetReplacedHtmlWithBase("base_mold.html", "/mm/select-function/parts.tmpl", $aryData ,$objAuth );
	return true;
?>