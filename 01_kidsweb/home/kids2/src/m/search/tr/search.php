<?
/** 
*	�ޥ������� ����졼�ȥޥ��� ��������
*
*	@package   KIDS
*	@license   http://www.solcom.co.jp/
*	@copyright Copyright &copy; 2019, Solcom
*	@author    solcom rin
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID    -> search.php
//

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_POST;

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// HIDDEN ����
$aryParts["strSessionID"] = $aryData["strSessionID"];

// �̲�ñ�̥����ɥץ�������˥塼 ����
$aryParts["lngMonetaryUnitCode"]  = "<option value=\"\"></option>\n";
$aryParts["lngMonetaryUnitCode"] .= fncGetPulldown( "m_MonetaryUnit", "lngMonetaryUnitCode", "strMonetaryUnitName || ':' ||strMonetaryUnitSign", "", " WHERE lngMonetaryUnitCode > 1", $objDB );


$objDB->close();

// HTML����

$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "/m/search/tr/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;


return TRUE;
?>
