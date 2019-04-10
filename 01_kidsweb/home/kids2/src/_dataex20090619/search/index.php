<?
/** 
*	�ǡ����������ݡ��� ��������
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
// index.php -> strSessionID  -> index.php
// index.php -> lngExportData -> index.php


// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "dataex/cmn/lib_dataex.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ�������
$aryData = $_GET;


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ�Τ���ν����оݤε�ǽ�����ɤ����
$lngFunctionCode = getFunctionCode( $aryData["lngExportData"] );



// ���³�ǧ
if ( !fncCheckAuthority( $lngFunctionCode, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]  = "null:numenglish(32,32)";
$aryCheck["lngExportData"] = "null:number(DEF_EXPORT_SALES,DEF_EXPORT_STOCK)";


// L��C������(����)
$strDefaultLCDate = date( "Y/m/d", strtotime( "-1 day" ) );

$aryData["lcdatestart"] = $strDefaultLCDate;
$aryData["lcdateend"]   = $strDefaultLCDate;


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//echo getArrayTable( $aryCheckResult, "TABLE" );
//echo getArrayTable( $aryData, "TABLE" );
//exit;
fncPutStringCheckError( $aryCheckResult, $objDB );


// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "dataex/search/" . $aryDirName[$aryData["lngExportData"]] . "/parts.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;
?>
