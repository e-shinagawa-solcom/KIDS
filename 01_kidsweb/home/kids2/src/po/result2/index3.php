<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ȯ�����
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
*         ������ȯ���ǡ����κ������
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
require (SRC_ROOT . "po/cmn/lib_pos1.php");
require (SRC_ROOT . "po/cmn/column.php");
require_once (LIB_DEBUGFILE);

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
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ������桼���������ɤμ���
$lngInputUserCode = $objAuth->UserCode;


// ���³�ǧ
// 510 ȯ�������ȯ��񸡺���
if ( !fncCheckAuthority( DEF_FUNCTION_PO10, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 513 ȯ�������ȯ�������
if ( !fncCheckAuthority( DEF_FUNCTION_PO13, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

//$aryPurchaseOrderNo = explode(",", $aryData["lngPurchaseOrderNo"]);

$lngpurchaseorderno = $aryData["lngPurchaseOrderNo"];
$lngrevisionno = $aryData["lngRevisionNo"];

// echo $lngorderno . "-" . $lngrevisionno . "<br>";

if($_POST){
	
	// ȯ���˴�Ť�ȯ���ޥ�������ˡ�Ǽ�ʺѡפ�¸�ߤ������ϥ��顼
	
	
	$objDB->transactionBegin();
    // ����оݤȤʤä�ȯ���˴�Ť�ȯ���ޥ����Υ��ơ�������ֲ�����פ��ѹ�
	//if(!fncCancelOrder($lngorderno, $lngrevisionno, $objDB)){ return false; }

	// ����оݤ�ȯ���ޥ����˥�ӥ����-1�Υǡ������ɲ�
	//$aryOrder = fncGetPurchaseOrder2($lngpurchaseorderno, $alngrevisionno, $objDB);
	$orgRevision = $aryOrder["lngrevisionno"];
	$aryOrder["lngrevisionno"] = -1;
	$aryOrder["lngcustomercode"] = null;
	$aryOrder["strcustomername"] = null;
	$aryOrder["strcustomercompanyaddreess"] = null;
	$aryOrder["strcustomercompanytel"] = null;
	$aryOrder["strcustomercompanyfax"] = null;
	$aryOrder["strproductname"] = null;
	$aryOrder["strproductenglishname"] = null;
	$aryOrder["dtmexpirationdate"] = null;
	$aryOrder["lngmonetaryunitcode"] = null;
	$aryOrder["strmonetaryunitname"] = null;
	$aryOrder["strmonetaryunitsign"] = null;
	$aryOrder["lngmonetaryratecode"] = null;
	$aryOrder["strmonetaryratename"] = null;
	$aryOrder["lngpayconditioncode"] = null;
	$aryOrder["strpayconditionname"] = null;
	$aryOrder["lnggroupcode"] = null;
	$aryOrder["strgroupname"] = null;
	$aryOrder["txtsignaturefilename"] = null;
	$aryOrder["lngusercode"] = null;
	$aryOrder["strusername"] = null;
	$aryOrder["lngdeliveryplacecode"] = null;
	$aryOrder["strdeliveryplacename"] = null;
	$aryOrder["curtotalprice"] = null;
	$aryOrder["lnginsertusercode"] = $lngInputUserCode;
	$aryOrder["strinsertusername"] = null;
	$aryOrder["strnote"] = null;
	$aryOrder["lngprintcount"] = null;

	if(!fncInsertPurchaseOrder($aryOrder, $objDB, $objAuth)) { return false; }

	// $objDB->transactionRollback();
	$objDB->transactionCommit();

	$aryHtml[] = "<p class=\"caption\">������ȯ���������ޤ���</p>";
	//$aryHtml[] = fncCancelPurchaseOrderHtml($aryOrder, $aryCancelOrderDetail, $aryData["strSessionID"], true);

	if($aryHtml){
		$aryResult["aryPurchaseOrder"] = implode("\n", $aryHtml);
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate("po/finish/remove_parts.tmpl");
		$objTemplate->replace($aryResult);
		$objTemplate->complete();
		echo $objTemplate->strTemplate;
	} else {
		fncOutputError ( 9051, DEF_ERROR, "�����оݤ�ȯ���ޥ���������ޤ���", TRUE, "", $objDB );
		return FALSE;
	}
	
	$objDB->close();
	return true;
}

// ȯ�������ǧ����
// ȯ���ޥ��������
$strQuery = fncGetPurchaseOrderSQL($lngpurchaseorderno, $lngrevisionno );
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$aryPurchaseOrder = $objDB->fetchArray( $lngResultID, 0 );
}
$objDB->freeResult( $lngResultID );


// ȯ������٤����
$strQuery = fncGetPurchaseOrderDetailSQL($lngpurchaseorderno, $lngrevisionno );
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryPurchaseOrderDetail[$i] = $objDB->fetchArray( $lngResultID, $i );
	}
}
$objDB->freeResult( $lngResultID );


$aryResult["strResult"] = fncCancelPurchaseOrderHtml2($aryPurchaseOrder, $aryPurchaseOrderDetail );
$aryResult["lngOrderNo"] = $aryData["lngOrderNo"];
$aryResult["strSessionID"] = $aryData["strSessionID"];
//$aryResult["strResult"] = implode("\n", $aryHtml);
//$aryResult["strOrderCode"] = implode(",", $aryOrderCode);
//$aryResult["lngRevisionNo"] = implode(",", $aryRevisionNo);


// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "po/result2/parts3.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryResult );
// $objTemplate->replace( $aryOrderResult );
// $objTemplate->replace( $aryDetailResult );
//$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>