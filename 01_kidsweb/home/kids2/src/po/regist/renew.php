<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ��������
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
*         ����������Ͽ���̤�ɽ��
*         �����ϥ��顼�����å�
*         ����Ͽ�ܥ��󲡲��塢��Ͽ��ǧ���̤�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



	// �ɤ߹���
	include('conf.inc');
	require(LIB_FILE);
	require(SRC_ROOT."po/cmn/lib_po.php");
	// require(SRC_ROOT."po/cmn/lib_pop.php");
	// require(SRC_ROOT."po/cmn/lib_pos1.php");
	require(SRC_ROOT."po/cmn/lib_por.php");
	require(SRC_ROOT."po/cmn/column.php");
	require_once (LIB_DEBUGFILE);


	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	
	$aryData["strSessionID"]        = $_REQUEST["strSessionID"];
	$aryData["lngPurchaseOrderNo"]  = $_REQUEST["lngPurchaseOrderNo"];
	$aryData["lngRevisionNo"]       = $_REQUEST["lngRevisionNo"];
	$aryData["dtmExpirationDate"]   = $_REQUEST["dtmExpirationDate"];
	$aryData["lngPayConditionCode"] = $_REQUEST["lngPayConditionCode"];
//	$aryData["strPayConditionName"] = $_REQUEST["strPayConditionName"];
	$aryData["strPayConditionName"] = mb_convert_encoding($_REQUEST["strPayConditionName"], "EUC-JP", "auto");
	$aryData["lngLocationCode"]     = $_REQUEST["lngLocationCode"];
//	$aryData["strLocationName"]     = $_REQUEST["strLocationName"];
	$aryData["strLocationName"]     = mb_convert_encoding($_REQUEST["strLocationName"], "EUC-JP", "auto");
//	$aryData["strNote"]             = $_REQUEST["strNote"];
	$aryData["strNote"]             = mb_convert_encoding($_REQUEST["strNote"], "EUC-JP", "auto");
	$aryData["strOrderCode"]        = $_REQUEST["strOrderCode"];
	// $aryData["strProductCode"]      = $_REQUEST["strProductCode"];
	// $aryData["strProductName"]      = $_REQUEST["strProductName"];
	// $aryData["strCustomerCode"]     = $_REQUEST["strCustomerCode"];
	// $aryData["strCustomerName"]     = $_REQUEST["strCustomerName"];
	$aryData["aryDetail"]           = $_REQUEST["aryDetail"];
	
	$objDB->open("", "", "", "");
	
	// ʸ��������å�
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );


	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	
	$lngUserCode = $objAuth->UserCode;
	
	
	// 500	ȯ�����
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	
	
	// 512 ȯ�������ȯ�������
	if( !fncCheckAuthority( DEF_FUNCTION_PO12, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
fncDebug("kids2.log", "pass-1", __FILE__, __LINE__, "a" );

	// check
	if( $_POST["strMode"] == "check" || $_POST["strMode"] == "renew" )
	{
		$objDB->transactionBegin();
		// ȯ���ޥ�������
fncDebug("kids2.log", "pass-2", __FILE__, __LINE__, "a" );
		if(!fncUpdatePurchaseOrder($aryData, $objDB, $objAuth)) { return false; }
fncDebug("kids2.log", "pass-3", __FILE__, __LINE__, "a" );
		// ȯ������ٹ���
		if(!fncUpdatePurchaseOrderDetail($aryData, $objDB)) { return false; }
fncDebug("kids2.log", "pass-4", __FILE__, __LINE__, "a" );

		// ������Υǡ���������ɤ߹���
		$updatedPurchaseOrder = fncGetPurchaseOrderEdit($aryData["lngPurchaseOrderNo"], $aryData["lngRevisionNo"], $objDB);

		$strHtml = fncCreatePurchaseOrderUpdateHtml($updatedPurchaseOrder, $aryData["strSessionID"]);
		$aryData["aryPurchaseOrder"] = $strHtml;

		// $objDB->transactionRollback();
		$objDB->transactionCommit();

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();

		header("Content-type: text/plain; charset=EUC-JP");
		$objTemplate->getTemplate( "po/finish/parts.tmpl" );
		
		// �ƥ�ץ졼������
		$objTemplate->replace( $aryData );

		// HTML����
		echo $objTemplate->strTemplate;

		return true;
		
	}

	// ȯ���
	$aryResult = fncGetPurchaseOrderEdit($aryData["lngPurchaseOrderNo"], $aryData["lngRevisionNo"], $objDB);
	if(!$aryResult) { return false; }

	// �إå�
	$aryNewResult["strOrderCode"]             = $aryResult[0]["strordercode"];
	$aryNewResult["lngRevisionNo"]            = sprintf("%02d", $aryResult[0]["lngrevisionno"]);
	$aryNewResult["dtmExpirationDate"]        = $aryResult[0]["dtmexpirationdate"];
	$aryNewResult["strProductCode"]           = $aryResult[0]["strproductcode"];
	$aryNewResult["lngPayConditionCode"]      = fncPulldownMenu(2, $aryResult[0]["lngpayconditioncode"], "", $objDB);
	$aryNewResult["PayConditionDisabled"]     = "";
	$aryNewResult["lngMonetaryUnitCode"]      = fncPulldownMenu(0, $aryResult[0]["lngmonetaryunitcode"], "", $objDB);
	$aryNewResult["MonetaryUnitDisabled"]     = "disabled";
	$aryNewResult["strCustomerCode"]          = $aryResult[0]["strcustomercode"];
	$aryNewResult["strCustomerName"]          = $aryResult[0]["strcustomername"];
	$aryNewResult["strGroupDisplayCode"]      = $aryResult[0]["strgroupdisplaycode"];
	$aryNewResult["strGroupDisplayName"]      = $aryResult[0]["strgroupdisplayname"];
	$aryNewResult["strProductName"]           = $aryResult[0]["strproductname"];
	$aryNewResult["strProductEnglishName"]    = $aryResult[0]["strproductenglishname"];
	$aryNewResult["lngLocationCode"]          = $aryResult[0]["strdeliveryplacecode"];
	$aryNewResult["strLocationName"]          = $aryResult[0]["strdeliveryplacename"];
	$aryNewResult["strNote"]                  = $aryResult[0]["strnote"];
	$aryNewResult["lngPurchaseOrderNo"]       = $aryResult[0]["lngpurchaseorderno"];
	// $aryNewResult["lngRevisionNo"]            = $aryResult[0]["lngrevisionno"];
	$aryNewResult["lngPayConditionCodeOrign"] = $aryResult[0]["lngpayconditioncode"];

	// ����
	$aryNewResult["strPurchaseOrderDetail"] = fncGetPurchaseOrderDetailHtml($aryResult, $objDB);

	$objDB->close();
	$objDB->freeResult( $lngResultID );
	
	$aryNewResult["strSessionID"] = $aryData["strSessionID"];
	$aryNewResult["RENEW"] = TRUE;

	// �إ���б�
	$aryNewResult["lngFunctionCode"] = DEF_FUNCTION_PO5;
	
				// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/po/regist/renew.html");

// �ƥ�ץ졼������
$objTemplate->replace($aryNewResult);
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;

	// echo fncGetReplacedHtml( "po/regist/renew.tmpl", $aryNewResult ,$objAuth );
	
	return true;
	
?>