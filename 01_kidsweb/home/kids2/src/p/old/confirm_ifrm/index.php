<?php

// ----------------------------------------------------------------------------
/**
*       ���ʴ���  ������ʥꥹ��
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
*         ����Ͽ��ǧ���̤ǡ�������ʥꥹ�Ȥ�ɽ��
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
	
	$aryData["strSessionID"]    = $_GET["strSessionID"];
	//echo "session :".$aryData["strSessionID"];

	
	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );
	
	
	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	
	
	//print_r($aryData);
	$objDB->close();
	
	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/p/confirm_ifrm/parts.tmpl" );
	
	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;
	
	
	return true;
	
?>

