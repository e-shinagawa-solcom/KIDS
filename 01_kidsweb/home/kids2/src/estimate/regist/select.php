<?php

require ( 'conf.inc' );										// 設定読み込み
require ( LIB_DEBUGFILE );									// Debugモジュール

require ( LIB_ROOT . "mapping/conf_mapping_common.inc" );	// マッピング設定 - 共通
require ( LIB_ROOT . "mapping/conf_mapping_estimate.inc" );	// マッピング設定 - 見積原価管理

require ( LIB_FILE );										// ライブラリ読み込み

require ( SRC_ROOT . "estimate/cmn/estimateSheetController.php" );
require ( SRC_ROOT . "estimate/cmn/makeHTML.php" );

// 定数ファイルの読み込み
require_once ( SRC_ROOT . "/estimate/cmn/const/workSheetConst.php");

// 行クラスファイルの読み込み
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
$objTemplate	= new clsTemplate();								// テンプレートオブジェクト生成
$objReader      = new XlsxReader();                                 // phpSpreadSheetのReader

//-------------------------------------------------------------------------
// DBオープン
//-------------------------------------------------------------------------
$objDB->open( "", "", "", "" );


//-------------------------------------------------------------------------
// パラメータ取得
//-------------------------------------------------------------------------
// $aryData	= $_REQUEST; POST、GETで通信するデータがないためコメントアウト

$aryData["lngLanguageCode"]		= $_COOKIE["lngLanguageCode"];	// 言語コード
$aryData["lngFunctionCode"]		= DEF_FUNCTION_E1;				// 管理コード（見積原価）
$aryData["strSessionID"]        = $_COOKIE["strSessionID"];

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");
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



if( $_FILES )
{
    $file = array();
    // テンポラリファイル作成、ファイル名取得
    $strTmpFileName	= getTempFileName($_FILES['excel_file']['tmp_name'] );

    // ファイル情報の取得
    $file["exc_name"]			= $_FILES['excel_file']['name'];
    $file["exc_type"]			= $_FILES['excel_file']['type'];
    $file["exc_tmp_name"]		= $strTmpFileName;
    $file["exc_error"]			= $_FILES['excel_file']['error'];
    $file["exc_size"]			= $_FILES['excel_file']['size'];

    $file["lngRegistConfirm"]	= 1;	// 確認画面表示フラグ
}

// ファイルチェック
$fileCheckResult = estimateSheetController::checkFileFormat($file);

// DBから通貨の表示名取得
$monetaryUnitList = $objDB->getMonetaryUnitList();

// DBから標準割合を取得
$standardRateMaster = $objDB->getEstimateStandardRate();

