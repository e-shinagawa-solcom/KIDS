<?php
/** 
*	見積原価管理 実行画面
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/

	mb_http_output ( 'UTF-8' );


	require_once ('conf.inc');
	require_once ( LIB_DEBUGFILE );

	// ライブラリ読み込み
	require_once ( LIB_FILE );
    require_once ( LIB_EXCLUSIVEFILE );

	// 見積原価用クラス読み込み
	require_once ( SRC_ROOT . "estimate/cmn/const/workSheetConst.php");
	require_once ( SRC_ROOT . "estimate/cmn/deleteInsertData.php");

	// 見積原価DBクラス読み込み
	require_once ( SRC_ROOT . "estimate/cmn/estimateDB.php");
	
	// html生成クラス読み込み
	require_once ( SRC_ROOT . "estimate/cmn/makeHTML.php");

	$objDB   = new estimateDB();
	$objAuth = new clsAuth();
	
	$objDB->open( "", "", "", "" );

	// POSTデータ取得
	$aryData = $_POST;

// fncDebug( 'estimate_regist_action_data.txt', $aryData["aryDetail"], __FILE__, __LINE__);

	$aryCheck["strSessionID"] = "null:numenglish(32,32)";

	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	
	// エラーメッセージを出力する
	$strErrorMessage = array();

	foreach ( $aryCheckResult as $value ) {
		if ($value)	{
			list ($lngErrorNo, $errorReplace) = explode (":", $value);
			$strErrorMessage[] = fncOutputError ( $lngErrorNo, DEF_ERROR, $errorReplace, FALSE, "", $objDB );
		}
	}


	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode = $objAuth->UserCode;

	$functionCode = $aryData["lngFunctionCode"];


	// 権限確認
	//////////////////////////////////////////////////////////////////////////
	// 見積原価削除以外の場合
	//////////////////////////////////////////////////////////////////////////
	if (!$functionCode == DEF_FUNCTION_E4 || !fncCheckAuthority( $functionCode, $objAuth )) {
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

// 削除する見積原価の情報を取得
$estimateNo = $aryData['estimateNo'];
$revisionNo = $aryData['revisionNo'];

$estimate = $objDB->getEstimateDetail($estimateNo);

//////////////////////////////////////////////////////////////////////
// DB処理開始
//////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

// 排他ロック取得
if(!lockEstimateEdit($estimateNo, DEF_FUNCTION_E4, $objDB, $objAuth))
{
    fncOutputError ( 9051, DEF_ERROR, "見積原価計算書のロックに失敗しました。", TRUE, "", $objDB );
}

// リビジョンと削除済みチェック
if (isEstimateModified($estimateNo, $revisionNo, $objDB)) {
    fncOutputError ( DEF_MESSAGE_CODE_CURRENT_REVISION_ERROR, DEF_WARNING, "", TRUE, "", $objDB );
}

$objRegist = new deleteInsertData();

// 登録に必要なデータをセットする
$objRegist->setDeleteParam($estimateNo, $revisionNo, $lngUserCode, $objDB);

$strErrorMessage = $objRegist->delete();

// 排他ロックの解放
$result = unlockExclusive($objAuth, $objDB);

// 検索でエラーが発生したらエラーメッセージ出力画面に遷移する
if ($strErrorMessage) {
	makeHTML::outputErrorWindow($strErrorMessage);
}

$objDB->transactionCommit();

//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
$firstRecord = $estimate[0];

$completeMessage = "製品コード". $firstRecord->strproductcode. "_". $firstRecord->strrevisecode. "の見積原価データを削除いたしました。";

$postData = array(
	'completeMessage' => $completeMessage
);

$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "estimate/delete/result.tmpl" );

$objTemplate->replace( $postData );
$objTemplate->complete();

//fncDebug( 'es_finish.txt', $objTemplate->strTemplate, __FILE__, __LINE__);

echo $objTemplate->strTemplate;


$objDB->close();


return TRUE;
?>
