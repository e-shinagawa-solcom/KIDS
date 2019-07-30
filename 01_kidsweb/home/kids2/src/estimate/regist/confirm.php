<?php

/**
*
*	@charset	: EUC-JP
*/

	require ( 'conf.inc' );										// 設定読み込み
	require ( LIB_DEBUGFILE );									// Debugモジュール

	require ( LIB_ROOT . "mapping/conf_mapping_common.inc" );	// マッピング設定 - 共通
	require ( LIB_ROOT . "mapping/conf_mapping_estimate.inc" );	// マッピング設定 - 見積原価管理

	require ( LIB_FILE );										// ライブラリ読み込み
    
    require ( SRC_ROOT . "estimate/cmn/estimateSheetController.php" );
	require ( SRC_ROOT . "estimate/cmn/makeHTML.php" );

	// 行クラスファイルの読み込み
	require_once (SRC_ROOT. "/estimate/cmn/productSalesRowController.php");
	require_once (SRC_ROOT. "/estimate/cmn/fixedCostSalesRowController.php");
	require_once (SRC_ROOT. "/estimate/cmn/fixedCostOrderRowController.php");
	require_once (SRC_ROOT. "/estimate/cmn/partsCostOrderRowController.php");
	require_once (SRC_ROOT. "/estimate/cmn/otherCostOrderRowController.php");

	require_once (SRC_ROOT. "/estimate/cmn/estimateHeaderController.php");

	require_once (SRC_ROOT. "/estimate/cmn/estimateTotalCalculationController.php");

	require_once ( SRC_ROOT . "/estimate/cmn/estimateDB.php");
	
	use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

	$objDB			= new estimateDB();
	$objAuth		= new clsAuth();
	$objTemplate	= new clsTemplate();								// テンプレートオブジェクト生成

	$charset = 'EUC-JP';

    // $objMap			= new clsMapping();									// マッピングオブジェクト生成
    
	$objReader      = new XlsxReader();
	
	$objMaster;
	$aryProductHeader	= array();	// ワークシート製品データ取得配列
	$aryMaster			= array();	// 製品マスタデータ取得配列
	$arySystemRate		= array();	// システムレート配列
	$aryExcelRate		= array();	// Excelレート配列
	$aryDiff			= array();	// 差異取得配列
	$aryBuff			= array();	// 表示用データ取得配列バッファ
	$strDiff			= "";

	$aryError			= array();	// エラーチェック配列


	//-------------------------------------------------------------------------
	// DBオープン
	//-------------------------------------------------------------------------
	$objDB->InputEncoding = 'UTF-8';
	$objDB->open( "", "", "", "" );

	//-------------------------------------------------------------------------
	// パラメータ取得
	//-------------------------------------------------------------------------
	$aryData	= array();
	$aryData	= $_REQUEST;

	$aryData["CHAR_SET"]			= TMP_CHARSET;					// テンプレート文字コード
	$aryData["lngLanguageCode"]		= $_COOKIE["lngLanguageCode"];	// 言語コード

    // シート番号取得
    $sheetName = mb_convert_encoding($aryData['sheetname'], 'UTF-8', 'EUC-JP');

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

	

	// 権限確認
	if( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 権限グループコードの取得
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );
    
    // ファイル情報取得
    $file = array (
        'exc_name' => $aryData["exc_name"],
        'exc_type' => $aryData["exc_type"],
        'exc_tmp_name' => $aryData["exc_tmp_name"],
        'exc_error' => $aryData["exc_error"],
        'exc_size' => $aryData["exc_size"]
    );

    // ブックのファイル形式チェック
	$fileCheckResult = estimateSheetController::checkFileFormat($file);

	// DBから標準割合を取得
	$standardRateMaster = $objDB->getEstimateStandardRate();
    
	// ブックのロード
	if ($fileCheckResult) {

		$spreadSheet = $objReader->load($fileCheckResult);

		// 必要な定数を取得する
		$nameList = workSheetConst::getAllNameList();	
		$rowCheckNameList = workSheetConst::DETAIL_HEADER_CELL_NAME_LIST;
		$targetAreaList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

		// phpSpreadSheetオブジェクトからシートの情報を取得
		$allSheetInfo = estimateSheetController::getSheetInfo($spreadSheet, $nameList, $rowCheckNameList);

		$sheetInfo = $allSheetInfo[$sheetName];

		$objSheet = null;
		$outputMessage = array(); // 出力メッセージ

		$difference = array();
		$hiddenList = array();

		// シートが表示無効でない場合はワークシート処理オブジェクトのインスタンス生成
		$objSheet = new estimateSheetController();

		// オブジェクトにデータをセットする
		$objSheet->dataInitialize($sheetInfo);

		// phpSpreadSheetで生成したシートオブジェクトをグローバル参照用にセットする
		$sheet = $sheetInfo['sheet'];
		$cellAddressList = $sheetInfo['cellAddress'];

		// ヘッダ部のバリデーションを行う
		$objHeader = new estimateHeaderController();
		$objHeader->initialize($sheetInfo['cellAddress'], $lngUserCode);
		$message = $objHeader->validate();

		if ($message) {
			$outputMessage[] = $message;
		}

		// 対象エリアの範囲を取得する
		$targetAreaRows = $objSheet->outputTargetAreaRows();
		$startRowOfDetail = $targetAreaRows[DEF_AREA_PRODUCT_SALES]['firstRow']; // 明細の開始行
		$endRowOfDetail = $targetAreaRows[DEF_AREA_OTHER_COST_ORDER]['lastRow']; // 明細の終了行

		// 輸入費用と関税の行を保存するリストを生成する（クラス内からグローバル変数として使用）
		$importCostRow = array();
		$tariffRow = array();
		
		$tariffRowList = array();
		$importCostRowList = array();

		$tariff = 0;
		$importCost = 0;

		for ($row = $startRowOfDetail; $row <= $endRowOfDetail; ++$row) {

			$objRow = null;
			// 現在の行がどの対象エリアに属するか判定を行う
			$rowAttribute = $objSheet->checkAttributeRow($row);
			
			if ($rowAttribute) {
				// 対象エリアによってインスタンス作成時のクラスを指定する
				switch ($rowAttribute) {
					case DEF_AREA_PRODUCT_SALES:
						$objRow = new productSalesRowController();
						break;
					case DEF_AREA_FIXED_COST_SALES:
						$objRow = new fixedCostSalesRowController();
						break;
					case DEF_AREA_FIXED_COST_ORDER:
						$objRow = new fixedCostOrderRowController();
						break;
					case DEF_AREA_PARTS_COST_ORDER:
						$objRow = new partsCostOrderRowController();
						break;
					case DEF_AREA_OTHER_COST_ORDER;
						$objRow = new otherCostOrderRowController();
						break;
					default:
						break;
				}
				
				if ($objRow) {
					$objRow->initialize($sheetInfo['cellAddress'], $row);
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
					if ($objRow->invalidFlag === true) {
						$hiddenList[$row] = true;
					}
				}
			}
		}

		// 輸入費用計算用変数に関税計算用変数を代入
		$importCost = $tariff;

		// 関税の処理
		foreach ($tariffRowList as $rowIndex) {
			$tariffObjRow = &$objRowList[$rowIndex];
			$tariffObjRow->chargeCalculate($tariff);

			if ($tariffObjRow->invalidFlag === false) {
				// 単価出力
			    $price = $tariffObjRow->price;
				$priceColumn = $tariffObjRow->columnNumberList['price'];
				$priceCell =  $priceColumn. $rowIndex;

				$subtotalColumn = $tariffObjRow->columnNumberList['subtotal'];
				$subtotalCell =  $subtotalColumn. $rowIndex;

				$deliveryColumn = $tariffObjRow->columnNumberList['delivery'];
				$deliveryCell = $deliveryColumn. $rowIndex;

				// 計算後の単価、小計をsheetオブジェクトに挿入
				$objSheet->sheet->getCell($priceCell)->setValue($price);
				$objSheet->sheet->getCell($subtotalCell)->setValue($tariffObjRow->calculatedSubtotalJP);

				$objSheet->sheet->getCell($deliveryCell)->setValue(null);

				// 輸入費用計算用変数に計算結果を加算
				$importCost += $tariffObjRow->calculatedSubtotalJP;
			}
		}

		// 輸入費用の処理
		foreach ($importCostRowList as $rowIndex) {
			$importCostObjRow = &$objRowList[$rowIndex];
			$importCostObjRow->chargeCalculate($importCost);

			if ($importCostObjRow->invalidFlag === false) {
				// 単価出力
				$price = $importCostObjRow->price;

				$priceColumn =  $importCostObjRow->columnNumberList['price'];
				$priceCell =  $priceColumn. $rowIndex;

				$subtotalColumn = $importCostObjRow->columnNumberList['subtotal'];
				$subtotalCell =  $subtotalColumn. $rowIndex;

				$deliveryColumn = $importCostObjRow->columnNumberList['delivery'];
				$deliveryCell = $deliveryColumn. $rowIndex;

				// 計算後の単価をsheetオブジェクトに挿入
				$objSheet->sheet->getCell($priceCell)->setValue($price);
				$objSheet->sheet->getCell($subtotalCell)->setValue($importCostObjRow->calculatedSubtotalJP);

				$objSheet->sheet->getCell($deliveryCell)->setValue(null);
			}
		}

		// 標準割合のチェック
		$standardRateCell = $cellAddressList[workSheetConst::STANDARD_RATE];
		$standardRate = $objSheet->sheet->getCell($standardRateCell)->getCalculatedValue();
		if ($standardRateMaster != $standardRate) {
			$companyLocalRate = $standardRateMaster ? number_format(($standardRateMaster * 100), 2, '.', ''). "%" : '-';
			$sheetRate = $standardRate ? number_format(($standardRate * 100), 2, '.', ''). "%" : '-';
			$difference[] = array(
				'delivery' => '-',
				'monetary' => '標準割合',
				'temporaryRate' => $companyLocalRate,
				'sheetRate' => $sheetRate
			);
		}

		// 行オブジェクトを基にした処理
		foreach ($objRowList as $row => $objRow) {
			$columnList = $objRow->columnNumberList;
			
			// メッセージコードの取得
			$messageOfConversionRate = $objRow->messageCode['conversionRate'];
			$messageOfSubtotal = $objRow->messageCode['subtotal'];

			// ブックの適用レートがDBの通貨レートと異なる場合、またはブックの小計が計算結果と異なる場合
			if ($messageOfConversionRate === 9206 || $messageOfSubtotal === 9205) {
			// ブックの適用レートがDBの通貨レートと異なる場合は差分表のデータを作成する
				if ($messageOfConversionRate === 9206) {
					// ブックオブジェクトの通貨レートの置換
					$column = $columnList['conversionRate'];
					$cellAddress = $column.$row;
					$acquiredRate = $objRow->acquiredRate;
					$objSheet->sheet->getCell($cellAddress)->setValue($acquiredRate);
					
				}

				// ブックオブジェクトの小計の置換
				$column = $columnList['subtotal'];
				$cellAddress = $column.$row;
				$calculatedSubtotalJP = $objRow->calculatedSubtotalJP;

				$objSheet->sheet->getCell($cellAddress)->setValue($calculatedSubtotalJP);
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
			$aryHtml["strErrorMessage"] = mb_convert_encoding($strMessage, 'EUC-JP', 'UTF-8');

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

		$objCal = new estimateTotalCalculationController();
		$objCal->calculateParam($objRowList, $objHeader, $sheetInfo['cellAddress'], $standardRateMaster);
		$calcData = $objCal->outputParam();

		foreach ($calcData as $cellName => $value) {
			$cellAddress = $cellAddressList[$cellName];
			$objSheet->sheet->getCell($cellAddress)->setValue($value);
		}

		// 非表示リスト（無効リスト）を追加する
		$objSheet->setHiddenRowList($hiddenList);

		$viewData = $objSheet->makeDataOfSheet();

		$viewData = $objSheet->deleteInvalidRow($viewData);

		$viewDataList[0] = $viewData;

		$ws_num = 0;

		// 表示用データをJSONに変換
		$json = json_encode($viewDataList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
		$json = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');

		// 登録用データの取得
		// ヘッダ部
		$headerData = $objHeader->outputRegistData();
		// 行データ
		$index = 0;
		foreach ($objRowList as $objRow) {
			if ($objRow->invalidFlag === false) {
				++$index;
				$rowData = $objRow->outputRegistData();
				$rowDataList[$index] = $rowData;
			}
		}
		
		// 登録用データの整形
		$registData = array(
			'headerData' => $headerData,
			'rowDataList' => $rowDataList,
			'calculatedData' => $calcData
		);

		// JSONに変換
		$registJson = json_encode($registData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
		// ダブルクォーテーションを置換する（HTML経由でデータを渡す際に ":ダブルクォーテーション の重複によってデータが渡せないことを防ぐため)
		$registJson = str_replace('"', '/quot/', $registJson);

		$registJson = htmlspecialchars($registJson, ENT_QUOTES, 'UTF-8');

		// POST用データにセット
		$aryData['registJson'] = $registJson;

		// ExcelワークシートHTML取得
		$strExcel       .= "<div class=\"sheetHeaderConfirm\" id=\"sheet". $ws_num. "\">";
		$strExcel       .= "<br>";
		$strExcel		.= makeHTML::getWorkSheet2HTML($aryData['sheetname'], $ws_num, "confirm", $data); // ヘッダー
		$strExcel       .= "</div>";
		$strExcel		.= makeHTML::getGridTable($ws_num); // データ挿入タグ

		$css_rowstyle .= '.rowstyle'.$ws_num.' { display:none;}'."\n";

		$strExcel = str_replace('_%css_rowstyle%_', $css_rowstyle, $strCSS). $strExcel;

		// 送信用FORMデータ作成
		$form = makeHTML::getHiddenFileData($file);
		
		$form .= makeHTML::getHiddenData($aryData);

		$aryData["WORKSHEET"]	= $select; // ワークシート選択肢
		$aryData["EXCEL"]		= $strExcel; // index
		$aryData["TABLEDATA"]	= $json;
		$aryData["FORM_NAME"]	= FORM_NAME;
		$aryData["FORM"]	    = $form;
		$aryData["TEMPORARY"]	= $strTemporary;

		// テンプレート読み込み
		$objTemplate->getTemplate( "estimate/regist/confirm.tmpl" );

		// テンプレート生成
		$objTemplate->replace( $aryData );
		$objTemplate->complete();

		// HTML出力
		echo $objTemplate->strTemplate;
		return;
	}

?>
