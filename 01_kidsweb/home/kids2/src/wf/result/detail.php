<?
/** 
*	����ե� �ܺپ���ɽ������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	2004.04.19	�ܺٲ��̤���ȯ��ܺ�ɽ�����˥��å���󥨥顼��ɽ������Х��ν���
*
*/
// index.php -> strSessionID          -> detail.php
// index.php -> lngFunctionCode       -> detail.php
// index.php -> lngWorkflowStatusCode -> detail.php
// index.php -> lngApplicantUserCode  -> detail.php
// index.php -> lngInputUserCode      -> detail.php
// index.php -> dtmStartDateFrom      -> detail.php
// index.php -> dtmStartDateTo        -> detail.php
// index.php -> dtmEndDateFrom        -> detail.php
// index.php -> dtmEndDateTo          -> detail.php
// index.php -> lngInChargeCode       -> detail.php
// index.php -> lngWorkflowCode       -> detail.php
// index.php -> lngSelectFunctionCode -> index.php

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

$aryCheck["strSessionID"]            = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]         = "null:number(" . DEF_FUNCTION_WF1 . "," . DEF_FUNCTION_WF3 . ")";
//$aryCheck["lngWorkflowStatusCode"]   = "number(" . DEF_STATUS_VOID . "," . DEF_STATUS_DENIAL . ")";
$aryCheck["lngApplicantUserCode"]    = "number(0,32767)";
$aryCheck["lngInputUserCode"]        = "number(0,32767)";
$aryCheck["dtmStartDateFrom"]        = "date(/)";
$aryCheck["dtmStartDateTo"]          = "date(/)";
$aryCheck["dtmEndDateFrom"]          = "date(/)";
$aryCheck["dtmEndDateTo"]            = "date(/)";
$aryCheck["lngInChargeCode"]         = "number(0,32767)";
$aryCheck["lngWorkflowCode"]         = "number(0,2147483647)";
$aryCheck["lngSelectFunctionCode"] = "number(0,32767)";

// �����å��ܥå������Ϥ��줿WF���ơ�������ʸ���������
$aryData["lngWorkflowStatusCode"] = fncGetArrayToWorkflowStatusCode($aryData["lngWorkflowStatusCode"]);

