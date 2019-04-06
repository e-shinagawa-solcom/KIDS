<?
/** 
*	���Ѹ������� ��������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
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

//////////////////////////////////////////////////////////////////////////
// POST(����GET)�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// ʸ��������å�
$aryCheck["strSessionID"]    = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_UC3, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", FALSE, "", $objDB );
}

$objDB->close();


// �إ�ץ���ѵ�ǽ�����ɤ򥻥å�
$aryData["lngFunctionCode"] = DEF_FUNCTION_UC3;

// HTML����
//$aryData["Pwin"] = "search.php?strSessionID=" . $aryData["strSessionID"];
//$aryData["Pwin"] = "../search_ifrm/index.html";
echo fncGetReplacedHtmlWithBase("search/base_search.html", "estimate/search/es_search.tmpl", $aryData ,$objAuth );



return TRUE;
?>
