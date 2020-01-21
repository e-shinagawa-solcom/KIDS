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
require (SRC_ROOT . "po/cmn/lib_pos1.php");
require (SRC_ROOT . "list/cmn/lib_lo.php");
require (SRC_ROOT . "po/cmn/lib_por.php");
require (SRC_ROOT . "po/cmn/column.php");
require_once (LIB_DEBUGFILE);
require (LIB_EXCLUSIVEFILE);

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


if($_POST){
//echo "delete key:" . $lngpurchaseorderno . "-" . $lngrevisionno . "<br>";
	
	// ȯ���˴�Ť�ȯ���ޥ�������ˡ�Ǽ�ʺѡפ�¸�ߤ������ϥ��顼
	//if(
	$objDB->transactionBegin();

	// ȯ���ޥ�����å�
    if( !lockOrder($lngpurchaseorderno, $objDB) )
    {
        fncOutputError(9051, DEF_ERROR, "ȯ���Υ�å������˼��Ԥ��ޤ�����", true, "", $objDB);
        return false;
    }
    
    // ȯ��񹹿�̵ͭ�����å�
    if( isPurchaseOrderModified($lngpurchaseorderno, $lngrevisionno, $objDB) )
    {
        fncOutputError(9051, DEF_ERROR, "¾�桼������ȯ���򹹿��ޤ��Ϻ�����Ƥ��ޤ���", true, "", $objDB);
        return false;
    }
	
    // ȯ��ǡ�����å�&���ơ����������å�
    if( !fncCanDeletePO($lngpurchaseorderno, $lngrevisionno, $objDB))
    {
        fncOutputError(9051, DEF_ERROR, "Ǽ�ʺѤޤ�������Ѥ����٤����뤿�����Ǥ��ޤ���", true, "", $objDB);
    }
    
	$aryCancelOrderDetail = fncGetDeletePurchaseOrderDetailByPo($lngpurchaseorderno, $lngrevisionno, $objDB);
	$aryPurchaseOrder = fncGetPurchaseOrder2($lngpurchaseorderno, $lngrevisionno, $objDB);
	$aryOrder = $aryPurchaseOrder;
    // ����оݤȤʤä�ȯ���˴�Ť�ȯ���ޥ����Υ��ơ�������ֲ�����פ��ѹ�
	if(!fncCancelOrderByPo($lngpurchaseorderno, $lngrevisionno, $objDB)){ return false; }
    
	// ����оݤ�ȯ���ޥ����˥�ӥ����-1�Υǡ������ɲ�
	//$aryOrder = fncGetPurchaseOrder2($lngpurchaseorderno, $alngrevisionno, $objDB);
	//$orgRevision = $aryOrder["lngrevisionno"];
	//$aryOrder["lngpurchaseorderno"] = $lngpurchaseorderno;
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

	//$objDB->transactionRollback();
	$objDB->transactionCommit();

	$aryHtml[] = "<p class=\"caption\">������ȯ���������ޤ���</p>";
	$aryHtml[] = fncDeletePurchaseOrderHtml($aryPurchaseOrder, $aryCancelOrderDetail, $aryData["strSessionID"]);

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

//echo "start key:" . $lngpurchaseorderno . "-" . $lngrevisionno . "<br>";

// ȯ�������ǧ����
// ȯ���ޥ��������
$aryResult = fncGetPurchaseOrderEdit($lngpurchaseorderno, $lngrevisionno, $objDB);

// �����ǡ�����Ĵ��
$aryNewResult = fncSetPurchaseHeadTabelData($aryResult[0]);

////////// ���ٹԤμ��� ////////////////////
// ȯ������٤����

$strQuery = fncGetPurchaseOrderDetailSQL($lngpurchaseorderno, $lngrevisionno);
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryDetailResult[$i] = $objDB->fetchArray( $lngResultID, $i );
	}
}
$objDB->freeResult( $lngResultID );
if(!is_array($aryDetailResult)){
    $aryDetailResult[] = $aryDetailResult;
}
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

$aryNewResult["strResult"] = implode ("\n", $aryDetailTable );


$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strMode"] = "detail";
$aryNewResult["strSessionID"] = $aryData["strSessionID"];
// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "po/result2/parts3.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryNewResult );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();
return true;// �ƥ�ץ졼���ɤ߹���


?>