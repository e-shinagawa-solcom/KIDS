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

	mb_http_output ( 'EUC-JP' );


	require_once ('conf.inc');
	require_once ( LIB_DEBUGFILE );

	// ライブラリ読み込み
	require_once ( LIB_FILE );

	// 見積原価用クラス読み込み
	require_once ( SRC_ROOT . "estimate/cmn/const/workSheetConst.php");
	require_once ( SRC_ROOT . "estimate/cmn/deleteInsertData.php");

	// 見積原価DBクラス読み込み
    require_once ( SRC_ROOT . "estimate/cmn/estimateDB.php");

	$objDB   = new estimateDB();
	$objAuth = new clsAuth();
	
	$objDB->open( "", "", "", "" );

	// POSTデータ取得
	$aryData = $_POST;

// fncDebug( 'estimate_regist_action_data.txt', $aryData["aryDetail"], __FILE__, __LINE__);

	$aryCheck["strSessionID"] = "null:numenglish(32,32)";

	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );
	unset ( $aryCheck );


	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode = $objAuth->UserCode;

	$functionCode = $aryData["lngFunctionCode"];


	// 権限確認
	//////////////////////////////////////////////////////////////////////////
	// 見積原価削除以外の場合
	//////////////////////////////////////////////////////////////////////////
	if (!$functionCode == DEF_FUNCTION_E4 || !fncCheckAuthority( $functionCode, $objAuth )) {
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

// 削除する見積原価の情報を取得
$estimateNo = $aryData['estimateNo'];
$revisionNo = $aryData['revisionNo'];

$estimate = $objDB->getEstimateDetail($estimateNo);

//////////////////////////////////////////////////////////////////////
// DB処理開始
//////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

$objRegist = new deleteInsertData();

// 登録に必要なデータをセットする
$objRegist->setDeleteParam($estimateNo, $revisionNo, $lngUserCode, $objDB);

$errorMessage = $objRegist->delete();

if ($errorMessage) {

	$aryHtml["strErrorMessage"] = $errorMessage;

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

$objDB->transactionCommit();

//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////


$completeMessage = "製品コード". $productCode. "_". $reviseCode. "の見積原価データを削除いたしました。";

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
