<?php
/** 
*	���Ѹ������� ����������ϲ���
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
// index.php -> lngFunctionCode -> index.php
//

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
//require (SRC_ROOT . "wf/cmn/lib_wf.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData   = $_GET;

$aryParts = fncStringToArray ( $_COOKIE["UserSearch"], "&", ":" );

//$aryParts = array_merge ( $_GET, $_COOKIE );

// ʸ��������å�
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", FALSE, "", $objDB );
}


$aryParts["strSessionID"] = &$aryData["strSessionID"];




// ���å���������
if( $_COOKIE["EstimateSearch"] )
{
	$aryCookie = fncStringToArray ( $_COOKIE["EstimateSearch"], "&", ":" );
	while ( list ($strKeys, $strValues ) = each ( $aryCookie ) )
	{
		$aryParts[$strKeys] = $strValues;
	}
}


// ����ե�����
$aryParts["lngWorkFlowStatusCode"] 	= fncGetCheckBoxObject( "m_estimatestatus", "lngestimatestatuscode", "strestimatestatusname", "lngWorkFlowStatusCode[]", '', $objDB );



// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "estimate/search_ifrm/parts.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryParts );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;

$objDB->close();

return true;

?>
