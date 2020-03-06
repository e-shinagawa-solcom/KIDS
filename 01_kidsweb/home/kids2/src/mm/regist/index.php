<?php

// ----------------------------------------------------------------------------
/**
*       金型履歴管理  登録画面
*/
// ----------------------------------------------------------------------------

	//-------------------------------------------------------------------------
	// ■ ライブラリファイル読込
	//-------------------------------------------------------------------------
	include( 'conf.inc' );
	require( LIB_FILE );

	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();

	//-------------------------------------------------------------------------
	// ■ DBオープン
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");

	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData = $_POST;

	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];   // セッションID
	$aryData["lngLanguageCode"] = 1; // 言語コード

	setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 1800 金型履歴管理
	if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
	{
		fncOutputError( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 1801 金型履歴管理(登録)
	if ( !fncCheckAuthority( DEF_FUNCTION_MM1, $objAuth ) )
	{
		fncOutputError( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	$objDB->close();

	echo fncGetReplacedHtmlWithBase("base_mold.html", "mm/regist/mm_regist.tmpl", $aryData ,$objAuth);
