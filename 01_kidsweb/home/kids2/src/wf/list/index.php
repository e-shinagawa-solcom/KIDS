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

$aryData["strSessionID"]    = $_GET["strSessionID"];
$aryData["lngFunctionCode"] = DEF_FUNCTION_WF1;

// ʸ��������å�
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( $aryData["lngFunctionCode"], $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

$objDB->close();



// �إ�ץ���ѵ�ǽ�����ɤ򥻥å�
$aryData["lngFunctionCode"] = DEF_FUNCTION_WF1;

// HTML����

echo fncGetReplacedHtml( "wf/list/parts.tmpl", $aryData, $objAuth );



return TRUE;
?>
