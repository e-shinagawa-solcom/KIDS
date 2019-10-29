<?php
/** 
*	���Ѹ������� �¹Բ���
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/

	mb_http_output ( 'EUC-JP' );


	require_once ('conf.inc');
	require_once ( LIB_DEBUGFILE );

	// �饤�֥���ɤ߹���
	require_once ( LIB_FILE );

	// ���Ѹ����ѥ��饹�ɤ߹���
	require_once ( SRC_ROOT . "estimate/cmn/const/workSheetConst.php");
	require_once ( SRC_ROOT . "estimate/cmn/deleteInsertData.php");

	// ���Ѹ���DB���饹�ɤ߹���
	require_once ( SRC_ROOT . "estimate/cmn/estimateDB.php");
	
	// html�������饹�ɤ߹���
	require_once ( SRC_ROOT . "estimate/cmn/makeHTML.php");

	$objDB   = new estimateDB();
	$objAuth = new clsAuth();
	
	$objDB->open( "", "", "", "" );

	// POST�ǡ�������
	$aryData = $_POST;

// fncDebug( 'estimate_regist_action_data.txt', $aryData["aryDetail"], __FILE__, __LINE__);

	$aryCheck["strSessionID"] = "null:numenglish(32,32)";

	// ʸ��������å�
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	
	// ���顼��å���������Ϥ���
	$strErrorMessage = array();

	foreach ( $aryCheckResult as $value ) {
		if ($value)	{
			list ($lngErrorNo, $errorReplace) = explode (":", $value);
			$strErrorMessage[] = fncOutputError ( $lngErrorNo, DEF_ERROR, $errorReplace, FALSE, "", $objDB );
		}
	}


	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode = $objAuth->UserCode;

	$functionCode = $aryData["lngFunctionCode"];


	// ���³�ǧ
	//////////////////////////////////////////////////////////////////////////
	// ���Ѹ�������ʳ��ξ��
	//////////////////////////////////////////////////////////////////////////
	if (!$functionCode == DEF_FUNCTION_E4 || !fncCheckAuthority( $functionCode, $objAuth )) {
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

// ������븫�Ѹ����ξ�������
$estimateNo = $aryData['estimateNo'];
$revisionNo = $aryData['revisionNo'];

$estimate = $objDB->getEstimateDetail($estimateNo);

//////////////////////////////////////////////////////////////////////
// DB��������
//////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

$objRegist = new deleteInsertData();

// ��Ͽ��ɬ�פʥǡ����򥻥åȤ���
$objRegist->setDeleteParam($estimateNo, $revisionNo, $lngUserCode, $objDB);

$strErrorMessage = $objRegist->delete();

// �����ǥ��顼��ȯ�������饨�顼��å��������ϲ��̤����ܤ���
if ($strErrorMessage) {
	makeHTML::outputErrorWindow($strErrorMessage);
}

$objDB->transactionCommit();

//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////


$completeMessage = "���ʥ�����". $productCode. "_". $reviseCode. "�θ��Ѹ����ǡ��������������ޤ�����";

$postData = array(
	'completeMessage' => $completeMessage
);

$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "estimate/delete/result.tmpl" );

$objTemplate->replace( $postData );
$objTemplate->complete();

//fncDebug( 'es_finish.txt', $objTemplate->strTemplate, __FILE__, __LINE__);

echo $objTemplate->strTemplate;


$objDB->close();


return TRUE;
?>
