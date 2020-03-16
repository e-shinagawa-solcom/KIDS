<?php

/**
*
*	@charset	: UTF-8
*/

	require ( 'conf.inc' );										// 設定読み込み
	require ( LIB_DEBUGFILE );									// Debugモジュール

	require ( LIB_FILE );										// ライブラリ読み込み
    
    require ( SRC_ROOT . "estimate/cmn/estimateSheetController.php" );
	require ( SRC_ROOT . "estimate/cmn/makeHTML.php" );

	// 行クラスファイルの読み込み
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
	$objTemplate	= new clsTemplate();								// テンプレートオブジェクト生成

	$charset = 'UTF-8';
    
	$objReader      = new XlsxReader();


	//-------------------------------------------------------------------------
	// DBオープン
	//-------------------------------------------------------------------------
	$objDB->InputEncoding = 'UTF-8'; // phpSpreadSheetに代入する文字コードをUTF-8で扱うためDBの文字コードをセットする
	$objDB->open( "", "", "", "" );

	//-------------------------------------------------------------------------
	// パラメータ取得
	//-------------------------------------------------------------------------
	$aryData	= array();
	$aryData	= $_REQUEST;

	$aryData["lngLanguageCode"]		= $_COOKIE["lngLanguageCode"];	// 言語コード

    // シート名取得
    $sheetName = $aryData['sheetname'];

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
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
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
    
	if ($fileCheckResult) {
        // ブックのロード(phpSpreadSheetオブジェクトにブックの情報をセットする)
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

		// ワークシート処理オブジェクトのインスタンス生成
		$objSheet = new estimateSheetController();

		// オブジェクトにデータをセットする
		$objSheet->dataInitialize($sheetInfo, $objDB);

		// シート情報を取得する
		$sheet = $sheetInfo['sheet'];
		$cellAddressList = $sheetInfo['cellAddress'];

		// ヘッダ部のバリデーションを行う
		$objHeader = new registHeaderController($objDB);
		$objHeader->initialize($sheetInfo['cellAddress'], $lngUserCode, $sheet);
		$errorMessage = $objHeader->validate();

		// ヘッダ部の値を出力する
		$param = $objHeader->outputRegistData();
		$beforeProductionQuantity = $param[workSheetConst::PRODUCTION_QUANTITY];

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

					// 以下の処理を行う為、部材費及びその他費用の行の処理は製造費用の行の処理の後に行う
					if ($rowAttribute === DEF_AREA_PARTS_COST_ORDER
					    || $rowAttribute === DEF_AREA_OTHER_COST_ORDER) {

						$calcProductionQuantity = productSalesRowController::outputProductionQuantity();
						$result = $objRow->substitutePQForPrice($beforeProductionQuantity, $calcProductionQuantity);

						if ($result === true) { // 数量の代入が行われた場合はブックの値を書き換える
							
							$quantityColumn =  $objRow->columnNumberList['quantity'];
							$quantityCell =  $quantityColumn. $row;
	
							// 代入後の数量をsheetオブジェクトに挿入
							$objSheet->sheet->getCell($quantityCell)->setValue($calcProductionQuantity);
						}
					}

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
					if ($objRow->invalidFlag != true) {
						if ($objRow->divisionSubjectCode === DEF_STOCK_SUBJECT_CODE_CHARGE) {
/*
							if ($objRow->classItemCode === DEF_STOCK_ITEM_CODE_IMPORT_COST) {
								$importCostRowList[] = $row;
							} else if ($objRow->classItemCode === DEF_STOCK_ITEM_CODE_TARIFF) {
								$tariffRowList[] = $row;
							}
*/
							if ($objRow->classItemCode === DEF_STOCK_ITEM_CODE_TARIFF) {
								$tariffRowList[] = $row;
							}
						}
						else if ($objRow->divisionSubjectCode === DEF_STOCK_SUBJECT_CODE_MATERIAL_PARTS_COST 
						         && $objRow->classItemCode === DEF_STOCK_ITEM_CODE_CERTIFICATE) {
							$certificateRowList[] = $row;
						}
					}

					$objRowList[$row] = $objRow;
					if ($objRow->invalidFlag == true) {
						$hiddenList[$row] = true;
					}
				}
			}
		}

		// 輸入費用計算用変数に関税計算用変数を代入
		$importCost = $tariff;

		// 関税の処理
		if ($tariffRowList) {
			foreach ($tariffRowList as $rowIndex) {
				$tariffObjRow = &$objRowList[$rowIndex];
				$tariffObjRow->chargeCalculate($tariff);
	
				if ($tariffObjRow->invalidFlag != true) {
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
	
					// 輸入費用計算用変数に計算結果を加算
					$importCost += $tariffObjRow->calculatedSubtotalJP;
				}
			}
		}

		// 証紙第計算用変数に上代を代入
		$retailCell = $cellAddressList[workSheetConst::RETAIL_PRICE];
		$retailCost = $objSheet->sheet->getCell($retailCell)->getCalculatedValue();

		// 証紙の処理
		if ($certificateRowList) {
			foreach ($certificateRowList as $rowIndex) {
				$certificateObjRow = &$objRowList[$rowIndex];
				$certificateObjRow->chargeCalculate($retailCost);
	
				if ($certificateObjRow->invalidFlag != true) {
					// 単価出力
					$price = $certificateObjRow->price;
					$priceColumn = $certificateObjRow->columnNumberList['price'];
					$priceCell =  $priceColumn. $rowIndex;
	
					$subtotalColumn = $certificateObjRow->columnNumberList['subtotal'];
					$subtotalCell =  $subtotalColumn. $rowIndex;
	
					$deliveryColumn = $certificateObjRow->columnNumberList['delivery'];
					$deliveryCell = $deliveryColumn. $rowIndex;
	
					// 計算後の単価、小計をsheetオブジェクトに挿入
					$objSheet->sheet->getCell($priceCell)->setValue($price);
					$objSheet->sheet->getCell($subtotalCell)->setValue($certificateObjRow->calculatedSubtotalJP);
	
					// 輸入費用計算用変数に計算結果を加算
					$importCost += $certificateObjRow->calculatedSubtotalJP;
				}
			}
		}



		// // 標準割合のチェック
		// $standardRateCell = $cellAddressList[workSheetConst::STANDARD_RATE];
		// $standardRate = $objSheet->sheet->getCell($standardRateCell)->getCalculatedValue();
		// if ($standardRateMaster != $standardRate) {
		// 	$companyLocalRate = $standardRateMaster ? number_format(($standardRateMaster * 100), 2, '.', ''). "%" : '-';
		// 	$sheetRate = $standardRate ? number_format(($standardRate * 100), 2, '.', ''). "%" : '-';
		// 	$difference[] = array(
		// 		'delivery' => '-',
		// 		'monetary' => '標準割合',
		// 		'temporaryRate' => $companyLocalRate,
		// 		'sheetRate' => $sheetRate
		// 	);
		// }

		// 行オブジェクトを基にした処理
		foreach ($objRowList as $row => $objRow) {
			$columnList = $objRow->columnNumberList;
			
			// // メッセージコードの取得
			// $messageOfConversionRate = $objRow->message['conversionRate'];

			// ブックの適用レートがDBの通貨レートと異なる場合
			// if ($messageOfConversionRate === DEF_MESSAGE_CODE_RATE_DIFFER) {
			// 	// ブックオブジェクトの通貨レートの置換
			// 	$column = $columnList['conversionRate'];
			// 	$convarsionRateCell = $column.$row;
			// 	$acquiredRate = $objRow->acquiredRate;
			// 	$objSheet->sheet->getCell($convarsionRateCell)->setValue($acquiredRate);
			// }

			// ブックオブジェクトの小計の置換
			$column = $columnList['subtotal'];
			$subtotalCell = $column.$row;
			$calculatedSubtotalJP = $objRow->calculatedSubtotalJP;

			$objSheet->sheet->getCell($subtotalCell)->setValue($calculatedSubtotalJP);
			
			$column = $columnList['payoff'];

            if(strlen($column) > 0){
				$payoff = $objRow->payoff;
				$payoffCell = $column.$row;
				if ($objRow->percentInputFlag !== true) {
					// DBの情報に置換後（仕入の空欄はその他）の償却をセットする
					    $objSheet->sheet->getCell($payoffCell)->setValue($payoff);
				} else {
					// パーセント入力されている場合は右寄せにする
					$objSheet->setHorizontalRight($payoffCell);
				}
			}
		}

		// バリデーションでエラーが発生した場合はエラーメッセージ出力画面に遷移する
		if ($errorMessage) {
			makeHTML::outputErrorWindow($errorMessage);
		}

		$objCal = new registOtherCellsController();
		$objCal->calculateParam($objRowList, $objHeader, $sheetInfo['cellAddress'], $standardRateMaster);
		$calcData = $objCal->outputParam();

		foreach ($calcData as $cellName => $value) {
			$cellAddress = $cellAddressList[$cellName];
			$objSheet->sheet->getCell($cellAddress)->setValue($value);
		}

		// 非表示リスト（無効リスト）を追加する
		$objSheet->setHiddenRowList($hiddenList);

		// 表示用のヘッダ部の値を出力する（表示名をデータベースの名称に置換）
		$replace = $objHeader->outputDisplayData();
		// 表示用ヘッダ部の値をワークシートに挿入する
		$objSheet->cellValueReplace($replace);

		// web表示用のデータを出力する
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
			if ($objRow->invalidFlag != true) {
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

		// 再販フラグの取得
		$reviseFlag = $objHeader->outputReviseFlag();

		// 登録メッセージの作成
		if ($reviseFlag === true) {
			$registType = "再販";
			$registClass = "reviseRegist";
		} else {
			$registType = "新規";
			$registClass = "newRegist";
		}

		$aryData["WORKSHEET"]	 = $select; // ワークシート選択肢
		$aryData["EXCEL"]		 = $strExcel; // index
		$aryData["TABLEDATA"]	 = $json;
		$aryData["FORM"]	     = $form;
		$aryData["REGIST_TYPE"]  = $registType;
		$aryData["REGIST_CLASS"] = $registClass;

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
