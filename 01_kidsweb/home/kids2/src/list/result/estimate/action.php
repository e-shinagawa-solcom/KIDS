<?
/** 
*	Ģɼ���� ���Ѹ����׻� ������λ����
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*/
// �����ץ�ӥ塼����( * �ϻ���Ģɼ�Υե�����̾ )
// listoutput.php -> strSessionID       -> action.php
// listoutput.php -> strReportKeyCode   -> action.php
// listoutput.php -> lngReportCode      -> action.php

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
$aryCheck["strReportKeyCode"]   = "null:number(0,99999999)";
$strTemplateFile = "p";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


// ���ꥭ�������ɤ�Ģɼ�ǡ��������
$strQuery = fncGetCopyFilePathQuery( DEF_REPORT_ESTIMATE, $aryData["strReportKeyCode"], $aryData["lngReportCode"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum === 1 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$strListOutputPath = $objResult->strreportpathname;
	unset ( $objResult );
	$objDB->freeResult( $lngResultID );
	//echo "���ԡ��ե�����ͭ�ꡣ";
}

// Ģɼ��¸�ߤ��ʤ���硢���ԡ�Ģɼ�ե��������������¸
elseif ( $lngResultNum === 0 )
{
	// ���Ѹ����ޥ����ǡ�������
	$aryEstimateData = fncGetEstimate( $aryData["strReportKeyCode"], $objDB );


	// �����ȡʥХåե��˼���
	$strBuffRemark	= $aryEstimateData["strRemark"];

//fncDebug( 'es_list.txt', $aryEstimateData, __FILE__, __LINE__);


	// ���Ѹ����Υǥե�����ͤ��Ф��������ͤμ���
	// 2005/06/10 ABE Yuuki
	//������ۤ�Ф�����˼���Ǽ��/curReceiveProductPrice��������ɲ�
	$aryDefaultValue = fncGetEstimateDefaultValue( $aryData["strReportKeyCode"], $aryEstimateData["lngReceiveProductQuantity"], 
		$aryEstimateData["lngProductionQuantity"], $aryEstimateData["curProductPrice"], $aryRate, $objDB , $aryEstimateData["curReceiveProductPrice"]);
	//old
	//$aryDefaultValue = fncGetEstimateDefaultValue( $aryData["strReportKeyCode"], $aryEstimateData["lngReceiveProductQuantity"], 
	//	$aryEstimateData["lngProductionQuantity"], $aryEstimateData["curProductPrice"], $aryRate, $objDB );

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

	$strBodyHtml = $objTemplate->strTemplate;

	// ---------------------------------------- modifyed by Kazushi Saito 2004/04/22 ��
	$strHtml = $strBodyHtml;
	// ---------------------------------------- modifyed by Kazushi Saito 2004/04/22 ��

	$objDB->transactionBegin();

	// ��������ȯ��
	$lngSequence = fncGetSequence( "t_Report.lngReportCode", $objDB );

	// Ģɼ�ơ��֥��INSERT
	$strQuery = "INSERT INTO t_Report VALUES ( $lngSequence, " . DEF_REPORT_ESTIMATE . ", " . $aryData["strReportKeyCode"] . ", '', '$lngSequence' )";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// Ģɼ�ե����륪���ץ�
	if ( !$fp = fopen ( SRC_ROOT . "list/result/cash/" . $lngSequence . ".tmpl", "w" ) )
	{
		list ( $lngResultID, $lngResultNum ) = fncQuery( "ROLLBACK", $objDB );
		fncOutputError ( 9059, DEF_FATAL, "Ģɼ�ե�����Υ����ץ�˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}

	// Ģɼ�ե�����ؤν񤭹���
	if ( !fwrite ( $fp, $strHtml ) )
	{
		list ( $lngResultID, $lngResultNum ) = fncQuery( "ROLLBACK", $objDB );
		fncOutputError ( 9059, DEF_FATAL, "Ģɼ�ե�����ν񤭹��ߤ˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}

	$objDB->transactionCommit();
	//echo "���ԡ��ե��������";
}
//echo "<script language=javascript>window.form1.submit();window.returnValue=true;window.close();</script>";
echo "<script language=javascript>parent.window.close();</script>";


$objDB->close();



return TRUE;
?>
