<?php
/** 
*	�������������ɽ������
*
*	�������������ɽ������
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2004, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	���������Ԥ����֡��������Ƥ����򤵤�����̤�ɽ������
*
*/

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
// require (SRC_ROOT . "closed/cmn/lib_cld.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// GET�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ( $_GET )
{
	$aryData = $_GET;
}
else if ( $_POST )
{
	$aryData = $_POST;
}

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
// 1400 �������
if ( !fncCheckAuthority( DEF_FUNCTION_CLD0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// HTML����
$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "closed/closed.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();

return true;

?>