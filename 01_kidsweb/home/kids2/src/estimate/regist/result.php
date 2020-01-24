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
	require_once ( SRC_ROOT . "estimate/cmn/registInsertData.php");

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


	// 権限確認
	//////////////////////////////////////////////////////////////////////////
	// 見積原価登録の場合
	//////////////////////////////////////////////////////////////////////////
	if ($aryData["lngFunctionCode"] == DEF_FUNCTION_E1 && fncCheckAuthority( DEF_FUNCTION_E1, $objAuth )) {
		
	}
	//////////////////////////////////////////////////////////////////////////
	// それ以外(権限ERROR)
	//////////////////////////////////////////////////////////////////////////
	else {
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	

//////////////////////////////////////////////////////////////////////
// DB処理開始
//////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

// 登録データの取得
$registJson = str_replace('/quot/', '"', $aryData['registJson']);
$regist = json_decode($registJson, true);
mb_convert_variables('EUC-JP', 'UTF-8', $regist);

unset($aryData['registJson']);
unset($registJson);

$objRegist = new registInsertData();

// 登録に必要なデータをセットする
$objRegist->setRegistParam($regist, $objAuth->UserCode, $objDB);

// 見積原価登録の場合、INSERT
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 ) {
	$objRegist->regist();
}

$objDB->transactionCommit();

//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////

// 見積原価情報の場合
if( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 )
{
	$sessionID = $aryData['strSessionID'];
	$productCode = $objRegist->getProductCode();
	$reviseCode = $objRegist->getReviseCode();
	$estimateNo = $objRegist->getEstimateNo();

	if ($reviseCode === '00') {
		$completeMessage = "製品コード". $productCode. "を新規登録いたしました。";
	} else {
		$completeMessage = "製品コード". $productCode. "を". $productCode. "_". $reviseCode. "として再販登録いたしました。";
	}

	$postData = array(
		'strSessionID' => $sessionID,
		'productCode' => $productCode,
		'reviseCode' => $reviseCode,
		'estimateNo' => $estimateNo,
		'completeMessage' => $completeMessage
	);

	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "estimate/regist/result.tmpl" );

	$objTemplate->replace( $postData );
	$objTemplate->complete();

//fncDebug( 'es_finish.txt', $objTemplate->strTemplate, __FILE__, __LINE__);
	echo $objTemplate->strTemplate;
}


$objDB->close();


return TRUE;
?>
