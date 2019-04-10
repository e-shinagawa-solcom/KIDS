<?php

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
	$aryData["strActionScriptName"]	= '/upload/parse/parse.php';	// クエリー実行スクリプトパス
	$aryData["lngRegistConfirm"]	= 0;							// 確認画面表示フラグ


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





	/*-------------------------------------------------------------------------
		テンポラリファイル作成
	-------------------------------------------------------------------------*/
	if( $_FILES )
	{
		// テンポラリファイル作成、ファイル名取得
		$strTmpFileName	= getTempFileName( $_FILES['excel_file']['tmp_name'] );

		// ファイル情報の取得
		$aryData["exc_name"]			= $_FILES['excel_file']['name'];
		$aryData["exc_type"]			= $_FILES['excel_file']['type'];
		$aryData["exc_tmp_name"]		= $strTmpFileName;
		$aryData["exc_error"]			= $_FILES['excel_file']['error'];
		$aryData["exc_size"]			= $_FILES['excel_file']['size'];

		$aryData["lngRegistConfirm"]	= 1;	// 確認画面表示フラグ
	}


//fncDebug( 'parse.txt', $_FILES, __FILE__, __LINE__);
fncDebug( 'parse.txt', fncGetReplacedHtml( "upload/parts.tmpl", $aryData, $objAuth ), __FILE__, __LINE__);

	/*-------------------------------------------------------------------------
		出力
	-------------------------------------------------------------------------*/
	// HTML出力
	echo fncGetReplacedHtml( "upload/parts.tmpl", $aryData, $objAuth );


	unset( $objDB, $objAuth, $aryData, $strTmpFileName );
	return true;

?>
