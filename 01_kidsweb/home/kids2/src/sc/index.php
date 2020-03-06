<?php

// ----------------------------------------------------------------------------
/**
*       売上管理  メニュー画面
*
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

	// 600 売上管理
	if ( !fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
	{
	        fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 601 売上管理（ 売上登録）
	if ( fncCheckAuthority( DEF_FUNCTION_SC1, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist2/index.php?strSessionID=" . $aryData["strSessionID"];
	}
	
	// 602 売上管理（ 売上検索）
	if ( fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
	{
		$aryData["strSearchURL"]   = "search/index.php?strSessionID=" . $aryData["strSessionID"];
	}

	// 602 売上管理（ 納品書検索）
	if ( fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
	{
		$aryData["strSearch2URL"]   = "search2/index.php?strSessionID=" . $aryData["strSessionID"];
	}

	// ヘルプ対応
	$aryData["lngFunctionCode"] = DEF_FUNCTION_SC0;

	echo fncGetReplacedHtml( "sc/parts.tmpl", $aryData ,$objAuth );

	$objDB->close();
	return true;
?>