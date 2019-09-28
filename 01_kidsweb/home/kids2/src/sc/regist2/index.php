<?php

// ----------------------------------------------------------------------------
/**
*       ����Ǽ�ʽ����Ͽ
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
		// TODO:����������
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
		$strHtml = fncGetReceiveDetailHtml($aryReceiveDetail);
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
		$optTaxRate = fncGetTaxRatePullDown($_POST["dtmDeliveryDate"], $objDB);
		// �ǡ����ֵ�
		echo $optTaxRate;
		// DB����
		$objDB->close();
		// ������λ
		return true;
	}

	//-------------------------------------------------------------------------
	// �ե�������������
	//-------------------------------------------------------------------------
	// �إå����եå���

	// Ǽ����
	$nowDate = new DateTime();
	$aryData["dtmDeliveryDate"] = $nowDate->format('Y/m/d');

	// ��ʧ����
	$oneMonthLater = $nowDate->modify('+1 month');
	$aryData["dtmPaymentDueDate"] = $oneMonthLater->format('Y/m/d');

	// ��ʧ��ˡ�ץ������
	$optPaymentMethod .= fncGetPulldown("m_paymentmethod","lngpaymentmethodcode","strpaymentmethodname", "", "", $objDB);
	$aryData["optPaymentMethod"] = $optPaymentMethod;

	// �����Ƕ�ʬ�ץ������
	$optTaxClass .= fncGetPulldown("m_taxclass","lngtaxclasscode","strtaxclassname", "", "", $objDB);
	$aryData["optTaxClass"] = $optTaxClass;

	// ������Ψ�ץ������
	$optTaxRate = fncGetTaxRatePullDown($aryData["dtmDeliveryDate"], $objDB);
	$aryData["optTaxRate"] = $optTaxRate;

	// �����ǳ�
	$aryData["strTaxAmount"] = "0";

	// ��׶��
	$aryData["strTotalAmount"] = "0";

	// ����Ǽ�ʽ����Ͽ����ɽ���ʥƥ�ץ졼�Ȥ�Ǽ�ʽ������̤ȶ��̡�
	echo fncGetReplacedHtml( "sc/regist2/parts.tmpl", $aryData ,$objAuth);

	// DB����
	$objDB->close();

	// ������λ
	return true;

?>