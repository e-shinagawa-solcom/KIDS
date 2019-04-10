<?
/** 
*	�ǡ����������ݡ��� ��˥塼����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// ��˥塼ɽ��
// index.php -> strSessionID -> index.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ�������
if ( $_POST )
{
	$aryData = $_POST;
}
else
{
	$aryData = $_GET;
}


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_DE0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]   = "null:numenglish(32,32)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//echo getArrayTable( $aryCheckResult, "TABLE" );
//echo getArrayTable( $aryData, "TABLE" );
//exit;
fncPutStringCheckError( $aryCheckResult, $objDB );

// �إ�ץ���ѵ�ǽ�����ɤ򥻥å�
$aryData["lngFunctionCode"] = DEF_FUNCTION_DE0;

// HTML����
echo fncGetReplacedHtml( "dataex/parts.tmpl", $aryData, $objAuth );
?>
