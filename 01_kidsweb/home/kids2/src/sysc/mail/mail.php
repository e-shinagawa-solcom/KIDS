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
// �����ƥ������˥塼����
// index.php -> strSessionID -> index.php
//
// �����ƥ�᡼�����괰λ���̤�
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
$aryData = $_GET;


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_SYS3, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]  = "null:numenglish(32,32)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����ƥ��������", TRUE, "", $objDB );
}


// ���ߤδ����ԥ᡼�륢�ɥ쥹����
$strQuery = "SELECT strValue AS adminmailaddress FROM m_AdminFunction WHERE strClass = 'adminmailaddress'";
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum > 0 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
}
else
{
	$objResult->adminmailaddress = "";
}

$aryData["strAdminMailAddress"] = $objResult->adminmailaddress;

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/mail/mail.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;

?>
<!--
<form action="action.php" method="POST">
<input type="hidden" name="strSessionID" value="<? echo $aryData["strSessionID"]; ?>">
ADMIN MAIL ADDRESS:<input type="text" name="strAdminMailAddress" value="<? echo $objResult->adminmailaddress; ?>">
<input type="submit">
</form>
-->
<?

return TRUE;
?>
