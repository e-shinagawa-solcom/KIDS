<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  メニュー画面
*
*       処理概要
*         ・メニュー画面を表示
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


	// 400	受注管理
	if ( !fncCheckAuthority( DEF_FUNCTION_SO0, $objAuth ) )
	{
	        fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	
	// 402 受注管理（受注検索）
	if ( fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
	{
		$aryData["strSearchURL"]   = "search/index.php?strSessionID=" . $aryData["strSessionID"];
	}

	// ヘルプ対応
	$aryData["lngFunctionCode"] = DEF_FUNCTION_SO0;

	echo fncGetReplacedHtml( "so/parts.tmpl", $aryData ,$objAuth );

	$objDB->close();
	return true;
?>