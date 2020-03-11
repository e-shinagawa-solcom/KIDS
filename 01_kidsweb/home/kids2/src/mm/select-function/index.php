<?php

// ----------------------------------------------------------------------------
/**
*       金型履歴管理  機能選択画面
*
*       処理概要
*         ・メニュー画面にて金型履歴管理ボタンを直接押下した際に機能選択画面を表示する
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

	// 1800 金型履歴管理
	if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
	{
	        fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 1801 金型履歴登録
	if ( fncCheckAuthority( DEF_FUNCTION_MM1, $objAuth ) )
	{
		$aryData["Regist_visibility"] = 'style="visibility: visible"';
	} else {
		$aryData["Regist_visibility"] = 'style="visibility: hidden"';
	}
	
	// 1802 金型履歴検索
	if ( fncCheckAuthority( DEF_FUNCTION_MM2, $objAuth ) )
	{
		$aryData["Search_visibility"] = 'style="visibility: visible"';
	} else {
		$aryData["Search_visibility"] = 'style="visibility: hidden"';
	}
	$objDB->close();

	echo fncGetReplacedHtmlWithBase("base_mold.html", "/mm/select-function/parts.tmpl", $aryData ,$objAuth );
	return true;
?>