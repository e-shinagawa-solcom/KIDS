<?php

// ----------------------------------------------------------------------------
/**
*       Ǽ�ʽ���
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
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

	//-------------------------------------------------------------------------
	// �饤�֥��ե������ɹ�
	//-------------------------------------------------------------------------
	include('conf.inc');
	require (LIB_FILE);
	require (SRC_ROOT."sc/cmn/lib_scr.php");
	require (SRC_ROOT."sc/cmn/lib_scd1.php");

	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	//-------------------------------------------------------------------------
	// �ѥ�᡼������
	//-------------------------------------------------------------------------
	// ���å����ID
	if ($_POST["strSessionID"]){
		$aryData["strSessionID"] = $_POST["strSessionID"];
	}else{
		$aryData["strSessionID"] = $_REQUEST["strSessionID"];   
	}
	setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

	// �����⡼��
	$strMode    = $_POST["strMode"];

	// --------------------
	// �����оݤ�ɳ�Ť�����
	// --------------------
	// Ǽ����ɼ�ֹ�
	$lngSlipNo = $_GET["lngSlipNo"];
	// Ǽ����ɼ������
	$strSlipCode = $_GET["strSlipCode"];
	// Ǽ�ʽ�Υ�ӥ�����ֹ�
	$lngRevisionNo = $_GET["lngRevisionNo"];
	// ����ֹ�
	$lngSalesNo = $_GET["lngSalesNo"];
	// ��女����
	$strSalesCode = $_GET["strSalesCode"];
	// �ܵҥ�����
	$strCustomerCode = $_GET["strCustomerCode"];

	//-------------------------------------------------------------------------
	// DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");

	//-------------------------------------------------------------------------
	// ����ʸ�����͡����å���󡦸��¥����å�
	//-------------------------------------------------------------------------
	// ʸ��������å�
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode  = $objAuth->UserCode;
	$lngUserGroup = $objAuth->AuthorityGroupCode;

	// 600 ������
	if( !fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
	}

	// 601 �������������Ͽ��
	if( fncCheckAuthority( DEF_FUNCTION_SC1, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}

	// 610 �������ʹ��ɲá��Ժ����
	if( !fncCheckAuthority( DEF_FUNCTION_SC10, $objAuth ) )
	{
		$aryData["adddelrowview"] = 'hidden';
	}
	
	// --------------------------------
	//    ������ǽ���ɤ����Υ����å�
	// --------------------------------
	// �ܵҤι����ܤǡ�����Ǽ�ʽ�إå���ɳ�Ť���������٤�¸�ߤ�����Ͻ����Բ�
	if (fncJapaneseInvoiceExists($strCustomerCode, $lngSalesNo, $objDB)){
		MoveToErrorPage("�����ȯ�ԺѤߤΤ��ᡢ�����Ǥ��ޤ���");
	}

	// Ǽ�ʽ����٤�ɳ�Ť������ơ������������Ѥߡפξ��Ͻ����Բ�
	if (fncReceiveStatusIsClosed($lngSlipNo, $objDB))
	{
		MoveToErrorPage("���ѤߤΤ��ᡢ�����Ǥ��ޤ���");
	}

	//-------------------------------------------------------------------------
	// ��ajax�۸ܵҤ�ɳ�Ť��񥳡��ɤ����
	//-------------------------------------------------------------------------
	if ($strMode == "get-lngcountrycode"){
		// �ܵҥ�����
		$strCompanyDisplayCode = $_POST["strcompanydisplaycode"];
		// �񥳡��ɼ���
		$lngCountryCode = fncGetCountryCode($strCompanyDisplayCode, $objDB);
		// �ǡ����ֵ�
		echo $lngCountryCode;
		// DB����
		$objDB->close();
		// ������λ
		return true;
	}
	
	//-------------------------------------------------------------------------
	// ��ajax�۸ܵҤ�ɳ�Ť������������
	//-------------------------------------------------------------------------
	if ($strMode == "get-closedday"){
		// �ܵҥ�����
		$strCompanyDisplayCode = $_POST["strcompanydisplaycode"];
		// ����������
		$lngClosedDay = fncGetClosedDay($strCompanyDisplayCode, $objDB);
		// �ǡ����ֵ�
		echo $lngClosedDay;
		// DB����
		$objDB->close();
		// ������λ
		return true;
	}

	//-------------------------------------------------------------------------
	// ��ajax�����ٸ���
	//-------------------------------------------------------------------------
	if($strMode == "search-detail"){
		// DB�������٤򸡺�
		$aryReceiveDetail = fncGetReceiveDetail($_POST["condition"], $objDB);
		// �������򥨥ꥢ�˽��Ϥ���HTML�κ���
		$withCheckBox = true;
		$strHtml = fncGetReceiveDetailHtml($aryReceiveDetail, $withCheckBox);
		// �ǡ����ֵ�
		echo $strHtml;
		// DB����
		$objDB->close();
		// ������λ
		return true;
	}

	//-------------------------------------------------------------------------
	// ��ajax��Ǽ�����ѹ����ξ�����Ψ������ܺ�����
	//-------------------------------------------------------------------------
	if($strMode == "change-deliverydate"){
		// �ѹ����Ǽ�������б����������Ψ��������ܤ����
		$optTaxRate = fncGetTaxRatePullDown($_POST["dtmDeliveryDate"], "", $objDB);
		// �ǡ����ֵ�
		echo $optTaxRate;
		// DB����
		$objDB->close();
		// ������λ
		return true;
	}

	//-------------------------------------------------------------------------
	// �����оݥǡ����Υ�å�����
	//-------------------------------------------------------------------------
	// ¾�˥�å����Ƥ���ͤ����ʤ�����ǧ
	$lockUserName = fncGetExclusiveLockUser(EXCLUSIVE_CONTROL_FUNCTION_CODE_SC_RENEW, $strSlipCode, $objAuth, $objDB);
	if (strlen($lockUserName) > 0)
	{
		MoveToErrorPage("�桼����".$lockUserName."��������Ǥ���");
	}

	// �����оݥǡ����Υ�å�����
	$locked = fncTakeExclusiveLock(EXCLUSIVE_CONTROL_FUNCTION_CODE_SC_RENEW, $strSlipCode, $objAuth, $objDB);
	if (!$locked)
	{
		MoveToErrorPage("Ǽ�ʽ�ǡ����Υ�å��˼��Ԥ��ޤ�����");
	}

	//-------------------------------------------------------------------------
	// �����оݥǡ�������
	//-------------------------------------------------------------------------
	// Ǽ����ɼ�ֹ��ɳ�Ť��إå����եå����Υǡ����ɤ߹���
	$aryHeader = fncGetHeaderBySlipNo($lngSlipNo, $lngRevisionNo, $objDB);

	// Ǽ����ɼ�ֹ��ɳ�Ť��������Υ������������
	$aryDetailKey = fncGetDetailKeyBySlipNo($lngSlipNo, $objDB);
	
	// �������Υ�����ɳ�Ť��������پ�����������
	$aryDetail = array();
	for ( $i = 0; $i < count($aryDetailKey); $i++ ){

		$aryCondition = array();
		$aryCondition["lngReceiveNo"] = $aryDetailKey[$i]["lngreceiveno"];
		$aryCondition["lngReceiveDetailNo"] = $aryDetailKey[$i]["lngreceivedetailno"];
		$aryCondition["lngReceiveRevisionNo"] = $aryDetailKey[$i]["lngreceiverevisionno"];
		
		// ������ɳ�Ť����٤�1�鷺�ļ����������Τ�����˥ޡ���
		$arySubDetail = fncGetReceiveDetail($aryCondition, $objDB);
		$aryDetail = array_merge($aryDetail, $arySubDetail);
	}

	// ��������HTML������
	$withCheckBox = false;
	$strDetailHtml = fncGetReceiveDetailHtml($aryDetail, $withCheckBox);
	
	//-------------------------------------------------------------------------
	// �ե�������������
	//-------------------------------------------------------------------------
	// -------------------------
	//  �����оݥǡ�����ɳ�Ť���
	// -------------------------
	// Ǽ����ɼ�ֹ�ʤ����ͤ����åȤ���Ƥ����齤���Ȥߤʤ���
	$aryData["lngSlipNo"] = $lngSlipNo;
	// Ǽ����ɼ������
	$aryData["strSlipCode"] = $strSlipCode;
	// ����ֹ�
	$aryData["lngSalesNo"] = $lngSalesNo;
	// ��女����
	$aryData["strSalesCode"] = $strSalesCode;

	// -------------------------
	//  �������ٰ������ꥢ
	// -------------------------
	// ��������HTML�򥻥å�
	$aryData["strEditTableBody"] = $strDetailHtml;

	// -------------------------
	//  �إå����եå���
	// -------------------------
	// ��ɼ��
	$aryData["lngInsertUserCode"] = $aryHeader["struserdisplaycode"];
	$aryData["strInsertUserName"] = $aryHeader["struserdisplayname"];

	// �ܵ�
	$aryData["lngCustomerCode"] = $aryHeader["strcustomercode"];
	$aryData["strCustomerName"] = $aryHeader["strcustomerdisplayname"];

	// �ܵ�¦ô����
	$aryData["strCustomerUserName"] = $aryHeader["strcustomerusername"];

	// Ǽ����
	$aryData["dtmDeliveryDate"] = $aryHeader["dtmdeliverydate"];

	// ��ʧ��ˡ�ץ������
	$lngDefaultPaymentMethodCode = $aryHeader["lngpaymentmethodcode"];
	$optPaymentMethod .= fncGetPulldown("m_paymentmethod","lngpaymentmethodcode","strpaymentmethodname", $lngDefaultPaymentMethodCode, "", $objDB);
	$aryData["optPaymentMethod"] = $optPaymentMethod;

	// ��ʧ����
	$aryData["dtmPaymentDueDate"] = $aryHeader["dtmpaymentlimit"];

	// Ǽ����
	$aryData["lngDeliveryPlaceCode"] = $aryHeader["strcompanydisplaycode"];
	$aryData["strDeliveryPlaceName"] = $aryHeader["strdeliveryplacename"];

	// Ǽ����ô����
	$aryData["strDeliveryPlaceUserName"] = $aryHeader["strdeliveryplaceusername"];

	// ����
	$aryData["strNote"] = $aryHeader["strnote"];

	// �����Ƕ�ʬ�ץ������
	$lngDefaultTaxClassCode = $aryHeader["lngtaxclasscode"];
	$optTaxClass .= fncGetPulldown("m_taxclass","lngtaxclasscode","strtaxclassname", $lngDefaultTaxClassCode , "", $objDB);
	$aryData["optTaxClass"] = $optTaxClass;

	// ������Ψ�ץ������
	if($aryData["dtmDeliveryDate"]){
		$curDefaultTax = $aryHeader["curtax"];
		$optTaxRate = fncGetTaxRatePullDown($aryData["dtmDeliveryDate"], $curDefaultTax, $objDB);
		$aryData["optTaxRate"] = $optTaxRate;
	}

	// �����ǳۡʢ������Ǥ�0�򥻥åȤ��Ƥ���������ɽ������javascript�Ǵؿ���ƤӽФ��Ʒ׻������
	$aryData["strTaxAmount"] = "0";

	// ��׶��
	$aryData["strTotalAmount"] = $aryHeader["curtotalprice"];

	//-------------------------------------------------------------------------
	// ����ɽ��
	//-------------------------------------------------------------------------
	// ajax POST��򤳤Υե�����ˤ���
	$aryData["ajaxPostTarget"] = "renew.php";

	// Ǽ�ʽ�������ɽ���ʥƥ�ץ졼�Ȥ�����Ǽ�ʽ����Ͽ���̤ȶ��̡�
	echo fncGetReplacedHtml( "sc/regist2/parts.tmpl", $aryData ,$objAuth);

	// DB����
	$objDB->close();

	// ������λ
	return true;

// ���顼���̤ؤ�����
function MoveToErrorPage($strMessage){
	
	// ���쥳���ɡ����ܸ�
	$aryHtml["lngLanguageCode"] = 1;

	// ���顼��å�����������
	$aryHtml["strErrorMessage"] = $strMessage;

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/result/error/parts.tmpl" );
	
	// �ƥ�ץ졼������
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	exit;
}

?>