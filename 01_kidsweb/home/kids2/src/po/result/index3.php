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
require_once (LIB_DEBUGFILE);
require_once (LIB_EXCLUSIVEFILE);
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
    $objDB->transactionBegin();
	for($i = 0; $i < count($aryOrderNo); $i++){  // �ºݤ�1��Τߡ�
		$lngorderno = intval(explode("_", $aryOrderNo[$i])[0]);
		$lngrevisionno = fncGetLatestRevisionNo($lngorderno, $objDB);
	    
	    // �������оݤȤʤä�ȯ�����٤˴�Ť�ȯ���������������
		$aryPurchaseOrderDetail = fncGetDeletePurchaseOrderDetail($lngorderno, $lngrevisionno, $objDB);
		if( is_null($aryPurchaseOrderDetail) )
		{
			fncOutputError ( 501, DEF_ERROR, "����оݤ�ȯ��ϴ��˼�ä���Ƥ��ޤ���", TRUE, "", $objDB );
		}
	
		// ������ä��Ȥʤä�ȯ�����٤����
		$aryOrderDetail = fncGetDeleteOrderDetail($lngorderno, $lngrevisionno, $objDB);

        // ȯ������٤���ȯ���No�����
        $lngpurchaseorderno = $aryPurchaseOrderDetail[0]["lngpurchaseorderno"];
        
        // ȯ����å�
        if(!lockOrder($lngpurchaseorderno, $objDB)){
			fncOutputError ( 501, DEF_ERROR, "����оݤ�ȯ���ȯ��񤬽�����Ǥ���", TRUE, "", $objDB );
        }
        
        // �о�ȯ��Υ��ơ����������å�
		if(!isOrderModified($lngorderno, DEF_ORDER_ORDER, $objDB)){
			fncOutputError ( 505, DEF_ERROR, "", TRUE, "", $objDB );
		}
		// ������ä��Ȥʤä�ȯ�����٤˴�Ť�ȯ��ޥ�����ȯ���ơ�������ֲ�ȯ��פ��᤹
		if(!fncCancelOrder($lngorderno, $lngrevisionno, $objDB)){ return false; }

		// ��������ȯ����ֹ桢��ӥ�����ֹ�˳����������ĳ����äȤʤä�ȯ�����٤˳������ʤ�ȯ������٤�
		// ɽ���ѥ����ȥ����ξ���˼���������̤��ˡ��ʲ��λ��ͤǿ�����ȯ������٤򿷵�����Ͽ����
		$aryInsertPurchaseOrderDetail = [];
		$aryDetailNo = array_column($aryOrderDetail, "lngorderdetailno");
		if( is_array($aryPurchaseOrderDetail) )
		{
		    $count = 0;
			for($j = 0; $j < count($aryPurchaseOrderDetail); $j++){
				if($aryPurchaseOrderDetail[$j]["lngorderdetailno"] != $aryDetailNo[0]){
					$aryInsertPurchaseOrderDetail[$count]["lngpurchaseorderno"] = $aryPurchaseOrderDetail[$j]["lngpurchaseorderno"];
					$aryInsertPurchaseOrderDetail[$count]["lngpurchaseorderdetailno"] = $count + 1;
					$aryInsertPurchaseOrderDetail[$count]["lngrevisionno"] = intval($aryPurchaseOrderDetail[$j]["lngrevisionno"]) + 1;
					$aryInsertPurchaseOrderDetail[$count]["lngorderno"] = $aryPurchaseOrderDetail[$j]["lngorderno"];
					$aryInsertPurchaseOrderDetail[$count]["lngorderdetailno"] = $aryPurchaseOrderDetail[$j]["lngorderdetailno"];
					$aryInsertPurchaseOrderDetail[$count]["lngorderrevisionno"] = $aryPurchaseOrderDetail[$j]["lngorderrevisionno"];
					$aryInsertPurchaseOrderDetail[$count]["lngstocksubjectcode"] = $aryPurchaseOrderDetail[$j]["lngstocksubjectcode"];
					$aryInsertPurchaseOrderDetail[$count]["lngstockitemcode"] = $aryPurchaseOrderDetail[$j]["lngstockitemcode"];
					$aryInsertPurchaseOrderDetail[$count]["strstockitemname"] = $aryPurchaseOrderDetail[$j]["strstockitemname"];
					$aryInsertPurchaseOrderDetail[$count]["lngdeliverymethodcode"] = $aryPurchaseOrderDetail[$j]["lngdeliverymethodcode"];
					$aryInsertPurchaseOrderDetail[$count]["strdeliverymethodname"] = $aryPurchaseOrderDetail[$j]["strdeliverymethodname"];
					$aryInsertPurchaseOrderDetail[$count]["curproductprice"] = $aryPurchaseOrderDetail[$j]["curproductprice"];
					$aryInsertPurchaseOrderDetail[$count]["lngproductquantity"] = $aryPurchaseOrderDetail[$j]["lngproductquantity"];
					$aryInsertPurchaseOrderDetail[$count]["lngproductunitcode"] = $aryPurchaseOrderDetail[$j]["lngproductunitcode"];
					$aryInsertPurchaseOrderDetail[$count]["strproductunitname"] = $aryPurchaseOrderDetail[$j]["strproductunitname"];
					$aryInsertPurchaseOrderDetail[$count]["cursubtotalprice"] = $aryPurchaseOrderDetail[$j]["cursubtotalprice"];
					$aryInsertPurchaseOrderDetail[$count]["dtmdeliverydate"] = $aryPurchaseOrderDetail[$j]["dtmdeliverydate"];
					$aryInsertPurchaseOrderDetail[$count]["strnote"] = $aryPurchaseOrderDetail[$j]["strnote"];
					$aryInsertPurchaseOrderDetail[$count]["lngsortkey"] = $count + 1;
					$aryInsertPurchaseOrderDetail[$count]["lngprintcount"] = 0;
					$count++;
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
			$aryOrder_org = fncGetPurchaseOrder2($aryPurchaseOrderDetail[0]["lngpurchaseorderno"], $aryPurchaseOrderDetail[0]["lngrevisionno"], $objDB);
			$aryOrder = $aryOrder_org;
			$aryOrder["lngrevisionno"] = intval($aryOrder["lngrevisionno"]) + 1;
			$aryOrder["lngprintcount"] = 0;
			if(!fncInsertPurchaseOrder($aryOrder, $objDB)) { return false; }
		}
		else
		{
			// �����٤��ʤ�����ȯ���ޥ����������롣
			$aryOrder_org = fncGetPurchaseOrder2($aryCancelOrderDetail["lngpurchaseorderno"], $aryCancelOrderDetail["lngrevisionno"], $objDB);
			$aryOrder = $aryOrder_org;
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

			if(!fncInsertPurchaseOrder($aryOrder, $objDB)) { return false; }
			// �����ô�λ����ɽ���Τ���˥�ӥ�����ֹ������
			$aryOrder["lngrevisionno"] = $orgRevision;
			
		}
	

		if(count($aryInsertPurchaseOrderDetail) > 0){
			$aryHtml[] = "<p class=\"caption\">�ʲ���ȯ��γ����ä�ȼ����������ȯ���������ޤ�����</p>";
			$aryHtml[] = fncCancelPurchaseOrderHtml($aryOrder, $aryCancelOrderDetail, $aryData["strSessionID"]);
		}
		else
		{
			// �����٤��ʤ����
			$aryHtml[] = "<p class=\"caption\">�ʲ���ȯ��γ����ä�ȼ����������ȯ���������ޤ���</p>";
			$aryHtml[] = fncDeletePurchaseOrderHtml($aryOrder_org, $aryPurchaseOrderDetail, $aryData["strSessionID"]);
			
		}
	}
	// $objDB->transactionRollback();
	$objDB->transactionCommit();

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
	$lngrevisionno = fncGetLatestRevisionNo($lngorderno, $objDB);

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