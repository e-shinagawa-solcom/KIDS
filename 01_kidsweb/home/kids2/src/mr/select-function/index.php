<?php

// ----------------------------------------------------------------------------
/**
*       �ⷿĢɼ����  ��ǽ�������
*
*       ��������
*         ����˥塼���̤ˤƶⷿĢɼ�����ܥ����ľ�ܲ��������ݤ�ɽ�����뵡ǽ�������
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
	// ���å����˥��å����ID�򥻥å�
	setcookie("strSessionID",$_POST["strSessionID"]);

	// 1900 �ⷿĢɼ����
	if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
	{
	        fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// �إ���б�
	$aryData["lngFunctionCode"] = DEF_FUNCTION_MM0;

	$objDB->close();

	echo fncGetReplacedHtmlWithBase("base_mold.html", "/mr/select-function/parts.tmpl", $aryData ,$objAuth );
	return true;
?>