// エクセルファイルのロード
if ($fileCheckResult) {
	// ブックのデータをphpSpreadSheetオブジェクトに展開
	$spreadSheet = $objReader->load($fileCheckResult);

	// 必要な定数を取得する
	$nameList = workSheetConst::getAllNameList();	
	$rowCheckNameList = workSheetConst::DETAIL_HEADER_CELL_NAME_LIST;
	$targetAreaList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

    // phpSpreadSheetオブジェクトからシートの情報を取得
	$allSheetInfo = estimateSheetController::getSheetInfo($spreadSheet, $nameList, $rowCheckNameList);

	if ($allSheetInfo) {
		// シート番号の初期値を0に設定する
		$sheetNumber = 0;
		// エラーチェック等
		foreach ($allSheetInfo as $sheetName => $sheetInfo) {
			$objSheet = null;
			$outputMessage = array(); // 出力メッセージ

			$difference = array();
			$hiddenList = array();
			
			if ($sheetInfo['displayInvalid']) {
				continue;
			} else {
				// 輸入費用計算用変数（関税の小計の合計）
				$tariffTotal = 0;
				
				// シートが表示無効でない場合はワークシート処理オブジェクトのインスタンス生成
				$objSheet = new estimateSheetController();

				// オブジェクトにデータをセットする
				$objSheet->dataInitialize($sheetInfo, $objDB);

				// phpSpreadSheetで生成したシートオブジェクトをグローバル参照用にセットする
				$sheet = $objSheet->sheet;

				$cellAddressList = $sheetInfo['cellAddress'];

				// 対象エリアの範囲を取得する
				$targetAreaRows = $objSheet->outputTargetAreaRows();
				$startRowOfDetail = $targetAreaRows[DEF_AREA_PRODUCT_SALES]['firstRow']; // 明細の開始行
				$endRowOfDetail = $targetAreaRows[DEF_AREA_OTHER_COST_ORDER]['lastRow']; // 明細の終了行
			
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
							$objRow->initialize($cellAddressList, $row);
							$objRow->workSheetSelectCheck();

							$objRowList[$row] = $objRow;
							if ($objRow->invalidFlag === true) {
								$hiddenList[$row] = true;
							}							
						}
					}
				}

				// 行オブジェクトを基にした処理
				foreach ($objRowList as $row => $objRow) {
					$columnList = $objRow->columnNumberList;
					
					// メッセージコードの取得
					$messageOfConversionRate = $objRow->messageCode['conversionRate'];

					// ブックの適用レートがDBの通貨レートと異なる場合、またはブックの小計が計算結果と異なる場合
					if ($messageOfConversionRate) {
					// ブックの適用レートがDBの通貨レートと異なる場合は差分表のデータを作成する
						$delivery = $objRow->delivery;
						$monetary = $objRow->monetary;
						$acquiredRate = $objRow->acquiredRate;
						$conversionRate = $objRow->conversionRate;
						$monetaryUnit = $monetaryUnitList[$monetary];
						if ($messageOfConversionRate === DEF_MESSAGE_CODE_RATE_DIFFER) {
							// 通貨レート差分表のデータ生成
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
				
				// 重複するデータを削除する
				if ($difference) {
					$difference = array_unique($difference, SORT_REGULAR);
					$differenceMessage = fncOutputError ( DEF_MESSAGE_CODE_RATE_DIFFER, DEF_WARNING, "", false, "", $objDB );
				}
				
				if ($notFound) {
					$notFound = array_unique($notFound, SORT_REGULAR);
					$notFoundMessage = fncOutputError ( DEF_MESSAGE_CODE_RATE_DIFFER, DEF_WARNING, "", false, "", $objDB );
				}
			
				// 非表示リスト（無効リスト）を追加する
				$objSheet->setHiddenRowList($hiddenList);

				// ワークシート表示用のデータを出力する
				$viewData = $objSheet->makeDataOfSheet();

				// ワークシート名を'EUC-JP'にエンコードする
				$strWSName = mb_convert_encoding($sheetName, "EUC-JP", "UTF-8");

				$strExcel       .= "<div class=\"sheetHeader\" id=\"sheet". $sheetNumber. "\">";
				$strExcel       .= makeHTML::makeDifferenceRateTable($difference, $differenceMessage);
				$strExcel       .= "<br>";
				$strExcel       .= makeHTML::makeNotFoundRateTable($notFound, $notFoundMessage);
				$strExcel       .= "<br>";
				// $strExcel       .= makeHTML::makeWarningHTML($outputMessage);
				$strExcel       .= "<br>";
				$strExcel		.= makeHTML::getWorkSheet2HTML($strWSName, $sheetNumber, "select"); // ヘッダー
				$strExcel       .= "</div>";

				$strExcel		.= makeHTML::getGridTable($sheetNumber); // データ挿入タグ

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

// 有効なシートが存在しない場合はエラーメッセージを表示する
if ( !$allSheetInfo ) {
	$strMessage = '有効なシートが存在しません。';

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

// dataをJSONに変換
$json = json_encode($viewDataList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$json = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');

// セレクトボックスの値を取得
$select = makeHTML::getOptionsList($sheetNameList);

// 送信用FORMデータ作成
$form = makeHTML::getHiddenFileData($file);
$form .= makeHTML::getHiddenData($aryData);

//
$aryData["WORKSHEET"]	= $select; // ワークシート選択肢
$aryData["EXCEL"]		= $strExcel; // index
$aryData["TABLEDATA"]	= $json;
$aryData["FORM_NAME"]	= FORM_NAME;
$aryData["FORM"]	    = $form;

// テンプレート読み込み
$objTemplate->getTemplate( "estimate/regist/select.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;
return;
?>
