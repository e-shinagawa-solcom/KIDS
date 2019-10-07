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
	require_once ( SRC_ROOT . "estimate/cmn/registInsertData.php");

	// ���Ѹ���DB���饹�ɤ߹���
    require_once ( SRC_ROOT . "estimate/cmn/estimateDB.php");

	$objDB   = new estimateDB();
	$objAuth = new clsAuth();
	
	$objDB->open( "", "", "", "" );

	// POST�ǡ�������
	$aryData = $_POST;

// fncDebug( 'estimate_regist_action_data.txt', $aryData["aryDetail"], __FILE__, __LINE__);

	$aryCheck["strSessionID"] = "null:numenglish(32,32)";

	// ʸ��������å�
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );
	unset ( $aryCheck );


	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode = $objAuth->UserCode;


	// ���³�ǧ
	//////////////////////////////////////////////////////////////////////////
	// ���Ѹ�����Ͽ�ξ��
	//////////////////////////////////////////////////////////////////////////
	if ($aryData["lngFunctionCode"] == DEF_FUNCTION_E1 && fncCheckAuthority( DEF_FUNCTION_E1, $objAuth )) {
		
	}
	//////////////////////////////////////////////////////////////////////////
	// ����ʳ�(����ERROR)
	//////////////////////////////////////////////////////////////////////////
	else {
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	

//////////////////////////////////////////////////////////////////////
// DB��������
//////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

// ��Ͽ�ǡ����μ���
$registJson = str_replace('/quot/', '"', $aryData['registJson']);
$regist = json_decode($registJson, true);
mb_convert_variables('EUC-JP', 'UTF-8', $regist);

unset($aryData['registJson']);
unset($registJson);

$objRegist = new registInsertData();

// ��Ͽ��ɬ�פʥǡ����򥻥åȤ���
$objRegist->setRegistParam($regist, $objAuth->UserCode, $objDB);

// ���Ѹ�����Ͽ�ξ�硢INSERT
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 ) {
	$objRegist->regist();
}

$objDB->transactionCommit();

//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////

// ���Ѹ�������ξ��
if( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 )
{
	$sessionID = $aryData['strSessionID'];
	$productCode = $objRegist->getProductCode();
	$reviseCode = $objRegist->getReviseCode();
	$estimateNo = $objRegist->getEstimateNo();

	if ($reviseCode === '00') {
		$completeMessage = "���ʥ�����". $productCode. "�򿷵���Ͽ�������ޤ�����";
	} else {
		$completeMessage = "���ʥ�����". $productCode. "��". $productCode. "_". $reviseCode. "�Ȥ��ƺ�����Ͽ�������ޤ�����";
	}

	$postData = array(
		'strSessionID' => $sessionID,
		'productCode' => $productCode,
		'reviseCode' => $reviseCode,
		'estimateNo' => $estimateNo,
		'completeMessage' => $completeMessage
	);

	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "estimate/regist/result.tmpl" );

	$objTemplate->replace( $postData );
	$objTemplate->complete();

//fncDebug( 'es_finish.txt', $objTemplate->strTemplate, __FILE__, __LINE__);
	echo $objTemplate->strTemplate;
}


$objDB->close();


return TRUE;
?>
