<?
/** 
*	�桼�������� ��������
*
*	@package   kuwagata
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
require (SRC_ROOT . "wf/cmn/lib_wf.php");

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_GET;

// ʸ��������å�
$aryCheck["strSessionID"]    = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_UC3, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", FALSE, "", $objDB );
}


// �إ�ץ���ѵ�ǽ�����ɤ򥻥å�
$aryData["lngFunctionCode"] = DEF_FUNCTION_UC3;

// HTML����
// $aryData["Pwin"] = "search.php?strSessionID=" . $aryData["strSessionID"];
//$aryData["Pwin"] = "../search_ifrm/index.html";
// echo fncGetReplacedHtml( "uc/search/parts.html", $aryData, $objAuth );
// lngCompanyCode SELECT��������
$aryData["lngCompanyCode"] = fncGetPulldown( "m_Company", "lngcompanyCode", "strCompanyDisplayCode || ' ' || strCompanyDisplayName", "", "WHERE bytCompanyDisplayFlag = TRUE", $objDB );

// lngAuthorityGroupCode SELECT��������
$aryData["lngAuthorityGroupCode"] = fncGetPulldown( "m_AuthorityGroup", "lngAuthorityGroupCode", "strAuthorityGroupName", "", "", $objDB );

// lngAccessIPAddressCode SELECT��������
$aryData["lngAccessIPAddressCode"] = fncGetPulldown( "m_AccessIPAddress", "lngAccessIPAddressCode", "strAccessIPAddress || ' ' || strNote", "", "", $objDB );


$objDB->close();
echo fncGetReplacedHtmlWithBase("search/base_search.html", "uc/search/search.html", $aryData, $objAuth);

return TRUE;
?>
