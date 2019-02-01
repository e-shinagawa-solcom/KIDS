<?
/** 
*	����ե� �Ʒ��������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	2004.04.20	�����оݤΰƷ郎���Ĥ���ʤ��ä����Υ�å��������ѹ�
*				���Ĥ���ʤ�������¾�Υ桼������������Ԥä�
*
*/
// index.php -> strSessionID          -> edit.php
// index.php -> lngFunctionCode       -> edit.php
// index.php -> lngWorkflowStatusCode -> edit.php
// index.php -> lngApplicantUserCode  -> edit.php
// index.php -> lngInputUserCode      -> edit.php
// index.php -> dtmStartDateFrom      -> edit.php
// index.php -> dtmStartDateTo        -> edit.php
// index.php -> dtmEndDateFrom        -> edit.php
// index.php -> dtmEndDateTo          -> edit.php
// index.php -> lngInChargeCode       -> edit.php
// index.php -> lngWorkflowCode       -> edit.php
//
// ɽ������Ʒ�ε�ǽ������(DEF_FUNCTION)(�����500:ȯ������Τ�)
// index.php -> lngSelectFunctionCode -> edit.php
//
// lib_wf.php�ˤ��ɤ߹��९�������̤��뤿��ν���������(���ܤ�DEF_FUNCTION_WF6)
// index.php -> lngActionFunctionCode -> edit.php
//
// �Ʒ�����¹Ԥ�
// edit.php -> strSessionID          -> confirm.php
// edit.php -> lngFunctionCode       -> confirm.php
// edit.php -> lngWorkflowStatusCode -> confirm.php
// edit.php -> lngApplicantUserCode  -> confirm.php
// edit.php -> lngInputUserCode      -> confirm.php
// edit.php -> dtmStartDateFrom      -> confirm.php
// edit.php -> dtmStartDateTo        -> confirm.php
// edit.php -> dtmEndDateFrom        -> confirm.php
// edit.php -> dtmEndDateTo          -> confirm.php
// edit.php -> lngInChargeCode       -> confirm.php
// edit.php -> lngWorkflowCode       -> confirm.php
//
// ɽ������Ʒ�ε�ǽ������(DEF_FUNCTION)(�����500:ȯ������Τ�)
// edit.php -> lngSelectFunctionCode -> confirm.php
//
// lib_wf.php�ˤ��ɤ߹��९�������̤��뤿��ν���������(���ܤ�DEF_FUNCTION_WF6)
// edit.php -> lngActionFunctionCode -> confirm.php
//
// �������ܥ���(DEF_STATUS_ORDER, DEF_STATUS_DENIAL, DEF_STATUS_CANCELL)
// edit.php -> lngTransactionCode    -> confirm.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "wf/cmn/lib_wf.php");
require( LIB_DEBUGFILE );

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// GET�ǡ�������
$aryData = $_GET;

// ������ΰƷ�Τ߽�������ǽ�ʤ��ᡢ���ֿ֡�����פ򸡺����Ȥ��ƶ���
$aryData["lngWorkflowStatusCodeConditions"] =1;
$aryData["lngWorkflowStatusCode"] = DEF_STATUS_ORDER;

$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]       = "null:number(" . DEF_FUNCTION_WF1 . "," . DEF_FUNCTION_WF3 . ")";
$aryCheck["lngApplicantUserCode"]  = "number(0,32767)";
$aryCheck["lngInputUserCode"]      = "number(0,32767)";
$aryCheck["dtmStartDateFrom"]      = "date(/)";
$aryCheck["dtmStartDateTo"]        = "date(/)";
$aryCheck["dtmEndDateFrom"]        = "date(/)";
$aryCheck["dtmEndDateTo"]          = "date(/)";
$aryCheck["lngInChargeCode"]       = "number(0,32767)";
$aryCheck["lngWorkflowCode"]       = "number(0,2147483647)";
$aryCheck["lngActionFunctionCode"] = "number(0,32767)";
$aryCheck["lngSelectFunctionCode"] = "number(0,32767)";


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_WF6, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

// ���̼����Ϥ�URL����(���å����ID���ڡ������Ƹ������)
$strURL = fncGetURL( $aryData );

$aryParts["comment"] = "���������򤷤Ƥ���������";

// ����ե�����
// �Ʒ��ɤ߹��ߡ��������ܺپ������������ؿ�
list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getWorkflowQuery( $objAuth->UserCode, $aryData, $objDB );

