<?
/** 
*	�桼�������� ����������ϲ���
*
*	@package   KIDS
*	@copyright Copyright (c) 2004, kuwagata 
*	@author    Kenji Chiba
*	@editor    Kazushi Saito 2009.08.30
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID    -> index.php
// index.php -> lngFunctionCode -> index.php
//

echo "TEST";
exit;
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

/*
// ʸ��������å�
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_UC3, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", FALSE, "", $objDB );
}
*/


// HIDDEN��������
$aryParts["strHiddenForm"] = "
<input type=\"hidden\" name=\"strSessionID\" value=\"" . $aryData["strSessionID"] . "\">
<input type=\"hidden\" name=\"lngFunctionCode\" value=\"" . DEF_FUNCTION_UC3 . "\">
";



// lngWorkflowStatusCode SELECT��������
$aryParts["workflowStatusCodeMenu"] = "
<option value=\"\"></option>
<option value=\"" . DEF_STATUS_ORDER . "\">������</option>
<option value=\"" . DEF_STATUS_APPROVE . "\">��ǧ</option>
<option value=\"" . DEF_STATUS_DENIAL . "\">��ǧ</option>
";

if ( $bytCancellFlag )
{
	$aryParts["workflowStatusCodeMenu"] .= "<option value=\"" . DEF_STATUS_CANCELL . "\">�������</option>\n";
}

// lngCompanyCode SELECT��������
$aryParts["lngCompanyCode"] = fncGetPulldown( "m_Company", "lngcompanyCode", "strCompanyDisplayCode || ' ' || strCompanyDisplayName", "", "WHERE bytCompanyDisplayFlag = TRUE", $objDB );

// lngAuthorityGroupCode SELECT��������
$aryParts["lngAuthorityGroupCode"] = fncGetPulldown( "m_AuthorityGroup", "lngAuthorityGroupCode", "strAuthorityGroupName", "", "", $objDB );

// lngAccessIPAddressCode SELECT��������
$aryParts["lngAccessIPAddressCode"] = fncGetPulldown( "m_AccessIPAddress", "lngAccessIPAddressCode", "strAccessIPAddress || ' ' || strNote", "", "", $objDB );

$aryParts["strSessionID"]    = &$aryData["strSessionID"];
$aryParts["lngFunctionCode"] = DEF_FUNCTION_UC3;

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "estimate/search/search.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;
?>
