<?
/** 
*	�����ƥ���� �����ƥ൯ư�������
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
// �����ƥ൯ư���괰λ���̤�
// index.php -> strSessionID  -> action.php
// index.php -> lngActionCode -> action.php


// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "sysc/cmn/lib_sys.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ�������
$aryData = $_GET;


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_SYS2, $objAuth ) )
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


$aryData["DEF_ACTION_RESTART"] = DEF_ACTION_RESTART;
$aryData["DEF_ACTION_STOP"]    = DEF_ACTION_STOP;

// HTML����
/*
echo fncGetReplacedHtml( "sysc/sev/parts.tmpl", $aryData, $objAuth );
*/
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/sev/sev.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;

?>
<!--
<input type="button" value="RESTART" onClick="javascript:window.location='action.php?strSessionID=<? echo $aryData["strSessionID"]; ?>&lngActionCode=<? echo DEF_ACTION_RESTART; ?>';">
<input type="button" value="STOP" onClick="javascript:window.location='action.php?strSessionID=<? echo $aryData["strSessionID"]; ?>&lngActionCode=<? echo DEF_ACTION_STOP; ?>';">
-->
<?

return TRUE;
?>
