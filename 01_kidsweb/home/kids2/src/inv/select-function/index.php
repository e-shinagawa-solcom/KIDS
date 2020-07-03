<?php

// ----------------------------------------------------------------------------
/**
*       請求管理  機能選択画面
*
*       処理概要
*         ・メニュー画面にて請求管理ボタンを直接押下した際に機能選択画面を表示する
*
*/
// ----------------------------------------------------------------------------

	// 読み込み
	include('conf.inc');
	require (LIB_FILE);


	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	$objDB->open("", "", "", "");


	$aryData["strSessionID"] = $_POST["strSessionID"];

	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $_POST["strSessionID"], $objAuth, $objDB );
	// クッキーにセッションIDをセット
	setcookie("strSessionID",$_POST["strSessionID"]);

	// 2200 請求管理
	if ( !fncCheckAuthority( DEF_FUNCTION_INV0, $objAuth ) )
	{
	        fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 2201 請求書登録
	if ( fncCheckAuthority( DEF_FUNCTION_INV1, $objAuth ) )
	{
		$aryData["Regist_visibility"] = 'style="visibility: visible"';
	} else {
		$aryData["Regist_visibility"] = 'style="visibility: hidden"';
	}
	
	// 2202 請求書検索
	if ( fncCheckAuthority( DEF_FUNCTION_INV2, $objAuth ) )
	{
		$aryData["Search_visibility"] = 'style="visibility: visible"';
	} else {
		$aryData["Search_visibility"] = 'style="visibility: hidden"';
	}

	// 2204 請求集計
	if ( fncCheckAuthority( DEF_FUNCTION_INV4, $objAuth ) )
	{
		$aryData["Aggregate_visibility"] = 'style="visibility: visible"';
	} else {
		$aryData["Aggregate_visibility"] = 'style="visibility: hidden"';
	}
	
	// // ヘルプ対応
	// $aryData["lngFunctionCode"] = DEF_FUNCTION_INV0;

	$objDB->close();

	echo fncGetReplacedHtmlWithBase("base_mold.html", "inv/select-function/parts.tmpl", $aryData ,$objAuth );
	return true;
?>