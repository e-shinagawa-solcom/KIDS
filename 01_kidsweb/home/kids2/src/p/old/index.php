<?php

// ----------------------------------------------------------------------------
/**
*       ���ʴ���  ��˥塼����
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
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

	// 300 ���ʴ���
	if ( !fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
	{
	        fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}


	// �إ���б�
	$aryData["lngFunctionCode"] = DEF_FUNCTION_P0;


	$objDB->close();

	echo fncGetReplacedHtml( "p/parts.tmpl", $aryData ,$objAuth );
	return true;
?>