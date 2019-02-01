<?php

// ----------------------------------------------------------------------------
/**
*       金型帳票管理  機能選択画面
*
*       処理概要
*         ・メニュー画面にて金型帳票管理ボタンを直接押下した際に表示する機能選択画面
*
*       更新履歴
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

	// 1900 金型帳票管理
	if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
	{
	        fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// ヘルプ対応
	$aryData["lngFunctionCode"] = DEF_FUNCTION_MM0;

	$objDB->close();

	echo fncGetReplacedHtmlWithBase("base_mold.html", "/mr/select-function/parts.tmpl", $aryData ,$objAuth );
	return true;
?>
