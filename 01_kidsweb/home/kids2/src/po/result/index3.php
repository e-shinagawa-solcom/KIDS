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
	
		// �������оݤȤʤä�ȯ�����٤˴�Ť�ȯ���������������
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
		if( is_array($aryPurchaseOrderDetail) )
		{
			for($j = 0; $j < count($aryPurchaseOrderDetail); $j++){
				if($aryPurchaseOrderDetail[$j]["lngorderdetailno"] != $aryDetailNo[0]){
					$aryInsertPurchaseOrderDetail[$j]["lngpurchaseorderno"] = $aryPurchaseOrderDetail[$j]["lngpurchaseorderno"];
					$aryInsertPurchaseOrderDetail[$j]["lngpurchaseorderdetailno"] = $j + 1;
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
					$aryInsertPurchaseOrderDetail[$j]["lngprintcount"] = 0;
				}
				else{
					$aryCancelOrderDetail["lngpurchaseorderno"] = $aryPurchaseOrderDetail[$j]["lngpurchaseorderno"];
					$aryCancelOrderDetail["lngpurchaseorderdetailno"] = $aryPurchaseOrderDetail[$j]["lngpurchaseorderdetailno"];
					$aryCancelOrderDetail["lngrevisionno"] = intval($aryPurchaseOrderDetail[$j]["lngrevisionno"]);
					$aryCancelOrderDetail["lngorderno"] = $aryPurchaseOrderDetail[$j]["lngorderno"];
					$aryCancelOrderDetail["lngorderdetailno"] = $aryPurchaseOrderDetail[$j]["lngorderdetailno"];
					$aryCancelOrderDetail["lngorderrevisionno"] = $aryPurchaseOrderDetail[$j]["lngorderrevisionno"];
					$aryCancelOrderDetail["lngstocksubjectcode"] = $aryPurchaseOrderDetail[$j]["lngstocksubjectcode"];
					$aryCancelOrderDetail["lngstockitemcode"] = $aryPurchaseOrderDetail[$j]["lngstockitemcode"];
					$aryCancelOrderDetail["strstockitemname"] = $aryPurchaseOrderDetail[$j]["strstockitemname"];
					$aryCancelOrderDetail["lngdeliverymethodcode"] = $aryPurchaseOrderDetail[$j]["lngdeliverymethodcode"];
					$aryCancelOrderDetail["strdeliverymethodname"] = $aryPurchaseOrderDetail[$j]["strdeliverymethodname"];
					$aryCancelOrderDetail["curproductprice"] = $aryPurchaseOrderDetail[$j]["curproductprice"];
					$aryCancelOrderDetail["lngproductquantity"] = $aryPurchaseOrderDetail[$j]["lngproductquantity"];
					$aryCancelOrderDetail["lngproductunitcode"] = $aryPurchaseOrderDetail[$j]["lngproductunitcode"];
					$aryCancelOrderDetail["strproductunitname"] = $aryPurchaseOrderDetail[$j]["strproductunitname"];
					$aryCancelOrderDetail["cursubtotalprice"] = $aryPurchaseOrderDetail[$j]["cursubtotalprice"];
					$aryCancelOrderDetail["dtmdeliverydate"] = $aryPurchaseOrderDetail[$j]["dtmdeliverydate"];
					$aryCancelOrderDetail["strnote"] = $aryPurchaseOrderDetail[$j]["strnote"];
					$aryCancelOrderDetail["lngsortkey"] = $aryPurchaseOrderDetail[$j]["lngsortkey"];
				}
			}
		}
		else
		{
			$aryCancelOrderDetail["lngpurchaseorderno"] = $aryPurchaseOrderDetail["lngpurchaseorderno"];
			$aryCancelOrderDetail["lngpurchaseorderdetailno"] = $aryPurchaseOrderDetail["lngpurchaseorderdetailno"];
			$aryCancelOrderDetail["lngrevisionno"] = intval($aryPurchaseOrderDetail["lngrevisionno"]);
			$aryCancelOrderDetail["lngorderno"] = $aryPurchaseOrderDetail["lngorderno"];
			$aryCancelOrderDetail["lngorderdetailno"] = $aryPurchaseOrderDetail["lngorderdetailno"];
			$aryCancelOrderDetail["lngorderrevisionno"] = $aryPurchaseOrderDetail["lngorderrevisionno"];
			$aryCancelOrderDetail["lngstocksubjectcode"] = $aryPurchaseOrderDetail["lngstocksubjectcode"];
			$aryCancelOrderDetail["lngstockitemcode"] = $aryPurchaseOrderDetail["lngstockitemcode"];
			$aryCancelOrderDetail["strstockitemname"] = $aryPurchaseOrderDetail["strstockitemname"];
			$aryCancelOrderDetail["lngdeliverymethodcode"] = $aryPurchaseOrderDetail["lngdeliverymethodcode"];
			$aryCancelOrderDetail["strdeliverymethodname"] = $aryPurchaseOrderDetail["strdeliverymethodname"];
			$aryCancelOrderDetail["curproductprice"] = $aryPurchaseOrderDetail["curproductprice"];
			$aryCancelOrderDetail["lngproductquantity"] = $aryPurchaseOrderDetail["lngproductquantity"];
			$aryCancelOrderDetail["lngproductunitcode"] = $aryPurchaseOrderDetail["lngproductunitcode"];
			$aryCancelOrderDetail["strproductunitname"] = $aryPurchaseOrderDetail["strproductunitname"];
			$aryCancelOrderDetail["cursubtotalprice"] = $aryPurchaseOrderDetail["cursubtotalprice"];
			$aryCancelOrderDetail["dtmdeliverydate"] = $aryPurchaseOrderDetail["dtmdeliverydate"];
			$aryCancelOrderDetail["strnote"] = $aryPurchaseOrderDetail["strnote"];
			$aryCancelOrderDetail["lngsortkey"] = $aryPurchaseOrderDetail[$j]["lngsortkey"];
		}
		if(count($aryInsertPurchaseOrderDetail) > 0){
			// �����٤�������ϡ�ȯ������٤򿷵�����Ͽ����
			foreach($aryInsertPurchaseOrderDetail as $row){
				if(!fncInsertPurchaseOrderDetail($row, $objDB)) { return false; }
			}
	
			// ȯ���ޥ����򹹿�����
			$aryOrder = fncGetPurchaseOrder2($aryPurchaseOrderDetail[0]["lngpurchaseorderno"], $aryPurchaseOrderDetail[0]["lngrevisionno"], $objDB);
			$aryOrder["lngrevisionno"] = intval($aryPurchaseOrderDetail[0]["lngrevisionno"]) + 1;
			$aryOrder["lngprintcount"] = 0;
			if(!fncInsertPurchaseOrder($aryOrder, $objDB)) { return false; }
		}
		else
		{
			// �����٤��ʤ�����ȯ���ޥ����������롣
			$aryOrder = fncGetPurchaseOrder2($aryCancelOrderDetail["lngpurchaseorderno"], $aryCancelOrderDetail["lngrevisionno"], $objDB);
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

			if(!fncInsertPurchaseOrder($aryOrder, $objDB)) { return false; }
			
		}
	
		// $objDB->transactionRollback();
		$objDB->transactionCommit();

		if(count($aryInsertPurchaseOrderDetail) > 0){
			$aryHtml[] = "<p class=\"caption\">�ʲ���ȯ��γ����ä�ȼ����������ȯ���������ޤ�����</p>";
			$aryHtml[] = fncCancelPurchaseOrderHtml($aryOrder, $aryCancelOrderDetail);
		}
		else
		{
			// �����٤��ʤ����
			$aryHtml[] = "<p class=\"caption\">�ʲ���ȯ��γ����ä�ȼ����������ȯ���������ޤ���</p>";
			$aryHtml[] = fncCancelPurchaseOrderHtml($aryOrder, $aryCancelOrderDetail, true);
			
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