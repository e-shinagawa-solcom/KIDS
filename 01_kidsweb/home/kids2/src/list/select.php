<?
/** 
*	Ģɼ���� Ģɼ�������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// Ģɼ�������
// index.php -> strSessionID    -> index.php

// �������̤�( * �ϻ���Ģɼ�Υե�����̾ )
// index.php -> strSessionID    -> *.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "list/cmn/lib_lo.php");

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

// ʸ��������å�
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryParts["strGoodsPlanURL"]     = "#";
$aryParts["strPurchaseOrderURL"] = "#";

if ( fncCheckAuthority( DEF_FUNCTION_LO1, $objAuth ) )
{
	// ���ʲ�����Ģɼ���ϲ�ǽ
	$aryParts["strGoodsPlanURL"] = "/list/search/p/search.php?strSessionID=" . $aryData["strSessionID"];
}
if ( fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ) )
{
	// ȯ����P.O��Ģɼ���ϲ�ǽ
	$aryParts["strPurchaseOrderURL"] = "/list/search/po/search.php?strSessionID=" . $aryData["strSessionID"];
}
if ( fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) )
{
	// ���Ѹ����׻�Ģɼ���ϲ�ǽ
	$aryParts["strEstimateURL"] = "/list/search/estimate/search.php?strSessionID=" . $aryData["strSessionID"];
}



$objDB->close();


// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "/list/list/select.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;

?>
