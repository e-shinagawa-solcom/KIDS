<?php

/**
*
*	@charset	: EUC-JP
*/

	require ( 'conf.inc' );										// �����ɤ߹���
	require ( LIB_DEBUGFILE );									// Debug�⥸�塼��

	require ( LIB_ROOT . "mapping/conf_mapping_common.inc" );	// �ޥåԥ����� - ����
	require ( LIB_ROOT . "mapping/conf_mapping_estimate.inc" );	// �ޥåԥ����� - ���Ѹ�������

	require ( LIB_FILE );										// �饤�֥���ɤ߹���
    
    require ( SRC_ROOT . "estimate/cmn/estimateSheetController.php" );
	require ( SRC_ROOT . "estimate/cmn/makeHTML.php" );

	// �ԥ��饹�ե�������ɤ߹���
	require_once (SRC_ROOT. "/estimate/cmn/productSalesRowController.php");
	require_once (SRC_ROOT. "/estimate/cmn/fixedCostSalesRowController.php");
	require_once (SRC_ROOT. "/estimate/cmn/fixedCostOrderRowController.php");
	require_once (SRC_ROOT. "/estimate/cmn/partsCostOrderRowController.php");
	require_once (SRC_ROOT. "/estimate/cmn/otherCostOrderRowController.php");

	require_once (SRC_ROOT. "/estimate/cmn/registHeaderController.php");

	require_once (SRC_ROOT. "/estimate/cmn/registOtherCellsController.php");

	require_once ( SRC_ROOT . "/estimate/cmn/estimateDB.php");
	
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
	$aryData	= $_REQUEST;

	$aryData["lngLanguageCode"]		= $_COOKIE["lngLanguageCode"];	// ���쥳����

    // ������̾����
    $sheetName = mb_convert_encoding($aryData['sheetname'], 'UTF-8', 'EUC-JP');

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
    
    // �ե�����������
    $file = array (
        'exc_name' => $aryData["exc_name"],
        'exc_type' => $aryData["exc_type"],
        'exc_tmp_name' => $aryData["exc_tmp_name"],
        'exc_error' => $aryData["exc_error"],
        'exc_size' => $aryData["exc_size"]
    );

    // �֥å��Υե�������������å�
	$fileCheckResult = estimateSheetController::checkFileFormat($file);

	// DB����ɸ��������
	$standardRateMaster = $objDB->getEstimateStandardRate();
    
	if ($fileCheckResult) {
        // �֥å��Υ���(phpSpreadSheet���֥������Ȥ˥֥å��ξ���򥻥åȤ���)
		$spreadSheet = $objReader->load($fileCheckResult);

		// ɬ�פ�������������
		$nameList = workSheetConst::getAllNameList();	
		$rowCheckNameList = workSheetConst::DETAIL_HEADER_CELL_NAME_LIST;
		$targetAreaList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

		// phpSpreadSheet���֥������Ȥ��饷���Ȥξ�������
		$allSheetInfo = estimateSheetController::getSheetInfo($spreadSheet, $nameList, $rowCheckNameList);

		$sheetInfo = $allSheetInfo[$sheetName];

		$objSheet = null;
		$outputMessage = array(); // ���ϥ�å�����

		$difference = array();
		$hiddenList = array();

		// ��������Ƚ������֥������ȤΥ��󥹥�������
		$objSheet = new estimateSheetController();

		// ���֥������Ȥ˥ǡ����򥻥åȤ���
		$objSheet->dataInitialize($sheetInfo, $objDB);

		// �����Ⱦ�����������
		$sheet = $sheetInfo['sheet'];
		$cellAddressList = $sheetInfo['cellAddress'];

		// �إå����ΥХ�ǡ�������Ԥ�
		$objHeader = new registHeaderController($objDB);
		$objHeader->initialize($sheetInfo['cellAddress'], $lngUserCode, $sheet);
		$message = $objHeader->validate();

		if ($message) {
			$outputMessage[] = $message;
		}

		// �إå������ͤ���Ϥ���
		$param = $objHeader->outputRegistData();
		$beforeProductionQuantity = $param[workSheetConst::PRODUCTION_QUANTITY];

		// �оݥ��ꥢ���ϰϤ��������
		$targetAreaRows = $objSheet->outputTargetAreaRows();
		$startRowOfDetail = $targetAreaRows[DEF_AREA_PRODUCT_SALES]['firstRow']; // ���٤γ��Ϲ�
		$endRowOfDetail = $targetAreaRows[DEF_AREA_OTHER_COST_ORDER]['lastRow']; // ���٤ν�λ��

		// ͢�����Ѥȴ��ǤιԤ���¸����ꥹ�Ȥ���������ʥ��饹�⤫�饰���Х��ѿ��Ȥ��ƻ��ѡ�
		$importCostRow = array();
		$tariffRow = array();
		
		$tariffRowList = array();
		$importCostRowList = array();

		$tariff = 0;
		$importCost = 0;

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
					$objRow->initialize($sheetInfo['cellAddress'], $row);

					// �ʲ��ν�����Ԥ��١�������ڤӤ���¾���ѤιԤν�������¤���ѤιԤν����θ�˹Ԥ�
					if ($rowAttribute === DEF_AREA_PARTS_COST_ORDER
					    || $rowAttribute === DEF_AREA_OTHER_COST_ORDER) {

						$calcProductionQuantity = productSalesRowController::outputProductionQuantity();
						$result = $objRow->substitutePQForPrice($beforeProductionQuantity, $calcProductionQuantity);

						if ($result === true) { // ���̤��������Ԥ�줿���ϥ֥å����ͤ�񤭴�����
							
							$quantityColumn =  $objRow->columnNumberList['quantity'];
							$quantityCell =  $quantityColumn. $row;
	
							// ������ο��̤�sheet���֥������Ȥ�����
							$objSheet->sheet->getCell($quantityCell)->setValue($calcProductionQuantity);
						}

					}

					// �ԤΥ����å����Ʒ׻���Ԥ�
					$objRow->workSheetRegistCheck();

					$divisionSubjectCode = $objRow->divisionSubjectCode;
					$classItemCode = $objRow->classItemCode;

					switch ($divisionSubjectCode) {
						case DEF_STOCK_SUBJECT_CODE_IMPORT_PARTS_COST:
						case DEF_STOCK_SUBJECT_CODE_OVERSEA_MOLD_DEPRECIATION:
						    $tariff = $tariff + $objRow->calculatedSubtotalJP;
					}
					// ͢�����ѡ����ǤˤĤ��Ƥϸ��̽�����Ԥ��١��оݤι��ֹ������˳�Ǽ����
					if ($objRow->invalidFlag === false) {
						if ($objRow->divisionSubjectCode === DEF_STOCK_SUBJECT_CODE_CHARGE) {
							if ($objRow->classItemCode === DEF_STOCK_ITEM_CODE_IMPORT_COST) {
								$importCostRowList[] = $row;
							} else if ($objRow->classItemCode === DEF_STOCK_ITEM_CODE_TARIFF) {
								$tariffRowList[] = $row;
							}
						}
					}

					$objRowList[$row] = $objRow;
					if ($objRow->invalidFlag === true) {
						$hiddenList[$row] = true;
					}
				}
			}
		}

		// ͢�����ѷ׻����ѿ��˴��Ƿ׻����ѿ�������
		$importCost = $tariff;

		// ���Ǥν���
		if ($tariffRowList) {
			foreach ($tariffRowList as $rowIndex) {
				$tariffObjRow = &$objRowList[$rowIndex];
				$tariffObjRow->chargeCalculate($tariff);
	
				if ($tariffObjRow->invalidFlag === false) {
					// ñ������
					$price = $tariffObjRow->price;
					$priceColumn = $tariffObjRow->columnNumberList['price'];
					$priceCell =  $priceColumn. $rowIndex;
	
					$subtotalColumn = $tariffObjRow->columnNumberList['subtotal'];
					$subtotalCell =  $subtotalColumn. $rowIndex;
	
					$deliveryColumn = $tariffObjRow->columnNumberList['delivery'];
					$deliveryCell = $deliveryColumn. $rowIndex;
	
					// �׻����ñ�������פ�sheet���֥������Ȥ�����
					$objSheet->sheet->getCell($priceCell)->setValue($price);
					$objSheet->sheet->getCell($subtotalCell)->setValue($tariffObjRow->calculatedSubtotalJP);
	
					// ͢�����ѷ׻����ѿ��˷׻���̤�û�
					$importCost += $tariffObjRow->calculatedSubtotalJP;
				}
			}
		}


		// ͢�����Ѥν���
		if ($importCostRowList) {
			foreach ($importCostRowList as $rowIndex) {
				$importCostObjRow = &$objRowList[$rowIndex];
				$importCostObjRow->chargeCalculate($importCost);
	
				if ($importCostObjRow->invalidFlag === false) {
					// ñ������
					$price = $importCostObjRow->price;
	
					$priceColumn =  $importCostObjRow->columnNumberList['price'];
					$priceCell =  $priceColumn. $rowIndex;
	
					$subtotalColumn = $importCostObjRow->columnNumberList['subtotal'];
					$subtotalCell =  $subtotalColumn. $rowIndex;
	
					$deliveryColumn = $importCostObjRow->columnNumberList['delivery'];
					$deliveryCell = $deliveryColumn. $rowIndex;
	
					// �׻����ñ����sheet���֥������Ȥ�����
					$objSheet->sheet->getCell($priceCell)->setValue($price);
					$objSheet->sheet->getCell($subtotalCell)->setValue($importCostObjRow->calculatedSubtotalJP);
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

		// �ԥ��֥������Ȥ��ˤ�������
		foreach ($objRowList as $row => $objRow) {
			$columnList = $objRow->columnNumberList;
			
			// ��å����������ɤμ���
			$messageOfConversionRate = $objRow->messageCode['conversionRate'];

			// �֥å���Ŭ�ѥ졼�Ȥ�DB���̲ߥ졼�ȤȰۤʤ��硢�ޤ��ϥ֥å��ξ��פ��׻���̤Ȱۤʤ���
			if ($messageOfConversionRate === DEF_MESSAGE_CODE_RATE_DIFFER) {
				// �֥å����֥������Ȥ��̲ߥ졼�Ȥ��ִ�
				$column = $columnList['conversionRate'];
				$convarsionRateCell = $column.$row;
				$acquiredRate = $objRow->acquiredRate;
				$objSheet->sheet->getCell($convarsionRateCell)->setValue($acquiredRate);
			}

			// �֥å����֥������Ȥξ��פ��ִ�
			$column = $columnList['subtotal'];
			$subtotalCell = $column.$row;
			$calculatedSubtotalJP = $objRow->calculatedSubtotalJP;

			$objSheet->sheet->getCell($subtotalCell)->setValue($calculatedSubtotalJP);
			
			if ($objRow->percentInputFlag !== true) {
				// DB�ξ�����ִ���ʻ����ζ���Ϥ���¾�ˤθܵ��衢���������򥻥åȤ���
				$column = $columnList['customerCompany'];
				$customerCompany = $objRow->customerCompany;
				$companyCell = $column.$row;
				$objSheet->sheet->getCell($companyCell)->setValue($customerCompany);
			} else {
				// �ѡ���������Ϥ���Ƥ�����ϱ��󤻤ˤ���
				$objSheet->setHorizontalRight($companyCell);
			}
		}

		// �Х�ǡ������ǥ��顼��ȯ���������ϥ��顼��å�������ɽ������
		if ( $outputMessage ) {
			$strMessage = '';
			foreach ($outputMessage as $messageList) {
				foreach ($messageList as $message) {
					if (!$strMessage) {
						$strMessage = "<div>". $message. "</div>";
					} else {
						$strMessage .= "<br>";
						$strMessage .= "<div>". $message. "</div>";
					}
				}
			}

			// [lngLanguageCode]�񤭽Ф�
			$aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

			// [strErrorMessage]�񤭽Ф�
			$aryHtml["strErrorMessage"] = mb_convert_encoding($strMessage, 'EUC-JP', 'UTF-8');

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

		$objCal = new registOtherCellsController();
		$objCal->calculateParam($objRowList, $objHeader, $sheetInfo['cellAddress'], $standardRateMaster);
		$calcData = $objCal->outputParam();

		foreach ($calcData as $cellName => $value) {
			$cellAddress = $cellAddressList[$cellName];
			$objSheet->sheet->getCell($cellAddress)->setValue($value);
		}

		// ��ɽ���ꥹ�ȡ�̵���ꥹ�ȡˤ��ɲä���
		$objSheet->setHiddenRowList($hiddenList);

		$viewData = $objSheet->makeDataOfSheet();

		$viewData = $objSheet->deleteInvalidRow($viewData);

		$viewDataList[0] = $viewData;

		$ws_num = 0;

		// ɽ���ѥǡ�����JSON���Ѵ�
		$json = json_encode($viewDataList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
		$json = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');

		// ��Ͽ�ѥǡ����μ���
		// �إå���
		$headerData = $objHeader->outputRegistData();
		// �ԥǡ���
		$index = 0;
		foreach ($objRowList as $objRow) {
			if ($objRow->invalidFlag === false) {
				++$index;
				$rowData = $objRow->outputRegistData();
				$rowDataList[$index] = $rowData;
			}
		}
	
		// ��Ͽ�ѥǡ���������
		$registData = array(
			'headerData' => $headerData,
			'rowDataList' => $rowDataList,
			'calculatedData' => $calcData
		);

		// JSON���Ѵ�
		$registJson = json_encode($registData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
		// ���֥륯�����ơ��������ִ������HTML��ͳ�ǥǡ������Ϥ��ݤ� ":���֥륯�����ơ������ �ν�ʣ�ˤ�äƥǡ������Ϥ��ʤ����Ȥ��ɤ�����)
		$registJson = str_replace('"', '/quot/', $registJson);

		$registJson = htmlspecialchars($registJson, ENT_QUOTES, 'UTF-8');

		// POST�ѥǡ����˥��å�
		$aryData['registJson'] = $registJson;

		// Excel���������HTML����
		$strExcel       .= "<div class=\"sheetHeaderConfirm\" id=\"sheet". $ws_num. "\">";
		$strExcel       .= "<br>";
		$strExcel		.= makeHTML::getWorkSheet2HTML($aryData['sheetname'], $ws_num, "confirm", $data); // �إå���
		$strExcel       .= "</div>";
		$strExcel		.= makeHTML::getGridTable($ws_num); // �ǡ�����������

		$css_rowstyle .= '.rowstyle'.$ws_num.' { display:none;}'."\n";

		$strExcel = str_replace('_%css_rowstyle%_', $css_rowstyle, $strCSS). $strExcel;

		// ������FORM�ǡ�������
		$form = makeHTML::getHiddenFileData($file);
		
		$form .= makeHTML::getHiddenData($aryData);

		$aryData["WORKSHEET"]	= $select; // ��������������
		$aryData["EXCEL"]		= $strExcel; // index
		$aryData["TABLEDATA"]	= $json;
		$aryData["FORM_NAME"]	= FORM_NAME;
		$aryData["FORM"]	    = $form;

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate->getTemplate( "estimate/regist/confirm.tmpl" );

		// �ƥ�ץ졼������
		$objTemplate->replace( $aryData );
		$objTemplate->complete();

		// HTML����
		echo $objTemplate->strTemplate;
		return;
	}

?>
