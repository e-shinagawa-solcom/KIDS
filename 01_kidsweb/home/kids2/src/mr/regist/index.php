<?php

// ----------------------------------------------------------------------------
/**
*       金型帳票管理  登録画面*
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

	// 1900 金型帳票管理
	if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
	{
		fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 1901 金型帳票管理(登録)
	if ( !fncCheckAuthority( DEF_FUNCTION_MR1, $objAuth ) )
	{
		fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	$objDB->close();

	echo fncGetReplacedHtmlWithBase("base_mold.html", "mr/regist/mr_regist.tmpl", $aryData ,$objAuth);
