<?
/** 
*	�����ƥ���� �������������
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
// �����ƥ���������괰λ���̤�
// index.php -> strSessionID              -> action.php
// index.php -> strSystemInformationTitle -> action.php
// index.php -> strSystemInformationBody  -> action.php
//
// �����ƥ�����ԥ��������̤�
// index.php -> strSessionID              -> log.php
//
// �����ƥ�����ԥ����󥻥å����������̤�
// index.php -> strSessionID              -> session.php


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
if ( !fncCheckAuthority( DEF_FUNCTION_SYS1, $objAuth ) )
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


// ���ߤΤ��Τ餻��������
$strQuery = "SELECT strSystemInformationTitle, strSystemInformationBody FROM m_SystemInformation ORDER BY dtmInsertDate DESC LIMIT 1";
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum > 0 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$aryData["strSystemInformationTitle"] = $objResult->strsysteminformationtitle;
	$aryData["strSystemInformationBody"]  = preg_replace ( "/<br>/i", "\n", $objResult->strsysteminformationbody );
}


// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/inf/inf.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;
?>
<?

return TRUE;
?>