if ( !$lngResultNum )
{
// 2004.04.20 suzukaze update start
// ���ξ��֤��оݰƷ郎���Ĥ���ʤ�������¾�Υ桼������������¹Ԥ���
// ��¾�Υ桼�����ν����ˤ�ꡢ�оݰƷ�ϡֿ�����פǤϤʤ��ʤ�ޤ������פΥ�å�������ɽ������
	fncOutputError ( 803, DEF_WARNING, "", TRUE, "", $objDB );
// 2004.04.20 suzukaze update end
}

$objResult = $objDB->fetchObject( $lngResultID, 0 );
$aryParts["dtmStartDate"]       = $objResult->dtmstartdate;
//$aryParts["strWorkflowName"]    = $objResult->strworkflowname;
$aryParts["strWorkflowKeyCode"] = $objResult->strworkflowkeycode;
$aryParts["strApplicantName"]   = $objResult->strapplicantname;
$aryParts["strInputName"]       = $objResult->strinputname;
$aryParts["strRecognitionName"] = $objResult->strrecognitionname;
$aryParts["dtmLimitDate"]       = $objResult->dtmlimitdate;
$aryParts["dtmEndDate"]         = $objResult->dtmenddate;
$aryParts["lngStatusCode"]      = $aryWorkflowStatus[$objResult->tstatuscode];

/*
//
// ȯ������ե��ξ��
//
if( $objResult->lngfunctioncode == DEF_FUNCTION_PO1 )
{
	// ȯ��ˤƻ��ꤷ�Ƥ������ʥ����ɤμ�������
	$strProductCodeQuery = "SELECT od.strProductCode as strProductCode FROM t_OrderDetail od WHERE od.lngOrderNo = " . $aryParts["strWorkflowKeyCode"];

	// �ͤ�Ȥ� =====================================
	$lngEstimateNo = "";
	list ( $lngResultProductCodeID, $lngResultProductCodeNum ) = fncQuery( $strProductCodeQuery, $objDB );
	if ( $lngResultProductCodeNum )
	{
		$objProductCodeResult = $objDB->fetchObject( $lngResultProductCodeID, 0 );
		$strProductCode = $objProductCodeResult->strproductcode;

		// ���Ѹ����ǡ�������
		$aryEstimateQuery[] = "SELECT e.lngEstimateNo ";
		$aryEstimateQuery[] = "FROM m_Estimate e";
		$aryEstimateQuery[] = "WHERE e.strProductCode = '" . $strProductCode . "'";
		$aryEstimateQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM m_Estimate e2 WHERE e.lngEstimateNo = e2.lngEstimateNo)";
		$aryEstimateQuery[] = " AND e.bytDecisionFlag = true ";

		list ( $lngResultEstimateID, $lngResultEstimateNum ) = fncQuery( join ( " ", $aryEstimateQuery ), $objDB );

		if ( $lngResultEstimateNum )
		{
			$objEstimateResult = $objDB->fetchObject( $lngResultEstimateID, 0 );
			$objDB->freeResult( $lngResultEstimateID );
			unset ( $lngResultEstimateID );
			unset ( $lngResultEstimateNum );

			$lngEstimateNo = $objEstimateResult->lngestimateno;
			unset ( $objEstimateResult );
		}
		unset( $aryEstimateQuery );
	}
	$objDB->freeResult( $lngResultProductCodeID );

	// ���˻�������ʥ����ɤ��Ф��Ƹ��Ѹ�������¸�ߤ����
	if ( $lngEstimateNo != "" )
	{
		// ȯ�����Ƥȸ��Ѹ��������Υ�����ɥ��򳫤�����
		$aryParts["strWorkflowName"]   = "<td class=\"Segs\" onClick=\"javascript:fncShowWfDialogCommon('/po/result/index2.php?strSessionID=" . $aryData["strSessionID"] . "&lngOrderNo=" . $aryParts["strWorkflowKeyCode"] . "' , window.form1 , 'ResultIframeWf' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail', 505, 679, 6, 30 );\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $lngEstimateNo . "\" target=_blank>" . $aryParts["strWorkflowName"] . "</a></td>";
	}
}
//
// ���Ѹ����Υ���ե��ξ��
//
elseif( $objResult->lngfunctioncode == DEF_FUNCTION_E1 )
{
	// ���Ѹ����Υ���ե��ξ�硢���Ѹ����������ƤΥ�����ɥ��򳫤�����
	$aryParts["strWorkflowName"]   = "<td class=\"Segs\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $aryParts["strWorkflowKeyCode"] . "\" target=_blank>" . $aryParts["strWorkflowName"] . "</a></td>";
}

//
// �嵭��ȯ��ʸ��Ѹ�����ʻ�ѡˡ����Ѹ������˳������ʤ���¾�Υ���ե��ξ��
//
if( empty($aryParts["strWorkflowName"]) )
{
	$aryParts["strWorkflowName"]   = "<td class=\"Segs\" onClick=\"javascript:fncShowDialogCommon('".$aryFunctionLink[$objResult->lngfunctioncode]."?strSessionID=" . $aryData["strSessionID"] . "&".$aryWorkflowKeyName[$objResult->lngfunctioncode]."=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail' );\"><a href=# class=wfA>" . $objResult->strworkflowname . "</a></td>";
}
*/

