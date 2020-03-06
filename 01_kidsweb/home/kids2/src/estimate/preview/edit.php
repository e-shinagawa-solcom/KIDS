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

	require_once ( SRC_ROOT . "/estimate/cmn/estimateDB.php");
	require_once ( SRC_ROOT . "/estimate/cmn/estimatePreviewController.php");
	
	use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

	$objDB			= new estimateDB();
	$objAuth		= new clsAuth();
	$objTemplate	= new clsTemplate();								// テンプレートオブジェクト生成

	$charset = 'UTF-8';
    
	$objReader      = new XlsxReader();
	

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


	// 権限確認
	if( !fncCheckAuthority( DEF_FUNCTION_E3, $objAuth )) {
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 権限グループコードの取得
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );
    
	// POSTパラメータよりパラメータを取得
	$estimateNo = $aryData['estimateNo']; // 見積原価番号

	$revisionNo = $aryData['revisionNo']; // リビジョン番号
    
    // 見積原価情報の取得
    $estimate = $objDB->getEstimateDetail($estimateNo, $revisionNo);

	$firstEstimateDetail = current($estimate);
	
	$productCode = $firstEstimateDetail->strproductcode;
	$reviseCode = $firstEstimateDetail->strrevisecode;
	$productRevisionNo = $firstEstimateDetail->lngproductrevisionno;

    // 製品マスタの情報取得
	$product = $objDB->getProduct($productCode, $reviseCode, $productRevisionNo);

	$objPreview = new estimatePreviewController();
	$objPreview->dataInitialize($product, $estimate, $objDB);
	
	$productData = $objPreview->getProduct();

	$estimateData = $objPreview->getEstimate();

	if (!isset($maxRevisionNo)) {
        $maxRevisionNo = $objPreview->revisionNo;
	}

    $tempFilePath = EXCEL_TMP_ROOT. 'workSheetTmp.xlsx';

  
	$spreadSheet = $objReader->load($tempFilePath);

	// 必要な定数を取得する
	$nameList = workSheetConst::getAllNameListForDownload();	
	$rowCheckNameList = workSheetConst::DETAIL_HEADER_CELL_NAME_LIST;
	$targetAreaList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

	// phpSpreadSheetオブジェクトからシートの情報を取得
	$allSheetInfo = estimateSheetController::getSheetInfo($spreadSheet, $nameList, $rowCheckNameList);

	$sheetInfo = estimateSheetController::getFirstElement($allSheetInfo);

	if ($sheetInfo['displayInvalid']) {
		// テンプレート用のシートが無効になっていない場合、エラーを出力する
		if ( !$sheetDataList ) {
			$strMessage = 'テンプレート異常';

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
	}

	$objSheet = null;
	$outputMessage = array(); // 出力メッセージ

	$difference = array();
	$hiddenList = array();

	// シートが表示無効でない場合はワークシート処理オブジェクトのインスタンス生成
	$objSheet = new estimateSheetController();

	// オブジェクトにデータをセットする
	$objSheet->dataInitialize($sheetInfo, $objDB);

	// phpSpreadSheetで生成したシートオブジェクトをグローバル参照用にセットする
	$sheet = $sheetInfo['sheet'];
	$cellAddressList = $sheetInfo['cellAddress'];

	// ワークシートオブジェクトに必要な値をセット
	$objSheet->setDBEstimateData($productData, $estimateData, workSheetConst::MODE_ESTIMATE_EDIT);

	$hiddenList = array();

	// 非表示リスト（無効リスト）を追加する
	$objSheet->setHiddenRowList($hiddenList);

	$viewData = $objSheet->makeDataOfSheet();

	// ドロップダウンデータの取得
	$dropdownDSCI = $objDB->getDropdownForDivSubAndClsItm();
	$dropdownCompany = $objDB->getDropdownForCompany();
	$dropdownGU = $objDB->getDropdownForGroupAndUser();
	$dropdownDev = $objDB->getDropdownForDevelopUser();

	$viewData['dropdownDSCI'] = $dropdownDSCI;
	$viewData['dropdownCompany'] = $dropdownCompany;
	$viewData['dropdownGU'] = $dropdownGU;
	$viewData['dropdownDev'] = $dropdownDev;

	$viewDataList[0] = $viewData;

	$ws_num = 0;

	//-------------------------------------------------------------------------
	// DB Close
	//-------------------------------------------------------------------------
	$objDB->close();
	$objDB->freeResult( $lngResultID );

	// dataをJSONに変換
	$json = json_encode($viewDataList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
	$json = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');

	// ヘッダ部生成
	$header = makeHTML::getEditHeader($maxRevisionNo, $revisionNo); // ヘッダー
	// ExcelワークシートHTML取得
	$strExcel		.= makeHTML::getGridTable($ws_num); // データ挿入タグ

	$css_rowstyle .= '.rowstyle'.$ws_num.' { display:none;}'."\n";

	$strExcel = str_replace('_%css_rowstyle%_', $css_rowstyle, $strCSS). $strExcel;


	$formData = array(
		'strSessionID' => $aryData["strSessionID"],
		'productCode' => $productCode,
		'reviseCode' => $reviseCode,
		'revisionNo' => $revisionNo,
		'estimateNo' => $estimateNo,
	);

	// 送信用FORMデータ作成
	$form .= makeHTML::getHiddenData($formData);

	$aryData["HEADER"]      = $header;
	$aryData["EXCEL"]		= $strExcel; // index
	$aryData["TABLEDATA"]	= $json;
	$aryData["FORM"]	    = $form;

	// テンプレート読み込み
	$objTemplate->getTemplate( "estimate/preview/edit.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

?>
