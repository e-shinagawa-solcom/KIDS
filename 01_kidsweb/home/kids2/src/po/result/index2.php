<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  �ܺ�
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
*         ������ȯ���ֹ�ǡ����ξܺ�ɽ������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "po/cmn/lib_pos.php");
require (SRC_ROOT . "list/cmn/lib_lo.php");
require (SRC_ROOT . "po/cmn/lib_pos1.php");
require (SRC_ROOT . "po/cmn/column.php");

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
// 2004.04.19 suzukaze update start
if ( !isset($aryData["lngOrderNo"]) )
{
	fncOutputError ( 9061, DEF_ERROR, "�ǡ����۾�Ǥ���", TRUE, "", $objDB );
}
// 2004.04.19 suzukaze update end

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngOrderNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ������桼���������ɤμ���
$lngInputUserCode = $objAuth->UserCode;


// ���³�ǧ
// 502 ȯ�������ȯ������
if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 504 ȯ������ʾܺ�ɽ����
if ( !fncCheckAuthority( DEF_FUNCTION_PO4, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

//�ܺٲ��̤�ɽ��

$lngOrderNo = $aryData["lngOrderNo"];

// ����ȯ���ֹ��ȯ��ǡ���������SQLʸ�κ���
//$strQuery = fncGetPurchaseHeadNoToInfoSQL ( $lngOrderNo );
$aryResult = fncGetPurchaseHeadNoToInfo ( $lngOrderNo, $objDB );

// �ܺ٥ǡ����μ���
// list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

// if ( $lngResultNum )
// {
// 	if ( $lngResultNum == 1 )
// 	{
// 		$aryResult = $objDB->fetchArray( $lngResultID, 0 );
// 	}
// 	else
// 	{
// 		fncOutputError( 503, DEF_ERROR, "�����ǡ����μ����˼��Ԥ��ޤ���", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
// 	}
// }
// else
// {
// 	fncOutputError( 503, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
// }

// $objDB->freeResult( $lngResultID );

// �����ǡ�����Ĵ��
$aryNewResult = fncSetPurchaseHeadTabelData ( $aryResult );

// ���������
$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
if ( $aryData["lngLanguageCode"] == 0 )
{
	$aryTytle = $aryTableTytleEng;
}
else
{
	$aryTytle = $aryTableTytle;
}

// �����̾������
$aryHeadColumnNames = fncSetPurchaseTabelName ( $aryTableViewHead, $aryTytle );
// �����̾������
$aryDetailColumnNames = fncSetPurchaseTabelName ( $aryTableViewDetail, $aryTytle );

////////// ���ٹԤμ��� ////////////////////

// ����ȯ���ֹ��ȯ�����٥ǡ���������SQLʸ�κ���
// $strQuery = fncGetPurchaseDetailNoToInfoSQL ( $lngOrderNo );
$aryDetailResult[] = fncGetPurchaseDetailNoToInfo ( $lngOrderNo, $objDB );

// ���٥ǡ����μ���
// list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

// if ( $lngResultNum )
// {
// 	for ( $i = 0; $i < $lngResultNum; $i++ )
// 	{
// 		$aryDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
// 	}
// }
// else
// {
// 	fncOutputError( 503, DEF_WARNING, "ȯ���ֹ���Ф������پ��󤬸��Ĥ���ޤ���", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
// }

// $objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($aryDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetPurchaseDetailTabelData ( $aryDetailResult[$i], $aryNewResult );

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "po/result/parts_detail.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDetailColumnNames );
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML����
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );

// Ģɼ�����б�
// ɽ���оݤ�����ǡ�����������ǡ����ξ��ϥץ�ӥ塼�ܥ����ɽ�����ʤ�
// �ޤ�Ģɼ���ϸ��¤���äƤʤ�����ץ�ӥ塼�ܥ����ɽ�����ʤ�
if ( $aryResult["lngrevisionno"] >= 0 and $aryResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ))
{
	// ɽ���оݤ�����ǧ�ǡ�����������åǡ����ξ��ϥץ�ӥ塼�ܥ����ɽ�����ʤ�
	// if ( fncCheckApprovalProductOrder( $aryData["lngOrderNo"], $objDB ) )
	// {
	// 	$aryNewResult["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $aryData["lngOrderNo"] . "&bytCopyFlag=TRUE";

		$aryNewResult["listview"] = 'visible';
	// }
	// else
	// {
		// $aryNewResult["listview"] = 'hidden';
	// }
}
else
{
	$aryNewResult["listview"] = 'hidden';
}




$aryNewResult["strAction"] = "index2.php";
$aryNewResult["strMode"] = "detail";

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "po/result/parts2.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>