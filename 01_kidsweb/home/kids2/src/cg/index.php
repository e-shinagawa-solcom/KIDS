<?
// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "uc/cmn/lib_uc.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_GET;

// �桼�������󡢥桼���������ξ��
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 || $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 )
{
	// ���SELECT��˥塼(Ʊ��°���δ�ȤΤ�)
	$aryParts["slctCompany"]  = fncGetPulldown( "m_Company c, m_AttributeRelation ar, m_AttributeRelation ar2", "c.lngCompanyCode", "c.strCompanyDisplayCode, c.strCompanyName", $aryData["lngCompanyCode"], "WHERE ar2.lngCompanyCode = " . $aryData["lngCompanyCode"] . " AND ( ar2.lngAttributeCode = 1 OR ar2.lngAttributeCode = 2 ) AND ar.lngAttributeCode = ar2.lngAttributeCode AND c.bytCompanyDisplayFlag = TRUE AND c.lngCompanyCode = ar.lngCompanyCode GROUP BY c.lngCompanyCode, c.strCompanyDisplayCode, c.strCompanyName", $objDB );

	// ���롼��SELECT��˥塼
	$aryParts["slctGroup2"]  = fncGetPulldown( "m_Group", "lngGroupCode", "strGroupDisplayCode, strGroupName", "", "WHERE bytGroupDisplayFlag = TRUE AND lngCompanyCode = " . $aryData["lngCompanyCode"], $objDB );

}

// �桼������Ͽ�ξ��
else
{
	// ���SELECT��˥塼(���٤Ƥδ��)
	$aryParts["slctCompany"]  = fncGetPulldown( "m_Company c, m_AttributeRelation ar", "c.lngCompanyCode", "c.strCompanyDisplayCode, c.strCompanyName", $aryData["lngCompanyCode"], "WHERE ( ar.lngAttributeCode = 1 OR ar.lngAttributeCode = 2 ) AND c.bytCompanyDisplayFlag = TRUE AND c.lngCompanyCode = ar.lngCompanyCode", $objDB );
}

// HTML����

$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "cg/index.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;

?>
