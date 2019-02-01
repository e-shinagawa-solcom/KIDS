<?
/** 
*	�����ƥ���� ��˥塼����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// �ᥤ�����
// index.php -> strSessionID -> index.php
//
// �Ƶ�ǽ���̤�
// index.php -> strSessionID -> index.php


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
if ( $_GET )
{
	$aryData = $_GET;
}
else
{
	$aryData = $_POST;
}


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_SYS0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

$aryData["strInfVisibility"]     = "visible";
$aryData["strMailVisibility"]    = "visible";
$aryData["strSessionVisibility"] = "visible";
$aryData["strSevVisibility"]     = "visible";
if ( !fncCheckAuthority( DEF_FUNCTION_SYS1, $objAuth ) )
{
	$aryData["strInfVisibility"] = "hidden";
}
if ( !fncCheckAuthority( DEF_FUNCTION_SYS2, $objAuth ) )
{
	$aryData["strMailVisibility"] = "hidden";
}
if ( !fncCheckAuthority( DEF_FUNCTION_SYS3, $objAuth ) )
{
	$aryData["strSessionVisibility"] = "hidden";
}
if ( !fncCheckAuthority( DEF_FUNCTION_SYS4, $objAuth ) )
{
	$aryData["strSevVisibility"] = "hidden";
}


$aryCheck["strSessionID"]  = "null:numenglish(32,32)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����ƥ��������", TRUE, "", $objDB );
}


// HTML����
echo fncGetReplacedHtml( "sysc/parts.tmpl", $aryData, $objAuth );

?>
<!--
<input type="button" value="MESSAGE" onClick="javascript:window.location='/sysc/inf/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';">
<input type="button" value="SERVER" onClick="javascript:window.location='/sysc/sev/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';">
<input type="button" value="MAIL" onClick="javascript:window.location='/sysc/mail/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';">
<input type="button" value="SESSION" onClick="javascript:window.location='/sysc/session/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';">
-->
<?

return TRUE;
?>
