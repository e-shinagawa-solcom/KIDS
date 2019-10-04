<?php

/**
*
*	@charset	: EUC-JP
*/

// $post = $_POST;
// var_dump($post);

// exit;

mb_http_output ( 'EUC-JP' );

require_once ('conf.inc');
require_once ( LIB_DEBUGFILE );

// ライブラリ読み込み
require_once ( LIB_FILE );

// 見積原価用クラス読み込み
require_once ( SRC_ROOT . "estimate/cmn/const/workSheetConst.php");
require_once ( SRC_ROOT . "estimate/cmn/updateInsertData.php");

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

$action = $aryData['action'];
$errorAuthority = false;

// 権限確認
if ($action === 'confirm') {
    // 確定処理の場合
    if(!fncCheckAuthority( DEF_FUNCTION_E3, $objAuth )) {
        $errorAuthority = true;
    }
} else if ($action === 'cancel') {
    // 確定取消処理の場合
    if(!fncCheckAuthority( DEF_FUNCTION_E3, $objAuth )) {
        $errorAuthority = true;
    }
} else {
    $errorAuthority = true;
}

// 権限ERRORの場合
if ($errorAuthority) {
    fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

$productCode = $aryData['productCode'];
$reviseCode = $aryData['reviseCode'];
$areaCode = $aryData['areaCode'];
$estimateDetailNoList[] = explode(',', $aryData['estimateDetailNo']);


//////////////////////////////////////////////////////////////////////
// DB処理開始
//////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

$estimateDetailList = $objDB->getEstimateDetail();

foreach ($estimateDetailList as $estimateDetail) {
	$orderNo = null;
	if (isset($errorRevisionFlag)) {
        if ($estimateDetail->lngrevisionno != $revisionNo) {
			echo '最新じゃないです';
			$errorRevisionFlag = true;
			break;
		} else {
			$errorRevisionFlag = false;
		}
	}

    foreach ($estimateDetailNoList as $estimateDetailNo) {
		if ($estimateDetail->lngestimatedetailno == $estimateDetailNo) {
			$orderNo = $estimateDetail->lngorderno;
			// 通過したら変更処理
			if ($action === 'confirm') {
				// 確定処理の場合

			} else if ($action === 'cancel') {
				// 確定取消処理の場合

			}
			break;
		}
	}

	// ステータスチェック
	// $estimateDetail->
	// エリアコードチェック


}


// 登録データの取得


unset($aryData['registJson']);
unset($registJson);

$objRegist = new updateInsertData();

// 登録に必要なデータをセットする
$objRegist->setParam($regist, $objAuth->UserCode, $objDB);
$objRegist->update();

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

	if ($reviseCode === '00') {
		$completeMessage = "製品コード". $productCode. "を新規登録いたしました。";
	} else {
		$completeMessage = "製品コード". $productCode. "を". $productCode. "_". $reviseCode. "として再販登録いたしました。";
	}

	$postData = array(
		'strSessionID' => $sessionID,
		'productCode' => $productCode,
		'reviseCode' => $reviseCode,
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
