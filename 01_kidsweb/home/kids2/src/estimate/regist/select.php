<?php

require ( 'conf.inc' );										// �����ɤ߹���
require ( LIB_DEBUGFILE );									// Debug�⥸�塼��

require ( LIB_ROOT . "mapping/conf_mapping_common.inc" );	// �ޥåԥ����� - ����
require ( LIB_ROOT . "mapping/conf_mapping_estimate.inc" );	// �ޥåԥ����� - ���Ѹ�������

require ( LIB_FILE );										// �饤�֥���ɤ߹���

require ( SRC_ROOT . "estimate/cmn/estimateSheetController.php" );
require ( SRC_ROOT . "estimate/cmn/makeHTML.php" );

// ����ե�������ɤ߹���
require_once ( SRC_ROOT . "/estimate/cmn/const/workSheetConst.php");

// �ԥ��饹�ե�������ɤ߹���
require_once (SRC_ROOT. "/estimate/cmn/productSalesRowController.php");
require_once (SRC_ROOT. "/estimate/cmn/fixedCostSalesRowController.php");
require_once (SRC_ROOT. "/estimate/cmn/fixedCostOrderRowController.php");
require_once (SRC_ROOT. "/estimate/cmn/partsCostOrderRowController.php");
require_once (SRC_ROOT. "/estimate/cmn/otherCostOrderRowController.php");

require_once ( SRC_ROOT . "/estimate/cmn/estimateDB.php");

// $charset='utf-8';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;


$objDB			= new estimateDB();
$objAuth		= new clsAuth();
$objTemplate	= new clsTemplate();								// �ƥ�ץ졼�ȥ��֥�����������
$objReader      = new XlsxReader();                                 // phpSpreadSheet��Reader

//-------------------------------------------------------------------------
// DB�����ץ�
//-------------------------------------------------------------------------
$objDB->open( "", "", "", "" );


//-------------------------------------------------------------------------
// �ѥ�᡼������
//-------------------------------------------------------------------------
// $aryData	= $_REQUEST; POST��GET���̿�����ǡ������ʤ����ᥳ���ȥ�����

