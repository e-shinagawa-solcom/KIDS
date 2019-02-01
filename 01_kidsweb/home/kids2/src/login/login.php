<?php
/** 
*	������ɽ�������������
*
*	��������̤�ɽ������
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	��������̤���Υ桼��������������������Ԥ�
*
*	��������
*	2004.02.26	��ե��顼��Ƚ�Ǥ�TOP�ڡ����ʳ�����Υ��ɥ쥹��ľ�����ػߤ���
*
*/

// ���������

// ������ɤ߹���
include_once ( "conf.inc" );

// �饤�֥���ɤ߹���
require ( LIB_FILE );

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ����μ���
$aryData = $_POST;

if ( isset($aryData["strUserID"]) && isset($aryData["strPassword"]) )
{
	// ʸ��������å�
	$aryCheck["strUserID"]      = "null:ascii(0,20)";
	$aryCheck["strPassword"] 	= "null:ascii(0,20)";

	// �ѿ�̾�Ȥʤ륭�������
	$aryKey = array_keys( $aryCheck );
	$flag = TRUE;
	// �����ο����������å�
	foreach ( $aryKey as $strKey )
	{
		// $aryData[$strKey]  : �����å��оݥǡ���
		// $aryCheck[$strKey] : �����å�����(���͡��ѿ���������������)
		$strResult = fncCheckString( $aryData[$strKey], $aryCheck[$strKey] );
		if ( $strResult ) {
			list ( $lngErrorNo, $strErrorMessage ) = split ( ":", $strResult );
//			fncOutputError ( $lngErrorNo, DEF_ERROR, $strErrorMessage, FALSE, "", $objDB );
			$flag = FALSE;
		}
	}

	if ( $flag == FALSE )
	{
		fncOutputError ( 9052, DEF_ERROR, "������˼��Ԥ��ޤ�����", TRUE, "", $objDB );
		exit;
	}

	// ���å�����ǧ
	if ( !$objAuth->isLogin( $aryData["strSessionID"], $objDB ) )
	{
		// ǧ�ڽ���
		if ( !$objAuth->login( $aryData["strUserID"], $aryData["strPassword"], $objDB ) )
		{
			fncOutputError( 9052, DEF_ERROR, "������˼��Ԥ��ޤ�����", TRUE, "", $objDB );
			exit;
		}
	}

	// HTML���ϡʥ����ƥ��˥塼���̡�
	header ( "Location:  ../menu/menu.php?strSessionID=" . $objAuth->SessionID );

}
else
{
// 2004.02.26 suzukaze update start
	if ( $_GET["value"] == "kids" )
	{
		// HTML���ϡʥ�������̡�
		require ( TMP_ROOT . 'login/index.html' );
	}
	else
	{
		// HTML���ϡ�TOP��
		require ( SRC_ROOT . "index.html" );
	}
// 2004.02.26 suzukaze update end
}


$objDB->close();


return TRUE;
?>