// ���å�����ǧ
$objAuth = fncIsSession( $aryData[strSessionID], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_WF4, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
if ( fncCheckAuthority( DEF_FUNCTION_WF5, $objAuth ) )
{
	$lngFunctionCode = DEF_FUNCTION_WF5;
}


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

// ���̼����Ϥ�URL����(���å����ID���ڡ������Ƹ������)
$strURL = fncGetURL( $aryData );

// ����ե�����
// �Ʒ��ɤ߹��ߡ��������ܺپ������������ؿ�
list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getWorkflowQuery( $objAuth->UserCode, $aryData, $objDB );

//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////


$objResult = $objDB->fetchObject( $lngResultID, 0 );

$partsData["dtmStartDate"]       = $objResult->dtmstartdate;
//$partsData["strWorkflowName"]    = $objResult->strworkflowname;
$partsData["strApplicantName"]   = $objResult->strapplicantname;
$partsData["strInputName"]       = $objResult->strinputname;
$partsData["strRecognitionName"] = $objResult->strrecognitionname;
$partsData["dtmLimitDate"]       = $objResult->dtmlimitdate;
$partsData["dtmEndDate"]         = $objResult->dtmenddate;
$partsData["strWorkflowKeyCode"] = $objResult->strworkflowkeycode;
$partsData["lngStatusCode"]      = $aryWorkflowStatus[$objResult->lngworkflowstatuscode];
$lngFunctionCode    = $objResult->lngfunctioncode;

/*
//
// ȯ������ե��ξ��
//
if( $objResult->lngfunctioncode == DEF_FUNCTION_PO1 )
{
	// ȯ��ˤƻ��ꤷ�Ƥ������ʥ����ɤμ�������
	$strProductCodeQuery = "SELECT od.strProductCode as strProductCode FROM t_OrderDetail od WHERE od.lngOrderNo = " . $partsData["strWorkflowKeyCode"];

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
		$partsData["strWorkflowName"]   = "<td class=\"Segs\" onClick=\"javascript:fncShowWfDialogCommon('/po/result/index2.php?strSessionID=" . $aryData["strSessionID"] . "&lngOrderNo=" . $partsData["strWorkflowKeyCode"] . "' , window.form1 , 'ResultIframeWf' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail', 505, 679, 6, 30 );\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $lngEstimateNo . "\" target=_blank>" . $partsData["strWorkflowName"] . "</a></td>";
	}
}
//
// ���Ѹ����Υ���ե��ξ��
//
elseif( $objResult->lngfunctioncode == DEF_FUNCTION_E1 )
{
	// ���Ѹ����Υ���ե��ξ�硢���Ѹ����������ƤΥ�����ɥ��򳫤�����
	$partsData["strWorkflowName"]   = "<td class=\"Segs\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $partsData["strWorkflowKeyCode"] . "\" target=_blank>" . $partsData["strWorkflowName"] . "</a></td>";
}

//
// �嵭��ȯ��ʸ��Ѹ�����ʻ�ѡˡ����Ѹ������˳������ʤ���¾�Υ���ե��ξ��
//
if( empty($partsData["strWorkflowName"]) )
{
	$partsData["strWorkflowName"]   = "<td class=\"Segs\" onClick=\"javascript:fncShowDialogCommon('".$aryFunctionLink[$objResult->lngfunctioncode]."?strSessionID=" . $aryData["strSessionID"] . "&".$aryWorkflowKeyName[$objResult->lngfunctioncode]."=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail' );\"><a href=# class=wfA>" . $objResult->strworkflowname . "</a></td>";
}
*/

// �Ʒ����ʳƥ���ե����֤���������
$partsData["strWorkflowName"] = fncGetWorkflowNameLink( $objDB, $objResult, $aryData["strSessionID"]);

fncDebug('wf.txt', $partsData["strWorkflowName"], __FILE__, __LINE__);


$objDB->freeResult( $lngResultID );


// ����ե����֤����
$strQuery = "SELECT wfo.lngWorkflowOrderNo, u.strUserDisplayName " .
            "FROM m_Workflow wf, m_WorkflowOrder wfo, m_User u " .
            "WHERE wf.lngWorkflowCode = " . $aryData["lngWorkflowCode"] . " " .
            " AND wfo.lngWorkflowOrderCode = wf.lngWorkflowOrderCode " .
            " AND wfo.lngInChargeCode = u.lngUserCode " .
            "ORDER BY wfo.lngWorkflowOrderNo";

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum < 1 )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );
	$aryWorkflowOrder[] = "<tr><td class=\"SegColumn\">" . $objResult->lngworkfloworderno . "</td><td class=\"Segs\">" . $objResult->struserdisplayname . "</td></tr>\n";
}

$partsData["strWorkflowOrder"] = join ( "", $aryWorkflowOrder );

$partsData["strMode"] = "detail";
$partsData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
/*
$partsData["strRecognAction"] = "";
$partsData["strDenyAction"] = "";
$partsData["strCancelAction"] = "";
$partsData["strProcessAction"] = "";
*/


// �ѡ��ĥƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "wf/result/parts.tmpl" );
$strPartsTemplate = $objTemplate->strTemplate;

// �ƥ�ץ졼���ɤ߹���
if( $lngFunctionCode == DEF_FUNCTION_E1 )
{
	$objTemplate->getTemplate( "wf/regist/confirm_estimate.tmpl" );
}
else
{
	$objTemplate->getTemplate( "wf/regist/confirm.tmpl" );
}

// �ƥ�ץ졼������
$objTemplate->replace( $partsData );
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;
$objDB->close();

return TRUE;
?>
