<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ���
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
*         ������ȯ���ֹ�ǡ����κ������
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
if ( !isset($aryData["lngOrderNo"]) )
{
	fncOutputError ( 9061, DEF_ERROR, "�ǡ����۾�Ǥ���", TRUE, "", $objDB );
}

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
// 506 ȯ�������ȯ������
if ( !fncCheckAuthority( DEF_FUNCTION_PO6, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

$aryOrderNo = explode(",", $aryData["lngOrderNo"]);

if($_POST){
	for($i = 0; $i < count($aryOrderNo); $i++){
		$lngorderno = intval(explode("_", $aryOrderNo[$i])[0]);
		$lngrevisionno = intval(explode("_", $aryOrderNo[$i])[1]);
	
		// �������оݤȤʤä�ȯ�����٤˴�Ť�ȯ������٤����
		$aryPurchaseOrderDetail = fncGetDeletePurchaseOrderDetail($lngorderno, $lngrevisionno, $objDB);
	
		$objDB->transactionBegin();
		// ������ä��Ȥʤä�ȯ�����٤˴�Ť�ȯ��ޥ�����ȯ���ơ�������ֲ�ȯ��פ��᤹
		if(!fncCancelOrder($lngorderno, $lngrevisionno, $objDB)){ return false; }
	
		// ������ä��Ȥʤä�ȯ�����٤����
		$aryOrderDetail = fncGetDeleteOrderDetail($lngorderno, $lngrevisionno, $objDB);
	
		// ��������ȯ����ֹ桢��ӥ�����ֹ�˳����������ĳ����äȤʤä�ȯ�����٤˳������ʤ�ȯ������٤�
		// ɽ���ѥ����ȥ����ξ���˼���������̤��ˡ��ʲ��λ��ͤǿ�����ȯ������٤򿷵�����Ͽ����
		$aryInsertPurchaseOrderDetail = [];
		$aryDetailNo = array_column($aryOrderDetail, "lngorderdetailno");
		for($j = 0; $j < count($aryPurchaseOrderDetail); $j++){
			if(!in_array($aryOrderDetail[$j]["lngorderdetailno"], $aryDetailNo, true)){
				$aryInsertPurchaseOrderDetail[$j]["lngpurchaseorderno"] = $aryPurchaseOrderDetail[$j]["lngpurchaseorderno"];
				$aryInsertPurchaseOrderDetail[$j]["lngpurchaseorderdetailno"] = $aryPurchaseOrderDetail[$j]["lngpurchaseorderdetailno"];
				$aryInsertPurchaseOrderDetail[$j]["lngrevisionno"] = intval($aryPurchaseOrderDetail[$j]["lngrevisionno"]) + 1;
				$aryInsertPurchaseOrderDetail[$j]["lngorderno"] = $aryPurchaseOrderDetail[$j]["lngorderno"];
				$aryInsertPurchaseOrderDetail[$j]["lngorderdetailno"] = $aryPurchaseOrderDetail[$j]["lngorderdetailno"];
				$aryInsertPurchaseOrderDetail[$j]["lngorderrevisionno"] = $aryPurchaseOrderDetail[$j]["lngorderrevisionno"];
				$aryInsertPurchaseOrderDetail[$j]["lngstocksubjectcode"] = $aryPurchaseOrderDetail[$j]["lngstocksubjectcode"];
				$aryInsertPurchaseOrderDetail[$j]["lngstockitemcode"] = $aryPurchaseOrderDetail[$j]["lngstockitemcode"];
				$aryInsertPurchaseOrderDetail[$j]["strstockitemname"] = $aryPurchaseOrderDetail[$j]["strstockitemname"];
				$aryInsertPurchaseOrderDetail[$j]["lngdeliverymethodcode"] = $aryPurchaseOrderDetail[$j]["lngdeliverymethodcode"];
				$aryInsertPurchaseOrderDetail[$j]["strdeliverymethodname"] = $aryPurchaseOrderDetail[$j]["strdeliverymethodname"];
				$aryInsertPurchaseOrderDetail[$j]["curproductprice"] = $aryPurchaseOrderDetail[$j]["curproductprice"];
				$aryInsertPurchaseOrderDetail[$j]["lngproductquantity"] = $aryPurchaseOrderDetail[$j]["lngproductquantity"];
				$aryInsertPurchaseOrderDetail[$j]["lngproductunitcode"] = $aryPurchaseOrderDetail[$j]["lngproductunitcode"];
				$aryInsertPurchaseOrderDetail[$j]["strproductunitname"] = $aryPurchaseOrderDetail[$j]["strproductunitname"];
				$aryInsertPurchaseOrderDetail[$j]["cursubtotalprice"] = $aryPurchaseOrderDetail[$j]["cursubtotalprice"];
				$aryInsertPurchaseOrderDetail[$j]["dtmdeliverydate"] = $aryPurchaseOrderDetail[$j]["dtmdeliverydate"];
				$aryInsertPurchaseOrderDetail[$j]["strnote"] = $aryPurchaseOrderDetail[$j]["strnote"];
				$aryInsertPurchaseOrderDetail[$j]["lngsortkey"] = $j + 1;
			}
		}
	
		if($aryInsertPurchaseOrderDetail){
			// ȯ������٤򿷵�����Ͽ����
			foreach($aryInsertPurchaseOrderDetail as $row){
				if(!fncInsertPurchaseOrderDetail($row, $objDB)) { return false; }
			}
	
			// ȯ���ޥ����򹹿�����
			$aryOrder = fncGetPurchaseOrder2($aryPurchaseOrderDetail[0]["lngpurchaseorderno"], $aryPurchaseOrderDetail[0]["lngrevisionno"], $objDB);
			$aryOrder["lngrevisionno"] = intval($aryPurchaseOrderDetail[0]["lngrevisionno"]) + 1;
			$aryOrder["lngprintcount"] = 0;
			if(!fncInsertPurchaseOrder($aryOrder, $objDB)) { return false; }
		}
	
		// $objDB->transactionRollback();
		$objDB->transactionCommit();

		if($aryInsertPurchaseOrderDetail){
			$aryHtml[] = "<p class=\"caption\">ȯ������ä�ȼ�����ʲ���ȯ���������ޤ�����</p>";
			$aryHtml[] = fncCancelPurchaseOrderHtml($aryOrder, $aryInsertPurchaseOrderDetail);
		}
	}

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

for($i = 0; $i < count($aryOrderNo); $i++){
	$lngorderno = intval(explode("_", $aryOrderNo[$i])[0]);
	$lngrevisionno = intval(explode("_", $aryOrderNo[$i])[1]);

	$aryOrder[] = fncGetOrder($lngorderno, $lngrevisionno, $objDB);
}

$aryResult["strResult"] = fncCancelOrderHtml($aryOrder);
$aryResult["lngOrderNo"] = $aryData["lngOrderNo"];
$aryResult["strSessionID"] = $aryData["strSessionID"];
//$aryResult["strResult"] = implode("\n", $aryHtml);
//$aryResult["strOrderCode"] = implode(",", $aryOrderCode);
//$aryResult["lngRevisionNo"] = implode(",", $aryRevisionNo);


// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "po/result/parts3.tmpl" );

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