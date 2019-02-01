<?
/** 
*	�桼��������TOP����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID    -> index.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

if ( $_GET["strSessionID"] )
{
	$aryData["strSessionID"]    = $_GET["strSessionID"];
}
else
{
	$aryData["strSessionID"]    = $_POST["strSessionID"];
}

// ʸ��������å�
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_UC0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

$aryData["lngFunctionCode1"] = DEF_FUNCTION_UC1;
$aryData["visibility1"]      = "visible";
$aryData["visibility2"]      = "hidden";
$aryData["visibility3"]      = "hidden";

if ( fncCheckAuthority( DEF_FUNCTION_UC2, $objAuth ) )
{
	$aryData["lngFunctionCode2"] = DEF_FUNCTION_UC2;
	$aryData["visibility2"]      = "visible";
}
if ( fncCheckAuthority( DEF_FUNCTION_UC3, $objAuth ) )
{
	$aryData["lngFunctionCode3"] = DEF_FUNCTION_UC3;
	$aryData["visibility3"]      = "visible";
}

$objDB->close();


// �إ�ץ���ѵ�ǽ�����ɤ򥻥å�
$aryData["lngFunctionCode"] = DEF_FUNCTION_UC0;

// HTML����
echo fncGetReplacedHtml( "uc/parts.tmpl", $aryData, $objAuth );
?>
