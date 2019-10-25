<?php

/**
*
*	@charset	: EUC-JP
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

$charset = 'EUC-JP';

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
$aryData	= $_GET;

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
if( !fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 権限グループコードの取得
$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

// GETパラメータよりパラメータを取得
$estimateNo = $aryData['estimateNo']; // 製品コード

// リビジョン番号の取得
if (isset($_POST['revisionNo'])) {
	$revisionNo = $_POST['revisionNo'];
	$estimate = $objDB->getEstimateDetail($estimateNo, $revisionNo);
} else {
	// リビジョン番号がPOSTされなかった場合は最新のデータを取得する
	$estimate = $objDB->getEstimateDetail($estimateNo);
}

$firstEstimateDetail = current($estimate);

if (!isset($revisionNo)) {
	$revisionNo = $firstEstimateDetail->lngrevisionno;
}

// 最大のリビジョン番号の取得
if (isset($_POST['maxRevisionNo'])) {
	$maxRevisionNo = $_POST['maxRevisionNo'];
} else {
	$result = $objDB->getEstimateDetail($estimateNo);
	if ($result) {
		$firstRecord = current($result);
		$maxRevisionNo = $firstRecord->lngrevisionno;
	}
}

$minRevisionNo = $firstRecord->minrevisionno;

$productCode = $firstEstimateDetail->strproductcode;
$reviseCode = $firstEstimateDetail->strrevisecode;
$productRevisionNo = $firstEstimateDetail->lngproductrevisionno;

// 製品マスタの情報取得
$product = $objDB->getProduct($productCode, $reviseCode, $productRevisionNo);

$objPreview = new estimatePreviewController();
$objPreview->dataInitialize($product, $estimate, $objDB);

$productData = $objPreview->getProduct();

$estimateData = $objPreview->getEstimate();


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

// シートが表示無効でない場合はワークシート処理オブジェクトのインスタンス生成
$objSheet = new estimateSheetController();

// オブジェクトにデータをセットする
$objSheet->dataInitialize($sheetInfo, $objDB);

// 最新でない、または削除済みの見積原価明細の場合は編集不可能フラグをセットする
if ($maxRevisionNo !== $revisionNo || $minRevisionNo < 0) {
	$objSheet->setUneditableFlag(true);
}

// テンプレートの整形を行う
$objSheet->templateAdjust($estimateData);

// ワークシートオブジェクトに必要な値をセット
$objSheet->setDBEstimateData($productData, $estimateData);

$hiddenList = array();

// 非表示リスト（無効リスト）を追加する
$objSheet->setHiddenRowList($hiddenList);

$viewData = $objSheet->makeDataOfSheet('preview');

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

// ヘッダ部の生成
$header	= makeHTML::getPreviewHeader($maxRevisionNo, $revisionNo, $minRevisionNo); // ヘッダー

// ExcelワークシートHTML取得
$strExcel .= makeHTML::getGridTable($ws_num); // データ挿入タグ
$aryData['revisionNo'] = $revisionNo;

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
$objTemplate->getTemplate( "estimate/preview/parts.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

?>