// �Ʒ����ʳƥ���ե����֤���������
$aryParts["strWorkflowName"] = fncGetWorkflowNameLink( $objDB, $objResult, $aryData["strSessionID"]);




//////////////////////////////////////////////////////////////////////////
// �桼�����̥ܥ���ɽ������
//////////////////////////////////////////////////////////////////////////

// �����ܥ���(��ǧ����ǧ���������ä�)ɽ����ǧ�Τ����
// ������桼�����Υ���ե������ɤ��ֹ�����
list ( $aryWorkflowOrderCode, $aryWorkflowOrderNo ) = fncGetArrayData( $objAuth->UserCode, 0, $objDB );

// ������ ����
// ��ǧ�Ԥ�������桼������Ʊ�����ϡ־�ǧ�ס���ǧ�פ�ɽ��
if ( $objResult->tstatuscode == DEF_STATUS_ORDER && $objResult->lnginchargecode == $objAuth->UserCode )
{
	$aryParts["strRecognAction"] = "confirm.php?$strURL&lngWorkflowCode=$aryData[lngWorkflowCode]&lngActionFunctionCode=" . DEF_FUNCTION_WF6 . "&lngTransactionCode=" . DEF_STATUS_ORDER;
	$aryParts["strDenyAction"]   = "confirm.php?$strURL&lngWorkflowCode=$aryData[lngWorkflowCode]&lngActionFunctionCode=" . DEF_FUNCTION_WF6 . "&lngTransactionCode=" . DEF_STATUS_DENIAL;
}


// ������ ����
// ���ϼԤ�������桼������Ʊ��
// ���ϡֿ������ä��פ�ɽ��
elseif ( $objResult->tstatuscode == DEF_STATUS_ORDER && $objResult->lnginputusercode == $objAuth->UserCode )
{
	$flgPutButton = TRUE;
}

// ������ ����
// ������桼�����ν��֡㸽�ߤν��֤Ǥ���
// ���ϡֿ������ä��פ�ɽ��
elseif ( $objResult->tstatuscode == DEF_STATUS_ORDER )
{
	// ������桼�����Υ���ե������ֹ椬
	// ɽ������Ʒ���ֹ��꾮�������
	for ( $j = 0; $j < count ( $aryWorkflowOrderCode ); $j++ )
	{
		if ( $aryWorkflowOrderCode[$j] == $objResult->lngworkflowordercode && $aryWorkflowOrderNo[$j] < $objResult->lngworkfloworderno )
		{
			$flgPutButton = TRUE;
			break;
		}
	}
}

if ( $flgPutButton )
{
	$aryParts["strCancelAction"] = "confirm.php?$strURL&lngWorkflowCode=$aryData[lngWorkflowCode]&lngActionFunctionCode=" . DEF_FUNCTION_WF6 . "&lngTransactionCode=" . DEF_STATUS_CANCELL;
}


//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
//$aryParts["close"] = "<h3><a href=\"javascript:window.close();\">�Ĥ���</a></h3>\n";

/*
foreach ( $aryParts as $strKey )
{
	echo $strKey;
}
*/

// HTML����
//$aryData["RENEW"] = TRUE;
//echo fncGetReplacedHtml( "p/regist/parts.tmpl", $aryData, $objAuth );

$aryParts["strMode"] = "edit";
$aryParts["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
$aryParts["strSessionID"] =& $aryData["strSessionID"];







$objTemplate = new clsTemplate();

// �ƥ�ץ졼���ɤ߹���
if( $lngFunctionCode == DEF_FUNCTION_E1 )
{
	$objTemplate->getTemplate( "wf/regist/confirm_estimate.tmpl" );
}
else
{
	$objTemplate->getTemplate( "wf/regist/confirm.tmpl" );
}

$objTemplate->replace( $aryParts );
$objTemplate->complete();


echo $objTemplate->strTemplate;


$objDB->close();


return TRUE;
?>
