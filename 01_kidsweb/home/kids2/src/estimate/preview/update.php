<?php

/**
*
*	@charset	: EUC-JP
*/

	require ( 'conf.inc' );										// �����ɤ߹���
	require ( LIB_DEBUGFILE );									// Debug�⥸�塼��

	require ( LIB_FILE );										// �饤�֥���ɤ߹���

	require ( SRC_ROOT . "estimate/cmn/makeHTML.php" );

	// �ԥ��饹�ե�������ɤ߹���
	require_once (SRC_ROOT. "/estimate/cmn/productSalesRowController.php");
	require_once (SRC_ROOT. "/estimate/cmn/fixedCostSalesRowController.php");
	require_once (SRC_ROOT. "/estimate/cmn/fixedCostOrderRowController.php");
	require_once (SRC_ROOT. "/estimate/cmn/partsCostOrderRowController.php");
	require_once (SRC_ROOT. "/estimate/cmn/otherCostOrderRowController.php");

	unset($__composer_autoload_files);

	require_once (SRC_ROOT. "/estimate/cmn/updateHeaderController.php");

	require_once (SRC_ROOT. "/estimate/cmn/updateOtherCellsController.php");

	require_once ( SRC_ROOT . "/estimate/cmn/estimateDB.php");
	require_once ( SRC_ROOT . "/estimate/cmn/estimatePreviewController.php");

	require_once ( SRC_ROOT . "estimate/cmn/updateInsertData.php");
	
	require_once (SRC_ROOT. "/estimate/cmn/updateOtherCellsController.php");

	$objDB			= new estimateDB();
	$objAuth		= new clsAuth();
	$objTemplate	= new clsTemplate();								// �ƥ�ץ졼�ȥ��֥�����������

	$charset = 'EUC-JP';

	//-------------------------------------------------------------------------
	// DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB->InputEncoding = 'EUC-JP';
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

	// ��ǽ������
	$functionCode = DEF_FUNCTION_E3;

	// ���³�ǧ
	if( !fncCheckAuthority( $functionCode, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// ���¥��롼�ץ����ɤμ���
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );



    // ���ϥǡ��������
	$postDataJson = mb_convert_encoding($aryData['postData'], 'UTF-8', 'EUC-JP'); // JSON�ǥ������Ѥ�UTF8���Ѵ�
	$escapeJson = str_replace('/', '\\/', $postDataJson); // '/'�ʥ���å���)�򥨥������פ���

	$postData = json_decode($escapeJson, true);
	// mb_convert_variables('EUC-JP', 'UTF-8', $regist); // EUC-JP���Ѵ�


	$value = $postData['value']; // ������
	$class = $postData['class']; // �����ͤ����ơʥ���ˤ�ɳ�դ��뤿��Υ��饹�Υꥹ��
	$estimateDetailNo = $postData['estimateDetailNo']; // ���Ѹ��������ֹ�ȹ��ֹ��ɳ�դ��뤿��Υꥹ��

	// �إå�����̾�Υꥹ�Ȥ����
	$headerNameList = workSheetConst::WORK_SHEET_HEADER_DATA_CELL; // �֥å��Υإå�
	$detailHeader = workSheetConst::DETAIL_HEADER_CELL_NAME_LIST;  // ���ٹԤΥإå��ʥ����ȥ�ԡ�

	// ���饹̾���饨�ꥢ�򸡺����뤿�������ɽ��������
	$areaReg = '/'. workSheetConst::AREA_CLASS_STRING. '([\d])+/';

	// �֥å�����̲�̾���̲ߥ����ɤ��Ѵ����뤿������������
	$monetaryExchange = workSheetConst::MONETARY_UNIT_WORKSHEET;

	$headerKey = array_flip(workSheetConst::WORK_SHEET_HEADER_DATA_CELL);

	// ���饹̾�θ���
	foreach ($class as $classInfo) {
		$className = $classInfo['className'];
		$row = $classInfo['row'];
		$col = $classInfo['col'];
		if (preg_match($areaReg, $className, $match)) { // ���ٹԤΥǡ�������
			$areaCode = $match[1];
			$nameList = $detailHeader[$areaCode];
			foreach ($nameList as $index => $headerCellName) {
				if (strpos($className, $index)) {
					$param = mb_convert_encoding($value[$row][$col], 'EUC-JP', 'UTF-8');
					switch ($index) {
						case 'monetary':						
							if (!$param) { // �̲ߥ����ɤ����åȤ���Ƥ��ʤ�����JP�򥻥åȤ���
								$param = 'JP';
							}
							$param = $monetaryExchange[$param];
							break;
						case 'subtotal':
							$param = str_replace('\\', '', $param);
							break;
						case 'customerCompany':
						    if (is_numeric($param)) {
								$param = $param / 100;
							}
						default:
							break;
					}

					$rowParam[$areaCode][$row][$index] = $param; // ����Υǡ�������
					
				}
			}
		} else { // ����¾�Υǡ�������
			foreach ($headerNameList as $cellName) {
				if (strpos($className, $cellName) !== false) {
					$param = mb_convert_encoding($value[$row][$col], 'EUC-JP', 'UTF-8');
					if ($cellName === workSheetConst::RETAIL_PRICE) {
						$param = str_replace('\\', '', $param);
						$param = str_replace(',', '', $param);
					}
					$paramKey = $headerKey[$cellName];
					$headerParam[$paramKey] = $param;
					break;
				}
			}
		}
	}

	// �إå����ν���
	$objHeader = new updateHeaderController($objDB);
	$objHeader->initialize($lngUserCode, $headerParam);
	$message = $objHeader->validate();

	if ($message) {
		$outputMessage[] = $message;
	}

	// ���ٹԤν���
	foreach ($rowParam as $areaCode => $rowParams) {
		foreach ($rowParams as $row => $params) {
			switch ($areaCode) {
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
			$objRow->editInitialize($params, $row);

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
		}	
	}

	// ���ֹ�ǥ����Ȥ���
	ksort($objRowList);

	// ͢�����ѷ׻����ѿ��˴��Ƿ׻����ѿ�������
	$importCost = $tariff;

	// ���Ǥν���
	if ($tariffRowList) {
		foreach ($tariffRowList as $rowIndex) {
			$tariffObjRow = &$objRowList[$rowIndex];
			$tariffObjRow->chargeCalculate($tariff);
	
			if ($tariffObjRow->invalidFlag === false) {
				// ñ������
				$priceColumn = $tariffObjRow->columnNumberList['price'];
				$priceCell =  $priceColumn. $rowIndex;
	
				$subtotalColumn = $tariffObjRow->columnNumberList['subtotal'];
				$subtotalCell =  $subtotalColumn. $rowIndex;
	
				$deliveryColumn = $tariffObjRow->columnNumberList['delivery'];
				$deliveryCell = $deliveryColumn. $rowIndex;
	
				// ͢�����ѷ׻����ѿ��˷׻���̤�û�
				$importCost += $tariffObjRow->calculatedSubtotalJP;
			}
		}
	}

	unset($tariffRowList);


	// ͢�����Ѥν���
	if ($importCostRowList) {
		foreach ($importCostRowList as $rowIndex) {
			$importCostObjRow = &$objRowList[$rowIndex];
			$importCostObjRow->chargeCalculate($importCost);
	
			if ($importCostObjRow->invalidFlag === false) {
				// ñ������	
				$priceColumn =  $importCostObjRow->columnNumberList['price'];
				$priceCell =  $priceColumn. $rowIndex;
	
				$subtotalColumn = $importCostObjRow->columnNumberList['subtotal'];
				$subtotalCell =  $subtotalColumn. $rowIndex;
	
				$deliveryColumn = $importCostObjRow->columnNumberList['delivery'];
				$deliveryCell = $deliveryColumn. $rowIndex;
	
			}
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
//		$aryHtml["strErrorMessage"] = mb_convert_encoding($strMessage, 'EUC-JP', 'UTF-8');
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

	// ��Ͽ�ѥǡ����μ���
	// �إå���
	$headerData = $objHeader->outputRegistData();

	// ���Ѹ����ֹ�ꥹ�Ȥ�����
	foreach($estimateDetailNo as $value) {
		$rowNo = $value['row'];
		$detailNo = $value['estimateDetailNo'];
		$detailNoList[$rowNo] = $detailNo;
	}

	// �ԥǡ���
	$index = 0;
	foreach ($objRowList as $objRow) {
		if ($objRow->invalidFlag === false) {
			++$index;
			$row = $objRow->outputRow();
			$rowData = $objRow->outputRegistData();
			$previousDetailNo = $detailNoList[$row];
			$rowData['previousDetailNo'] = $previousDetailNo;
			$rowDataList[$index] = $rowData;
		}
	}

	$objRegist = new updateInsertData();

	$standardRateMaster = $objDB->getEstimateStandardRate();

	$objCal = new updateOtherCellsController();
	$objCal->calculateParam($objRowList, $objHeader, $standardRateMaster);
	$calcData = $objCal->outputParam();

	$update = array(
		'headerData' => $headerData,
		'rowDataList' => $rowDataList,
		'calculatedData' => $calcData
	);

	$objDB->transactionBegin();

	$productCode = $aryData['productCode'];
	$reviseCode = $aryData['reviseCode'];
	$revisionNo = $aryData['revisionNo'];
	$estimateNo = $aryData['estimateNo'];

	// ��Ͽ��ɬ�פʥǡ����򥻥åȤ���
	$objRegist->setUpdateParam($update, $lngUserCode, $productCode, $reviseCode, $revisionNo, $objDB);
	$objRegist->update();

	$objDB->transactionCommit();
	
	$sessionID = $aryData['strSessionID'];

	$completeMessage = "���ʥ�����". $productCode. "_". $reviseCode. "���Խ����Ƥ���Ͽ�������ޤ�����<br>";
	$completeMessage .= "���Ѹ����ǡ����κ��ɤ߹��ߤ�Ԥ��ޤ�";

	$formData = array(
		'strSessionID' => $aryData["strSessionID"],
		'productCode' => $productCode,
		'reviseCode' => $reviseCode,
		'revisionNo' => $revisionNo,
		'estimateNo' => $estimateNo,
	);

	$form = makeHTML::getHiddenData($formData);

	$formData = array(
		'FORM' => $form,
		'completeMessage' => $completeMessage
	);

	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "estimate/preview/update.tmpl" );

	$objTemplate->replace( $formData );
	$objTemplate->complete();

    //fncDebug( 'es_finish.txt', $objTemplate->strTemplate, __FILE__, __LINE__);
	echo $objTemplate->strTemplate;


	// ��¾�ơ��֥�������
	$strQuery = "DELETE";
	$strQuery .= " FROM t_exclusivecontrol";
	$strQuery .= " WHERE lngfunctioncode = ". $functionCode;
	$strQuery .= " AND strexclusivekey1 = '". $productCode. "'";
	$strQuery .= " AND strexclusivekey2 = '". $reviseCode. "'";

	$result = pg_query($objDB->ConnectID, $strQuery);

	$objDB->close();
