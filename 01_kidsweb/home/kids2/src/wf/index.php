<?
/** 
*	����ե� �Ʒ������ǽ����
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
if ( !fncCheckAuthority( DEF_FUNCTION_WF0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
if ( fncCheckAuthority( DEF_FUNCTION_WF1, $objAuth ) )
{
	$aryData["strListURL"]   = "list/index.php?strSessionID=" . $aryData["strSessionID"];
}
if ( fncCheckAuthority( DEF_FUNCTION_WF2, $objAuth ) )
{
	$aryData["strSearchURL"] = "search/index.php?strSessionID=" . $aryData["strSessionID"];
}

$objDB->close();


// �إ�ץ���ѵ�ǽ�����ɤ򥻥å�
$aryData["lngFunctionCode"] = DEF_FUNCTION_WF0;

// HTML����
echo fncGetReplacedHtml( "wf/parts.tmpl", $aryData, $objAuth );
echo $_COOKIE["lngLanguageCode"];
?>