$aryData["lngLanguageCode"]		= $_COOKIE["lngLanguageCode"];	// ���쥳����
$aryData["lngFunctionCode"]		= DEF_FUNCTION_E1;				// ���������ɡʸ��Ѹ�����
$aryData["strSessionID"]        = $_COOKIE["strSessionID"];

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");
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
if( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
{
    fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ���¥��롼�ץ����ɤμ���
$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );



if( $_FILES )
{
    $file = array();
    // �ƥ�ݥ��ե�����������ե�����̾����
    $strTmpFileName	= getTempFileName($_FILES['excel_file']['tmp_name'] );

    // �ե��������μ���
    $file["exc_name"]			= $_FILES['excel_file']['name'];
    $file["exc_type"]			= $_FILES['excel_file']['type'];
    $file["exc_tmp_name"]		= $strTmpFileName;
    $file["exc_error"]			= $_FILES['excel_file']['error'];
    $file["exc_size"]			= $_FILES['excel_file']['size'];

    $file["lngRegistConfirm"]	= 1;	// ��ǧ����ɽ���ե饰
}

// �ե���������å�
$fileCheckResult = estimateSheetController::checkFileFormat($file);

// DB�����̲ߤ�ɽ��̾����
$monetaryUnitList = $objDB->getMonetaryUnitList();

// DB����ɸ��������
$standardRateMaster = $objDB->getEstimateStandardRate();

// ��������ե�����Υ���
if ($fileCheckResult) {
	// �֥å��Υǡ�����phpSpreadSheet���֥������Ȥ�Ÿ��
	$spreadSheet = $objReader->load($fileCheckResult);

	// ɬ�פ�������������
	$nameList = workSheetConst::getAllNameList();	
	$rowCheckNameList = workSheetConst::DETAIL_HEADER_CELL_NAME_LIST;
	$targetAreaList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

    // phpSpreadSheet���֥������Ȥ��饷���Ȥξ�������
	$allSheetInfo = estimateSheetController::getSheetInfo($spreadSheet, $nameList, $rowCheckNameList);

	if ($allSheetInfo) {
		// �������ֹ�ν���ͤ�0�����ꤹ��
		$sheetNumber = 0;
		// ���顼�����å���
		foreach ($allSheetInfo as $sheetName => $sheetInfo) {
			$objSheet = null;
			$outputMessage = array(); // ���ϥ�å�����

			$difference = array();
			$hiddenList = array();
			
			if ($sheetInfo['displayInvalid']) {
				continue;
			} else {
				// ͢�����ѷ׻����ѿ��ʴ��Ǥξ��פι�ס�
				$tariffTotal = 0;
				
				// �����Ȥ�ɽ��̵���Ǥʤ����ϥ�������Ƚ������֥������ȤΥ��󥹥�������
				$objSheet = new estimateSheetController();

				// ���֥������Ȥ˥ǡ����򥻥åȤ���
				$objSheet->dataInitialize($sheetInfo, $objDB);

				// phpSpreadSheet���������������ȥ��֥������Ȥ򥰥��Х뻲���Ѥ˥��åȤ���
				$sheet = $objSheet->sheet;

				$cellAddressList = $sheetInfo['cellAddress'];

				// �оݥ��ꥢ���ϰϤ��������
				$targetAreaRows = $objSheet->outputTargetAreaRows();
				$startRowOfDetail = $targetAreaRows[DEF_AREA_PRODUCT_SALES]['firstRow']; // ���٤γ��Ϲ�
				$endRowOfDetail = $targetAreaRows[DEF_AREA_OTHER_COST_ORDER]['lastRow']; // ���٤ν�λ��
			
				for ($row = $startRowOfDetail; $row <= $endRowOfDetail; ++$row) {

					$objRow = null;
					// ���ߤιԤ��ɤ��оݥ��ꥢ��°���뤫Ƚ���Ԥ�
					$rowAttribute = $objSheet->checkAttributeRow($row);
					
					if ($rowAttribute) {
						// �оݥ��ꥢ�ˤ�äƥ��󥹥��󥹺������Υ��饹����ꤹ��
						switch ($rowAttribute) {
							case DEF_AREA_PRODUCT_SALES:
								$objRow = new productSalesRowController($objDB);
								break;
							case DEF_AREA_FIXED_COST_SALES:
								$objRow = new fixedCostSalesRowController($objDB);
								break;
							case DEF_AREA_FIXED_COST_ORDER:
								$objRow = new fixedCostOrderRowController($objDB);
								break;
							case DEF_AREA_PARTS_COST_ORDER:
								$objRow = new partsCostOrderRowController($objDB);
								break;
							case DEF_AREA_OTHER_COST_ORDER;
								$objRow = new otherCostOrderRowController($objDB);
								break;
							default:
								break;
						}
						if ($objRow) {
							$objRow->initialize($cellAddressList, $row);
							$objRow->workSheetSelectCheck();

							$objRowList[$row] = $objRow;
							if ($objRow->invalidFlag === true) {
								$hiddenList[$row] = true;
							}							
						}
					}
				}

				// �ԥ��֥������Ȥ��ˤ�������
				foreach ($objRowList as $row => $objRow) {
					$columnList = $objRow->columnNumberList;
					
					// ��å����������ɤμ���
					$messageOfConversionRate = $objRow->messageCode['conversionRate'];

					// �֥å���Ŭ�ѥ졼�Ȥ�DB���̲ߥ졼�ȤȰۤʤ��硢�ޤ��ϥ֥å��ξ��פ��׻���̤Ȱۤʤ���
					if ($messageOfConversionRate) {
					// �֥å���Ŭ�ѥ졼�Ȥ�DB���̲ߥ졼�ȤȰۤʤ���Ϻ�ʬɽ�Υǡ������������
						$delivery = $objRow->delivery;
						$monetary = $objRow->monetary;
						$acquiredRate = $objRow->acquiredRate;
						$conversionRate = $objRow->conversionRate;
						$monetaryUnit = $monetaryUnitList[$monetary];
						if ($messageOfConversionRate === DEF_MESSAGE_CODE_RATE_DIFFER) {
							// �̲ߥ졼�Ⱥ�ʬɽ�Υǡ�������
							$difference[] = array(
								'delivery' => $delivery,
								'monetary' => $monetaryUnit,
								'temporaryRate' => $acquiredRate ? $acquiredRate : '-',
								'sheetRate' => $conversionRate ? $conversionRate : '-',
							);
						} else if ($messageOfConversionRate === DEF_MESSAGE_CODE_RATE_DIFFER) {
							$notFound[] = array(
								'delivery' => $delivery,
								'monetary' => $monetaryUnit,
								'sheetRate' => $conversionRate ? $conversionRate : '-',
							);						                        
						}
					}
				}

				// ɸ����Υ����å�
				$standardRateCell = $cellAddressList[workSheetConst::STANDARD_RATE];
				$standardRate = $objSheet->sheet->getCell($standardRateCell)->getCalculatedValue();
				if ($standardRateMaster != $standardRate) {
					$companyLocalRate = $standardRateMaster ? number_format(($standardRateMaster * 100), 2, '.', ''). "%" : '-';
					$sheetRate = $standardRate ? number_format(($standardRate * 100), 2, '.', ''). "%" : '-';
					$difference[] = array(
						'delivery' => '-',
						'monetary' => 'ɸ����',
						'temporaryRate' => $companyLocalRate,
						'sheetRate' => $sheetRate
					);
				}
				
				// ��ʣ����ǡ�����������
				if ($difference) {
					$difference = array_unique($difference, SORT_REGULAR);
					$differenceMessage = fncOutputError ( DEF_MESSAGE_CODE_RATE_DIFFER, DEF_WARNING, "", false, "", $objDB );
				}
				
				if ($notFound) {
					$notFound = array_unique($notFound, SORT_REGULAR);
					$notFoundMessage = fncOutputError ( DEF_MESSAGE_CODE_RATE_DIFFER, DEF_WARNING, "", false, "", $objDB );
				}
			
				// ��ɽ���ꥹ�ȡ�̵���ꥹ�ȡˤ��ɲä���
				$objSheet->setHiddenRowList($hiddenList);

				// ���������ɽ���ѤΥǡ�������Ϥ���
				$viewData = $objSheet->makeDataOfSheet();

				// ���������̾��'EUC-JP'�˥��󥳡��ɤ���
				$strWSName = mb_convert_encoding($sheetName, "EUC-JP", "UTF-8");

				$strExcel       .= "<div class=\"sheetHeader\" id=\"sheet". $sheetNumber. "\">";
				$strExcel       .= makeHTML::makeDifferenceRateTable($difference, $differenceMessage);
				$strExcel       .= "<br>";
				$strExcel       .= makeHTML::makeNotFoundRateTable($notFound, $notFoundMessage);
				$strExcel       .= "<br>";
				// $strExcel       .= makeHTML::makeWarningHTML($outputMessage);
				$strExcel       .= "<br>";
				$strExcel		.= makeHTML::getWorkSheet2HTML($strWSName, $sheetNumber, "select"); // �إå���
				$strExcel       .= "</div>";

				$strExcel		.= makeHTML::getGridTable($sheetNumber); // �ǡ�����������

				$css_rowstyle .= '.rowstyle'. $sheetNumber.' { display:none;}'."\n";
			}
			
			if ($objSheet) {
				$sheetDataList[$sheetName] = array(
					'objSheet' => $objSheet,
					'objRowList' => $objRowList
				);
				$sheetNameList[] = $sheetName;
				$viewDataList[] = $viewData;

				++$sheetNumber;
			}
		}
	}	
}

//-------------------------------------------------------------------------
// DB Close
//-------------------------------------------------------------------------
$objDB->close();
$objDB->freeResult( $lngResultID );

// ͭ���ʥ����Ȥ�¸�ߤ��ʤ����ϥ��顼��å�������ɽ������
if ( !$allSheetInfo ) {
	$strMessage = 'ͭ���ʥ����Ȥ�¸�ߤ��ޤ���';

	// [lngLanguageCode]�񤭽Ф�
	$aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

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

// data��JSON���Ѵ�
$json = json_encode($viewDataList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$json = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');

// ���쥯�ȥܥå������ͤ����
$select = makeHTML::getOptionsList($sheetNameList);

// ������FORM�ǡ�������
$form = makeHTML::getHiddenFileData($file);
$form .= makeHTML::getHiddenData($aryData);

//
$aryData["WORKSHEET"]	= $select; // ��������������
$aryData["EXCEL"]		= $strExcel; // index
$aryData["TABLEDATA"]	= $json;
$aryData["FORM_NAME"]	= FORM_NAME;
$aryData["FORM"]	    = $form;

// �ƥ�ץ졼���ɤ߹���
$objTemplate->getTemplate( "estimate/regist/select.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;
return;
?>
