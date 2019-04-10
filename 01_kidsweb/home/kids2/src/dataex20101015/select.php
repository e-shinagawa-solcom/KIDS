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
require (SRC_ROOT . "dataex/cmn/lib_dataex.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ�������
$aryData = $_GET;


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


// lngExportData ������
if ( fncCheckAuthority( DEF_FUNCTION_DE1, $objAuth ) )
{
	//echo "OK!";
	$aryData["strLCURL"]            = DEF_EXPORT_LC;
}
if ( fncCheckAuthority( DEF_FUNCTION_DE2, $objAuth ) )
{
	$aryData["strSaleURL"]          = DEF_EXPORT_SALES;
}
if ( fncCheckAuthority( DEF_FUNCTION_DE3, $objAuth ) )
{
	$aryData["strStockURL"]         = DEF_EXPORT_STOCK;
}
if ( fncCheckAuthority( DEF_FUNCTION_DE4, $objAuth ) )
{
	$aryData["strPurchaseOrderURL"] = DEF_EXPORT_PURCHASE;
}

if ( fncCheckAuthority( DEF_FUNCTION_DE5, $objAuth ) )
{
	$aryData["strEstimateURL"] = DEF_EXPORT_ESTIMATE;
}

if ( fncCheckAuthority( DEF_FUNCTION_DE6, $objAuth ) )
{
	$aryData["strStat01URL"] = DEF_EXPORT_STAT01;
}
if ( fncCheckAuthority( DEF_FUNCTION_DE7, $objAuth ) )
{
	$aryData["strStat02URL"] = DEF_EXPORT_STAT02;
}



//echo fncCheckAuthority( DEF_FUNCTION_DE1, $objAuth );
//echo $objAuth->FunctionCode[1002];
//echo getArrayTable( $objAuth->FunctionCode, "TABLE" );


// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "dataex/select.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;
?>
