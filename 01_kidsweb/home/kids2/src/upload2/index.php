<?php
/* ************************************************************************* */
/* UPLOAD Ver.Peruser */
/* ************************************************************************* */

/**
*	ファイルアップロード画面
*
*	@charset	: euc-jp
*/


	include ( 'conf.inc' );		// 設定読み込み
	require ( LIB_DEBUGFILE );	// Debugモジュール

	require ( LIB_FILE );		// ライブラリ読み込み



	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	//-------------------------------------------------------------------------
	// DBオープン
	//-------------------------------------------------------------------------
	$objDB->open( "", "", "", "" );


	//-------------------------------------------------------------------------
	// パラメータ取得
	//-------------------------------------------------------------------------
	$aryData	= $_REQUEST;

	$aryData["lngLanguageCode"]		= $_COOKIE["lngLanguageCode"];	// 言語コード
	$aryData["style"]				= 'segment';					// or "old"
	$aryData["lngFunctionCode"]		= DEF_FUNCTION_E1;				// 管理コード（見積原価）
	$aryData["lngRegistConfirm"]	= 0;							// 確認画面表示フラグ

    setcookie("strSessionID", $aryData["strSessionID"], 0, "/");
	//-------------------------------------------------------------------------
	// 入力文字列値・セッション・権限チェック
	//-------------------------------------------------------------------------
	// 文字列チェック
	$aryCheck["strSessionID"]	= "null:numenglish(32,32)";
	$aryResult	= fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ユーザーコード取得
	$lngUserCode = $objAuth->UserCode;

	// 権限確認
	if( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 権限グループコードの取得
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );


	//-------------------------------------------------------------------------
	// DB Close
	//-------------------------------------------------------------------------
	$objDB->close();
	$objDB->freeResult( $lngResultID );

// fncDebug( 'parse.txt', $_FILES, __FILE__, __LINE__);
// fncDebug( 'parse.txt', fncGetReplacedHtmlWithBase("search/base_search.html", "upload2/parts.tmpl", $aryData ,$objAuth ), __FILE__, __LINE__);

	/*-------------------------------------------------------------------------
		出力
	-------------------------------------------------------------------------*/
	// HTML出力
	echo fncGetReplacedHtmlWithBase("search/base_search.html", "upload2/parts.tmpl", $aryData ,$objAuth );


	unset( $objDB, $objAuth, $aryData, $strTmpFileName );
	return true;

?>
