<?php

// ----------------------------------------------------------------------------
/**
*       ȯ������  �ܺ�
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       ��������
*         ������ȯ����ֹ�ǡ����ξܺ�ɽ������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "po/cmn/lib_pos1.php");
require (SRC_ROOT . "list/cmn/lib_lo.php");
require (SRC_ROOT . "po/cmn/lib_por.php");
require (SRC_ROOT . "po/cmn/column.php");

require (LIB_DEBUGFILE);

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// GET�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ( $_GET )
{
	$aryData = $_GET;
}
else if ( $_POST )
{
	$aryData = $_POST;
}

if ( !isset($aryData["lngPurchaseOrderNo"]) )
{
	fncOutputError ( 9061, DEF_ERROR, "�ǡ����۾�Ǥ���", TRUE, "", $objDB );
}

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngPurchaseOrderNo"]	  = "null:number(0,10)";
$aryCheck["lngRevisionNo"]	  = "null:number(0,10)";

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ������桼���������ɤμ���
$lngInputUserCode = $objAuth->UserCode;


// ���³�ǧ
// 502 ȯ�������ȯ������
if ( !fncCheckAuthority( DEF_FUNCTION_PO10, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 504 ȯ������ʾܺ�ɽ����
if ( !fncCheckAuthority( DEF_FUNCTION_PO12, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

//�ܺٲ��̤�ɽ��

$lngPurchaseOrderNo = $aryData["lngPurchaseOrderNo"];
$lngRevisionNo = $aryData["lngRevisionNo"];

// ����ȯ����ֹ��ȯ��ǡ���������SQLʸ�κ���
$aryResult = fncGetPurchaseOrderEdit($lngPurchaseOrderNo, $lngRevisionNo, $objDB);

// �����ǡ�����Ĵ��
$aryNewResult = fncSetPurchaseHeadTabelData($aryResult[0]);

////////// ���ٹԤμ��� ////////////////////
// ȯ������٤����
$strQuery = fncGetPurchaseOrderDetailSQL($lngPurchaseOrderNo, $lngRevisionNo);
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryDetailResult[$i] = $objDB->fetchArray( $lngResultID, $i );
	}
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($aryDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetPurchaseDetailTabelData ( $aryDetailResult[$i], $aryNewResult );

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "po/result2/parts_detail.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML����
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );

// Ģɼ�����б�
// ɽ���оݤ�����ǡ�����������ǡ����ξ��ϥץ�ӥ塼�ܥ����ɽ�����ʤ�
// �ޤ�Ģɼ���ϸ��¤���äƤʤ�����ץ�ӥ塼�ܥ����ɽ�����ʤ�
if ( $aryResult["lngrevisionno"] >= 0 and fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ))
{
    $aryNewResult["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $aryData["lngPurchaseOrderNo"] . "&bytCopyFlag=TRUE";

	$aryNewResult["listview"] = 'visible';
}
else
{
	$aryNewResult["listview"] = 'hidden';
}




$aryNewResult["strAction"] = "index2.php";
$aryNewResult["strMode"] = "detail";

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "po/result2/parts2.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryNewResult );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>