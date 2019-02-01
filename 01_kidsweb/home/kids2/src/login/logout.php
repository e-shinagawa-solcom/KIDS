<?php
/** 
*	�������ȡ�����
*
*	�������Ȳ��̤�ɽ�����������Ƚ���
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	�ᥤ���˥塼���������ȥܥ��󤬲����줿�ݤ˼¹�
*	�¹�OK�Ǥ���Х������Ƚ�����Ԥ�
*
*/

// �������Ƚ���

// ������ɤ߹���
include_once ( "conf.inc" );

// �饤�֥���ɤ߹���
require ( LIB_FILE );

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(����GET)�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}

// �������ȼ¹Խ����ʤΤ����������ȳ�ǧ�����ʤΤ�Ƚ��
if ( !$aryData["bytLogoutFlag"] )
{
	// �������ȳ�ǧ�����ξ��
	if ( isset( $aryData["strSessionID"] ) )
	{
		// ʸ��������å�
		$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
		$aryResult = fncAllCheck( $aryData, $aryCheck );
		fncPutStringCheckError( $aryResult, $objDB );

		// ���å�����ǧ
		$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

		// LanguageCode����
		$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

		// HTML����
		$fp = fopen ( TMP_ROOT . "login/logout.html", "r" );

		while ( $strTemplLine = fgets ( $fp, 1000 ) )
		{
			$strTempl .= $strTemplLine;
		}

		$strTempl = preg_replace ( "/_%strSessionID%_/i", $aryData["strSessionID"], $strTempl );
		$strTempl = preg_replace ( "/_%lngLanguageCode%_/i", $aryData["lngLanguageCode"], $strTempl );
		$strTempl = preg_replace ( "/_%bytLogoutFlag%_/i", TRUE, $strTempl );

		// �ִ�����ʤ��ä��֤�����ʸ�������
		$strTempl = preg_replace ( "/_%.+?%_/", "", $strTempl );

		echo $strTempl;
	}
	else
	{
		fncOutputError ( 9052, DEF_ERROR, "���å���󤬰۾�Ǥ���", TRUE, "", $objDB );
	}
}
else
{
	// �������ȼ¹Խ����ξ��
	if ( isset( $aryData["strSessionID"] ) )
	{
		// ʸ��������å�
		$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
		$aryResult = fncAllCheck( $aryData, $aryCheck );
		fncPutStringCheckError( $aryResult, $objDB );

		// ���å�����ǧ
		$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

		// �������ȼ¹Խ���
		fncLogout( $aryData["strSessionID"], $objDB );

		// HTML���ϡʥ�������̡�
		require ( TMP_ROOT . 'login/index.html' );
	}
	else
	{
		fncOutputError ( 9052, DEF_ERROR, "���å���󤬰۾�Ǥ���", TRUE, "", $objDB );
	}
}

$objDB->close();


return TRUE;
?>
