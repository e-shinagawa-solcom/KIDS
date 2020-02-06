<?php

/**
*
*	@charset	: UTF-8
*/

	require ( 'conf.inc' );										// 設定読み込み
	require ( LIB_DEBUGFILE );									// Debugモジュール

	require ( LIB_FILE );										// ライブラリ読み込み
	require ( LIB_EXCLUSIVEFILE );										// ライブラリ読み込み

	require ( SRC_ROOT . "estimate/cmn/makeHTML.php" );

	// 行クラスファイルの読み込み
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
	$objTemplate	= new clsTemplate();								// テンプレートオブジェクト生成

	$charset = 'UTF-8';

	//-------------------------------------------------------------------------
	// DBオープン
	//-------------------------------------------------------------------------
	$objDB->InputEncoding = 'UTF-8';
	$objDB->open( "", "", "", "" );

	//-------------------------------------------------------------------------
	// パラメータ取得
	//-------------------------------------------------------------------------
	$aryData	= array();
	$aryData	= $_POST;

	//-------------------------------------------------------------------------
	// 入力文字列値・セッション・権限チェック
	//-------------------------------------------------------------------------
	// 文字列チェック
	$aryCheck["strSessionID"]	= "null:numenglish(32,32)";
	$aryResult	= fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ユーザーコード取得
	$lngUserCode = $objAuth->UserCode;

	// 機能コード
	$functionCode = DEF_FUNCTION_E3;

	// 権限確認
	if( !fncCheckAuthority( $functionCode, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 権限グループコードの取得
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );



    // 入力データを取得
	$postDataJson = $aryData['postData'];
	$escapeJson = str_replace('/', '\\/', $postDataJson); // '/'（スラッシュ)をエスケープする

	$postData = json_decode($escapeJson, true);


	$value = $postData['value']; // 入力値
	$class = $postData['class']; // 入力値と内容（セル）を紐付けるためのクラスのリスト
	$estimateDetailNo = $postData['estimateDetailNo']; // 見積原価明細番号と行番号を紐付けるためのリスト

	// ヘッダ部の名称リストを取得
	$headerNameList = workSheetConst::WORK_SHEET_HEADER_DATA_CELL; // ブックのヘッダ
	$detailHeader = workSheetConst::DETAIL_HEADER_CELL_NAME_LIST;  // 明細行のヘッダ（タイトル行）

	// クラス名からエリアを検索するための正規表現を生成
	$areaReg = '/'. workSheetConst::AREA_CLASS_STRING. '([\d])+/';

	// ブック上の通貨名を通貨コードに変換するための配列を生成
	$monetaryExchange = workSheetConst::MONETARY_UNIT_WORKSHEET;

	$headerKey = array_flip(workSheetConst::WORK_SHEET_HEADER_DATA_CELL);

	// クラス名の検索
	foreach ($class as $classInfo) {
		$className = $classInfo['className'];
		$row = $classInfo['row'];
		$col = $classInfo['col'];
		if (preg_match($areaReg, $className, $match)) { // 明細行のデータ取得
			$areaCode = $match[1];
			$nameList = $detailHeader[$areaCode];
			foreach ($nameList as $index => $headerCellName) {
				if (strpos($className, $index)) {
					$param = $value[$row][$col];
					switch ($index) {
						case 'monetary':						
							if (!$param) { // 通貨コードがセットされていない場合はJPをセットする
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

					$rowParam[$areaCode][$row][$index] = $param; // セルのデータ出力
					
				}
			}
		} else { // その他のデータ取得
			foreach ($headerNameList as $cellName) {
				if (strpos($className, $cellName) !== false) {
					$param = $value[$row][$col];
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

	// ヘッダ部の処理
	$objHeader = new updateHeaderController($objDB);
	$objHeader->initialize($lngUserCode, $headerParam);
	$message = $objHeader->validate();

	if ($message) {
		$outputMessage[] = $message;
	}

	// 明細行の処理
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

			// 行のチェック、再計算を行う
			$objRow->workSheetRegistCheck();

			$divisionSubjectCode = $objRow->divisionSubjectCode;
			$classItemCode = $objRow->classItemCode;

			switch ($divisionSubjectCode) {
				case DEF_STOCK_SUBJECT_CODE_IMPORT_PARTS_COST:
				case DEF_STOCK_SUBJECT_CODE_OVERSEA_MOLD_DEPRECIATION:
					$tariff = $tariff + $objRow->calculatedSubtotalJP;
			}
			// 輸入費用、関税については個別処理を行う為、対象の行番号を配列に格納する
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

	// 行番号でソートする
	ksort($objRowList);

	// 輸入費用計算用変数に関税計算用変数を代入
	$importCost = $tariff;

	// 関税の処理
	if ($tariffRowList) {
		foreach ($tariffRowList as $rowIndex) {
			$tariffObjRow = &$objRowList[$rowIndex];
			$tariffObjRow->chargeCalculate($tariff);
	
			if ($tariffObjRow->invalidFlag === false) {
				// 単価出力
				$priceColumn = $tariffObjRow->columnNumberList['price'];
				$priceCell =  $priceColumn. $rowIndex;
	
				$subtotalColumn = $tariffObjRow->columnNumberList['subtotal'];
				$subtotalCell =  $subtotalColumn. $rowIndex;
	
				$deliveryColumn = $tariffObjRow->columnNumberList['delivery'];
				$deliveryCell = $deliveryColumn. $rowIndex;
	
				// 輸入費用計算用変数に計算結果を加算
				$importCost += $tariffObjRow->calculatedSubtotalJP;
			}
		}
	}

	unset($tariffRowList);


	// 輸入費用の処理
	if ($importCostRowList) {
		foreach ($importCostRowList as $rowIndex) {
			$importCostObjRow = &$objRowList[$rowIndex];
			$importCostObjRow->chargeCalculate($importCost);
	
			if ($importCostObjRow->invalidFlag === false) {
				// 単価出力	
				$priceColumn =  $importCostObjRow->columnNumberList['price'];
				$priceCell =  $priceColumn. $rowIndex;
	
				$subtotalColumn = $importCostObjRow->columnNumberList['subtotal'];
				$subtotalCell =  $subtotalColumn. $rowIndex;
	
				$deliveryColumn = $importCostObjRow->columnNumberList['delivery'];
				$deliveryCell = $deliveryColumn. $rowIndex;
	
			}
		}
	}

	// バリデーションでエラーが発生した場合はエラーメッセージを表示する
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

		// [lngLanguageCode]書き出し
		$aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

		// [strErrorMessage]書き出し
		$aryHtml["strErrorMessage"] = $strMessage;

		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "/result/error/parts.tmpl" );
		
		// テンプレート生成
		$objTemplate->replace( $aryHtml );
		$objTemplate->complete();

		// HTML出力
		echo $objTemplate->strTemplate;

		exit;
	}

	// 登録用データの取得
	// ヘッダ部
	$headerData = $objHeader->outputRegistData();

	// 見積原価番号リストの生成
	foreach($estimateDetailNo as $value) {
		$rowNo = $value['row'];
		$detailNo = $value['estimateDetailNo'];
		$detailNoList[$rowNo] = $detailNo;
	}

	// 行データ
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

	// 登録に必要なデータをセットする
	$objRegist->setUpdateParam($update, $lngUserCode, $productCode, $reviseCode, $revisionNo, $objDB);
	$objRegist->update();
	unlockExclusive($objAuth, $objDB);

	$objDB->transactionCommit();
	
	$sessionID = $aryData['strSessionID'];

	$completeMessage = "製品コード". $productCode. "_". $reviseCode. "の編集内容を登録いたしました。<br>";
	$completeMessage .= "見積原価データの再読み込みを行います";

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


/*
	// 排他テーブル削除処理
	$strQuery = "DELETE";
	$strQuery .= " FROM t_exclusivecontrol";
	$strQuery .= " WHERE lngfunctioncode = ". $functionCode;
	$strQuery .= " AND strexclusivekey1 = '". $productCode. "'";
	$strQuery .= " AND strexclusivekey2 = '". $reviseCode. "'";

	$result = pg_query($objDB->ConnectID, $strQuery);
*/
	$objDB->close();
