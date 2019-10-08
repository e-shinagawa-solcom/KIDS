<?php

/**
*
*	@charset	: EUC-JP
*/

require ( 'conf.inc' );										// �����ɤ߹���
require ( LIB_DEBUGFILE );									// Debug�⥸�塼��

require ( LIB_FILE );										// �饤�֥���ɤ߹���

require ( SRC_ROOT . "estimate/cmn/estimateSheetController.php" );
require ( SRC_ROOT . "estimate/cmn/makeHTML.php" );

require_once ( SRC_ROOT . "/estimate/cmn/estimateDB.php");
require_once ( SRC_ROOT . "/estimate/cmn/estimatePreviewController.php");

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

$objDB			= new estimateDB();
$objAuth		= new clsAuth();
$objTemplate	= new clsTemplate();								// �ƥ�ץ졼�ȥ��֥�����������

$charset = 'EUC-JP';

$objReader      = new XlsxReader();

//-------------------------------------------------------------------------
// DB�����ץ�
//-------------------------------------------------------------------------
$objDB->InputEncoding = 'UTF-8';
$objDB->open( "", "", "", "" );

//-------------------------------------------------------------------------
// �ѥ�᡼������
//-------------------------------------------------------------------------
$aryData	= array();
$aryData	= $_POST;

//-------------------------------------------------------------------------
// ����ʸ�����͡����å���󡦸��¥����å�
//-------------------------------------------------------------------------

// ʸ��������å�
$aryCheck["strSessionID"]	= "null:numenglish(32,32)";
$aryResult	= fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// �桼���������ɼ���
$lngUserCode = $objAuth->UserCode;

// ���³�ǧ
if( !fncCheckAuthority( DEF_FUNCTION_E4, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ���¥��롼�ץ����ɤμ���
$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

// GET�ѥ�᡼�����ѥ�᡼�������
$estimateNo = $aryData['estimateNo']; // ���ʥ�����

// ��ӥ�����ֹ�μ���
if (isset($_POST['revisionNo'])) {
	$revisionNo = $_POST['revisionNo'];
	$estimate = $objDB->getEstimateDetail($estimateNo, $revisionNo);
} else {
	// ��ӥ�����ֹ椬POST����ʤ��ä����Ϻǿ��Υǡ������������
	$estimate = $objDB->getEstimateDetail($estimateNo);
}

$firstEstimateDetail = current($estimate);

if (!isset($revisionNo)) {
	$revisionNo = $firstEstimateDetail->lngrevisionno;
}

// ����Υ�ӥ�����ֹ�μ���
if (isset($_POST['maxRevisionNo'])) {
	$maxRevisionNo = $_POST['maxRevisionNo'];
} else {
	$result = $objDB->getEstimateDetail($estimateNo);
	if ($result) {
		$firstRecord = current($result);
		$maxRevisionNo = $firstRecord->lngrevisionno;
	}
}

$productCode = $firstEstimateDetail->strproductcode;
$reviseCode = $firstEstimateDetail->strrevisecode;
$productRevisionNo = $firstEstimateDetail->lngproductrevisionno;

// ���ʥޥ����ξ������
$product = $objDB->getProduct($productCode, $reviseCode, $productRevisionNo);

$objPreview = new estimatePreviewController();
$objPreview->dataInitialize($product, $estimate, $objDB);

$productData = $objPreview->getProduct();

$estimateData = $objPreview->getEstimate();


$tempFilePath = EXCEL_TMP_ROOT. 'workSheetTmp.xlsx';

$spreadSheet = $objReader->load($tempFilePath);

// ɬ�פ�������������
$nameList = workSheetConst::getAllNameListForDownload();
$rowCheckNameList = workSheetConst::DETAIL_HEADER_CELL_NAME_LIST;
$targetAreaList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

// phpSpreadSheet���֥������Ȥ��饷���Ȥξ�������
$allSheetInfo = estimateSheetController::getSheetInfo($spreadSheet, $nameList, $rowCheckNameList);

$sheetInfo = estimateSheetController::getFirstElement($allSheetInfo);

if ($sheetInfo['displayInvalid']) {
	// �ƥ�ץ졼���ѤΥ����Ȥ�̵���ˤʤäƤ��ʤ���硢���顼����Ϥ���
	if ( !$sheetDataList ) {
		$strMessage = '�ƥ�ץ졼�Ȱ۾�';

		// [strErrorMessage]�񤭽Ф�
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
}

$objSheet = null;

// �����Ȥ�ɽ��̵���Ǥʤ����ϥ�������Ƚ������֥������ȤΥ��󥹥�������
$objSheet = new estimateSheetController();

// ���֥������Ȥ˥ǡ����򥻥åȤ���
$objSheet->dataInitialize($sheetInfo, $objDB);

// phpSpreadSheet���������������ȥ��֥������Ȥ򥰥��Х뻲���Ѥ˥��åȤ���
$sheet = $sheetInfo['sheet'];
$cellAddressList = $sheetInfo['cellAddress'];

// �ƥ�ץ졼�Ȥ�������Ԥ�
$objSheet->templateAdjust($estimateData);

// ��������ȥ��֥������Ȥ�ɬ�פ��ͤ򥻥å�
$objSheet->setDBEstimateData($productData, $estimateData);

$hiddenList = array();

// ��ɽ���ꥹ�ȡ�̵���ꥹ�ȡˤ��ɲä���
$objSheet->setHiddenRowList($hiddenList);

$viewData = $objSheet->makeDataOfSheet();

$viewDataList[0] = $viewData;

$ws_num = 0;

//-------------------------------------------------------------------------
// DB Close
//-------------------------------------------------------------------------
$objDB->close();
$objDB->freeResult( $lngResultID );

// data��JSON���Ѵ�
$json = json_encode($viewDataList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$json = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');

// Excel���������HTML����
$strExcel .= makeHTML::getGridTable($ws_num); // �ǡ�����������
$aryData['revisionNo'] = $revisionNo;

$formData = array(
	'strSessionID' => $aryData["strSessionID"],
	'productCode' => $productCode,
	'reviseCode' => $reviseCode,
	'revisionNo' => $revisionNo,
	'estimateNo' => $estimateNo,
);

// ������FORM�ǡ�������
$form .= makeHTML::getHiddenData($formData);

$aryData["HEADER"]      = $header;
$aryData["EXCEL"]		= $strExcel; // index
$aryData["TABLEDATA"]	= $json;
$aryData["FORM"]	    = $form;

// �ƥ�ץ졼���ɤ߹���
$objTemplate->getTemplate( "estimate/delete/parts.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;

?>
