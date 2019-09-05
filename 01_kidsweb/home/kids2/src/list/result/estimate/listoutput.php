<?
/** 
*	Ģɼ���� ���Ѹ����׻� �����ץ�ӥ塼����
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*/
// ���Ѹ��� �����ץ�ӥ塼����
// frameset.php -> strSessionID     -> listoutput.php
// frameset.php -> lngReportCode    -> listoutput.php
// frameset.php -> lngReportKeyCode -> listoutput.php

// �����ɤ߹���
include_once('conf.inc');
require( LIB_DEBUGFILE );

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "list/cmn/lib_lo.php");
require (SRC_ROOT . "list/result/estimate/estimate.php");

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(����GET)�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}


// ʸ��������å�
$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
$aryCheck["lngReportCode"]      = "ascii(1,7)";
$aryCheck["strReportKeyCode"]   = "null:number(0,9999999)";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) || !fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ���Ѹ������ԡ��ե�����ѥ���������������
$strQuery = fncGetCopyFilePathQuery( DEF_REPORT_ESTIMATE, $aryData["strReportKeyCode"], $aryData["lngReportCode"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum > 0 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$strReportPathName = $objResult->strreportpathname;
	unset ( $objResult );
}

$copyDisabled = "visible";

// ���ԡ��ե�����ѥ���¸�ߤ��ʤ� �ޤ���
// Ģɼ�����ɤ�̵�� �ޤ��� ���ԡ��ե饰����(���ԡ�����ǤϤʤ�) ����
// ���ԡ�������¤������硢
// ���ԡ��ޡ�������ɽ��
if ( !$strReportPathName || ( !( $aryData["lngReportCode"] || $aryData["bytCopyFlag"] ) && fncCheckAuthority( DEF_FUNCTION_LO4, $objAuth ) ) )
{
	$copyDisabled = "hidden";
}


///////////////////////////////////////////////////////////////////////////
// Ģɼ�����ɤ����ξ�硢�ե�����ǡ��������
///////////////////////////////////////////////////////////////////////////
if ( $aryData["lngReportCode"] )
{
	if ( !$lngResultNum )
	{
		fncOutputError ( 9056, DEF_FATAL, "Ģɼ���ԡ�������ޤ���", TRUE, "", $objDB );
	}

	if ( !$aryHtml[] =  file_get_contents ( SRC_ROOT . "list/result/cash/" . $strReportPathName . ".tmpl" ) )
	{
		fncOutputError ( 9059, DEF_FATAL, "Ģɼ�ǡ����ե����뤬�����ޤ���Ǥ�����", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );
}

///////////////////////////////////////////////////////////////////////////
// �ƥ�ץ졼�Ȥ��֤������ǡ�������
///////////////////////////////////////////////////////////////////////////
else
{
	// ���Ѹ����ޥ����ǡ�������
	$aryEstimateData = fncGetEstimate( $aryData["strReportKeyCode"], $objDB );
	// �����ȡʥХåե��˼���
	$strBuffRemark	= $aryEstimateData["strRemark"];


	// ���Ѹ����Υǥե�����ͤ��Ф��������ͤμ���
	//������ۤ�Ф�����˼���Ǽ��/curReceiveProductPrice��������ɲ�
	$aryDefaultValue = fncGetEstimateDefaultValue( $aryData["strReportKeyCode"], $aryEstimateData["lngReceiveProductQuantity"], 
		$aryEstimateData["lngProductionQuantity"], $aryEstimateData["curProductPrice"], $aryRate, $objDB, $aryEstimateData["curReceiveProductPrice"]);

	list ( $aryDetail, $aryOrderDetail ) = fncGetEstimateDetail( $aryData["strReportKeyCode"], $aryEstimateData["strProductCode"], $aryRate, $aryDefaultValue, $objDB );

	list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, $aryOrderDetail, $aryDefaultValue, "list/result/e_detail.tmpl", "list/result/e_subject.tmpl", $objDB );

	unset ( $aryHiddenString );
	unset ( $aryRate );

	// ����Υޡ���
	$aryEstimateData = array_merge( $aryEstimateData, $aryCalculated );

	// ɸ�������
	$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

	// ����US�ɥ�졼�ȼ���
	$aryEstimateData["curConversionRate"] = fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

	// �׻���̤����
	$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

	// ����޽���
	$aryEstimateData = fncGetCommaNumber( $aryEstimateData );


	// ������
	$aryEstimateData["strRemarkDisp"]	= nl2br($strBuffRemark);


	// �١����ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "list/result/e_base.tmpl" );

	// �١����ƥ�ץ졼������
	$objTemplate->replace( $aryEstimateData );
	$objTemplate->replace( $aryDetail );
	$objTemplate->complete();

	// HTML����
	$aryHtml[] = $objTemplate->strTemplate;
}

echo join( "\n", $aryHtml );

$objDB->close();


return TRUE;
?>
