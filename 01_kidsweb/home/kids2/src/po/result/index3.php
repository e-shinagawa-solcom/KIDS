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
	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

	$aryOrderCode = explode(",", $aryData["strOrderCode"]);
	$aryRevisionNo = explode(",", $aryData["lngRevisionNo"]);

	for($i = 0; $i < count($aryRevisionNo); $i++){
		//$strOrderCode = explode("_", $aryOrderCode[$i])[0];
		$lngRevisionNo = explode("_", $aryRevisionNo[$i])[1];
		// �������оݤȤʤä�ȯ�����٤�ɳ�Ť�ȯ���ޥ�����ȯ����ֹ桢��ӥ�����ֹ��������롣
		$aryPurchaseOrder = fncGetPurchaseOrder($aryOrderNo[$i], $lngRevisionNo, $objDB);

		// list ( $lngResultID, $lngResultNum ) = fncQuery( $strSql, $objDB );
		// if ( $lngResultNum == 1 )
		// {
		// 	$aryPurchaseOrder = $objDB->fetchArray( $lngResultID, 0 );
		// }
		// $objDB->freeResult( $lngResultID );

		// ȯ��ޥ�����ֲ�����פ��ѹ�����
		if(!fncGetCancelOrder($aryOrderNo[$i], $lngRevisionNo, $objDB)){
			fncOutputError ( 9051, DEF_ERROR, "�ǡ����١����ι����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
			return FALSE;
		}

		if($aryPurchaseOrder){
			// ȯ������٤��������
			$strSql = fncGetPurchaseOrderDetailSQL($aryPurchaseOrder["lngpurchaseorderno"], $aryPurchaseOrder["lngrevisionno"]);

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strSql, $objDB );
			if ( $lngResultNum == 1 )
			{
				$aryPurchaseOrderDetail = $objDB->fetchArray( $lngResultID, 0 );
			}
			else if ( !$lngResultID = $objDB->execute( $strSql ) )
			{
				fncOutputError ( 9051, DEF_ERROR, "�ǡ����١����ι����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
				return FALSE;
			}
			$objDB->freeResult( $lngResultID );

			// ȯ������٤���Ͽ
			$aryPurchaseOrderDetail["lngpurchaseorderno"] = $aryPurchaseOrder["lngpurchaseorderno"];
			$aryPurchaseOrderDetail["lngrevisionno"] = intval($aryPurchaseOrder["lngrevisionno"]) + 1;
			$aryPurchaseOrderDetail["lngsortkey"] = intval($aryPurchaseOrderDetail["lngsortkey"]) + 1;

			$strSql = fncInsertPurchaseOrderDetailSQL($aryPurchaseOrderDetail);

			if ( !$lngResultID = $objDB->execute( $strSql ) )
			{
				fncOutputError ( 9051, DEF_ERROR, "ȯ������٤ؤ���Ͽ�����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
				return FALSE;
			}
			$objDB->freeResult( $lngResultID );

			// ȯ���ޥ����򿷵���Ͽ����

		}





		$objDB->transactionRollback();

	}

}

for($i = 0; $i < count($aryOrderNo); $i++){
	// ����оݤ�ȯ��NO��ȯ���������
	$strPurchaseOrder = fncGetPurchaseHeadNoToInfo ( $aryOrderNo[$i], $objDB );

	// ����ȯ���ֹ��ȯ������٥ǡ���������SQLʸ�κ���
	$strPurchaseOrderDatail = fncGetPurchaseDetailNoToInfo ( $aryOrderNo[$i], $objDB );

	$aryHtml[] = fncDeletePurchaseOrderHtml($strPurchaseOrder, $strPurchaseOrderDatail);
	$aryOrderCode[] = $strPurchaseOrder["strordercode"];
	$aryRevisionNo[] = $strPurchaseOrder["lngrevisionno"];

}

$aryResult["strResult"] = implode("\n", $aryHtml);
$aryResult["strOrderCode"] = implode(",", $aryOrderCode);
$aryResult["lngRevisionNo"] = implode(",", $aryRevisionNo);


// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "po/result/parts3.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryResult );
// $objTemplate->replace( $aryOrderResult );
// $objTemplate->replace( $aryDetailResult );
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>