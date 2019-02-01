<?
/** 
*	�����ƥ���� �᡼���������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// �����ƥ�᡼�����괰λ����
// index.php -> strSessionID        -> action.php
// index.php -> strAdminMailAddress -> action.php


// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
//require (SRC_ROOT . "sysc/cmn/lib_sys.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ�������
$aryData = $_POST;


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_SYS3, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["strAdminMailAddress"] = "null:email(1,100)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//fncPutStringCheckError( $aryCheckResult, $objDB );


// �᡼��ʸ���󥨥顼�����å�
if ( $aryCheckResult["strAdminMailAddress_Error"] )
{
	//echo getArrayTable( $aryData, "TABLE" );exit;
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<form action=mail.php method=GET>";
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=javascript>document.forms[0].submit();</script>";
	exit;
}


// �ȥ�󥶥�����󳫻�
$objDB->transactionBegin();

// ���������¹�
$strQuery = "UPDATE m_AdminFunction SET strValue = '" . $aryData["strAdminMailAddress"] . "' WHERE strClass = 'adminmailaddress'";
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

// �ѹ�����᡼������
list ( $strSubject, $strBody ) = fncGetMailMessage( DEF_FUNCTION_SYS3, $aryData, $objDB );
if ( !$aryData["strAdminMailAddress"] || !mail ( $aryData["strAdminMailAddress"], $strSubject, $strBody, "From: " . $aryData["strAdminMailAddress"] . "\nReturn-Path: " . ERROR_MAIL_TO . "\n" ) )
{
	// �������Ԥˤ�����Хå�
	list ( $lngResultID, $lngResultNum ) = fncQuery( "ROLLBACK", $objDB );

	fncOutputError ( 9053, DEF_WARNING, "�᡼���������ԡ�", TRUE, "", $objDB );
}

// �ȥ�󥶥�����󥳥ߥå�
$objDB->transactionCommit();

//echo "<a href=../>BACK</a>";

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/mail/finish.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;


return TRUE;
?>
