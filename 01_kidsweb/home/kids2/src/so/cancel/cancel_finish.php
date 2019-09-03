<?php

// ----------------------------------------------------------------------------
/**
 *       受注管理  確定取消完了
 *
 *       処理概要
 *         ・受注情報の確定取消処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

include 'conf.inc';
require LIB_FILE;
require (SRC_ROOT . "so/cmn/lib_so.php");

//////////////////////////////////////////////////////////////////////////
// GETデータ取得
//////////////////////////////////////////////////////////////////////////
$postdata = file_get_contents("php://input");
$aryData = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

//値が存在しない場合は通常の POST で受ける
if ($aryData == null) {
    $aryData = $_POST;
}

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);
// 権限確認
// 402 受注管理（受注検索）
if (!fncCheckAuthority(DEF_FUNCTION_SO2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 404 受注管理（確定）
if (!fncCheckAuthority(DEF_FUNCTION_SO4, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 指定受注番号の受注データ取得用SQL文の作成
$strQuery = fncGetReceiveHeadNoToInfoSQL($aryData["lngReceiveNo"], DEF_RECEIVE_ORDER);

// 詳細データの取得
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum )
{
	if ( $lngResultNum == 1 )
	{
		$aryResult = $objDB->fetchArray( $lngResultID, 0 );
	}
	else
	{
		fncOutputError( 403, DEF_ERROR, "該当データの取得に失敗しました", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
}
else
{
	fncOutputError( 403, DEF_ERROR, "データが異常です", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

$objDB->freeResult( $lngResultID );

$aryQuery = array();
$aryQuery[] = "UPDATE m_receive ";
$aryQuery[] = "set lngreceivestatuscode = " . DEF_RECEIVE_PREORDER . " ";
$aryQuery[] = "where lngreceiveno = " . $aryData["lngReceiveNo"] . " ";
$strQuery = implode("\n", $aryQuery);
//結果配列
$result = array();
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

$objDB->freeResult($lngResultID);


////////// 明細行の取得 ////////////////////
// 指定受注番号の受注明細データ取得用SQL文の作成
$strQuery = fncGetReceiveDetailNoToInfoSQL ($aryData["lngReceiveNo"]);

// 明細データの取得
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	if ( $lngResultNum == 1 )
	{
		$aryDetailResult = $objDB->fetchArray( $lngResultID, 0);
	}
}
else
{
	$strMessage = fncOutputError( 403, DEF_WARNING, "受注番号に対する明細情報が見つかりません。", FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

$objDB->freeResult( $lngResultID );



// 通貨記号の設定
if ($aryResult["lngmonetaryunitcode"] == 1) {
    $aryResult["strmonetaryunitsign"] = "&yen;";
}

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "so/cancel/so_finish_cancel.html" );

// テンプレート生成
$objTemplate->replace( $aryResult);
$objTemplate->replace( $aryDetailResult);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;
