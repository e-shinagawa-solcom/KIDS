<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ��Ͽ
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
*         ����Ͽ����
*         �����顼�����å�
*         ����Ͽ������λ�塢��Ͽ��λ���̤�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

	// �ɤ߹���
	include('conf.inc');
	require (LIB_FILE);
	require (SRC_ROOT."sc/cmn/lib_scr.php");
/*
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	
	$aryData["strSessionID"] = $_GET["strSessionID"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	$aryData["aryPurchaseOrderNo"] = $_GET["aryPurchaseOrderNo"];

	$objDB->open("", "", "", "");
	
	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );



	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	
	$lngInputUserCode = $objAuth->UserCode;
	
	// ���³�ǧ
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	// 500	ȯ�����
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	
	// 501 ȯ�������ȯ����Ͽ��
	if ( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	
	// 508 ȯ������ʾ��ʥޥ��������쥯�Ƚ�����
	if( !fncCheckAuthority( DEF_FUNCTION_PO8, $objAuth ) )
	{
		$aryData["popenview"] = 'hidden';
	}

	// ȯ���ǡ�������
	$aryPurchaseOrderNo = explode(",", $aryData["aryPurchaseOrderNo"]);
	for($i = 0; $i < count($aryPurchaseOrderNo); $i++){
		$arr = explode("-", $aryPurchaseOrderNo[$i]);
		$aryKey[$i]["purchaseorderno"] = $arr[0];
		$aryKey[$i]["revisionno"] = $arr[1];
	}
	$aryPurcharseOrder = fncGetPurchaseOrder($aryKey, $objDB);
	if(!$aryPurcharseOrder){
		fncOutputError ( 9051, DEF_ERROR, "ȯ���μ����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
		return FALSE;
	}
	
	$strHtml = fncCreatePurchaseOrderHtml($aryPurcharseOrder);
	$aryData["aryPurchaseOrder"] = $strHtml;


	
	$aryData["strBodyOnload"] = "";
	
	$objDB->close();

	// �����������Υ��ɥ쥹����
	$aryData["strAction"] = "/sc/regist2/index.php?strSessionID=";
*/

	echo fncGetReplacedHtmlWithBase("base_mold_noframes.html", "sc/regist2/condition.tmpl", $aryData ,$objAuth );

	// �ƥ�ץ졼���ɤ߹���
	//$objTemplate = new clsTemplate();
	
	// �ƥ�ץ졼�Ȥ�ȿ�Ǥ���ʸ����
	//$aryData["lngPONo"] = "$strOrderCode - $strReviseCode";

	//header("Content-type: text/plain; charset=EUC-JP");
	//$objTemplate->getTemplate( "sc/finish2/parts.tmpl" );
	

	


	// �ƥ�ץ졼������
	//$objTemplate->replace( $aryData );
	//$objTemplate->complete();

	// HTML����
	//echo $objTemplate->strTemplate;
			
	return true;
?>