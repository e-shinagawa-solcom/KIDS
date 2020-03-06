<?php

/**
*
*	@charset	: utf-8
*/



	require ( 'conf.inc' );										// 設定読み込み
	require ( LIB_DEBUGFILE );									// Debugモジュール

	require ( LIB_ROOT . "mapping/conf_mapping_common.inc" );	// マッピング設定 - 共通
	require ( LIB_ROOT . "mapping/conf_mapping_estimate.inc" );	// マッピング設定 - 見積原価管理

	require ( LIB_FILE );										// ライブラリ読み込み
	require ( LIB_EXCELPARSER_FILE );							// Excel Parser オブジェクト
	require ( LIB_ROOT . "excelparser/lib_excelparser.php" );	// Excel Parser ライブラリ
	require ( CLS_EXCELMAP_FILE );								// Excel Mapping オブジェクト
	require ( "cmn/getWorkSheet2Array.php" );					// ワークシートデータ -> 配列変換モジュール
	require ( "cmn/lib_parse.php" );							// パース処理ライブラリ



	$objDB			= new clsDB();
	$objAuth		= new clsAuth();
	$objTemplate	= new clsTemplate();								// テンプレートオブジェクト生成
	$exc			= new ExcelFileParser( "debug.log", ABC_NO_LOG );	// ABC_NO_LOG  ABC_VAR_DUMP
	$objMap			= new clsMapping();									// マッピングオブジェクト生成

	$aryError		= array();	// エラーチェック配列


	//-------------------------------------------------------------------------
	// Mapping Object 初期設定
	//-------------------------------------------------------------------------
	$objMap->setArrayMap( $aryMapping );	// マッピング配列を設定
	$objMap->setProcessMode( PROC_NORMAL );	// 初回処理モードを設定

	//-------------------------------------------------------------------------
	// DBオープン
	//-------------------------------------------------------------------------
	$objDB->open( "", "", "", "" );

	//-------------------------------------------------------------------------
	// パラメータ取得
	//-------------------------------------------------------------------------
	$aryData	= array();
	$aryData	= $_REQUEST;



	$aryData["CHAR_SET"]			= TMP_CHARSET;					// テンプレート文字コード
	$aryData["lngLanguageCode"]		= $_COOKIE["lngLanguageCode"];	// 言語コード


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
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 権限グループコードの取得
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );


	/*-------------------------------------------------------------------------
		Excelファイルパース処理
	-------------------------------------------------------------------------*/
	$aryError	= fncParseExcelFile( $exc, $aryData );

	// Excelファイルエラーチェック
	if( $aryError["bytError"] )
	{
		fncOutputError ( 9059, DEF_WARNING, $aryError["strError"], TRUE, "", $objDB );
		unset ( $aryError );
		return false;
	}

//fncDebug( 'upload_parse_parse_1.txt', $exc, __FILE__, __LINE__);

	/*-------------------------------------------------------------------------
		ワークシートデータ配列／ワークシート名／ワークシート名一覧／HTMLデータ／FORMデータ取得
	-------------------------------------------------------------------------*/
	$aryWSData		= array();
	$strWSName		= "";
	$strWorkSheet	= "";
	$strExcel		= "";
	$strForm		= "";

	for( $ws_num=0; $ws_num<count($exc->worksheet['name']); $ws_num++ )
	{
		// Excelワークシートデータ -> Array
		$aryWSData	= getWorkSheet2Array( $exc, $objMap, $ws_num );
//fncDebug( 'exc_array.txt', $aryWSData, __FILE__, __LINE__);

		// Excelワークシート名取得
		$strWSName	= getWorkSheetName( $exc, $ws_num );

		// Excelワークシート名一覧HTML返却
		$strWorkSheet	.= getWorkSheetName2HTML( $exc, $ws_num );

		// ExcelワークシートHTML取得
		$strExcel		.= getWorkSheet2HTML( $aryWSData, $strWSName, $ws_num, "select" );
	}

	// FORM取得
	$strForm	= getForm( $exc, 0, $aryData );

//fncDebug( 'upload_parse_parse_00.txt', $strExcel, __FILE__, __LINE__);

	//-------------------------------------------------------------------------
	// DB Close
	//-------------------------------------------------------------------------
	$objDB->close();
	$objDB->freeResult( $lngResultID );


//fncDebug( 'upload_parse_parse2.txt', $strExcel, __FILE__, __LINE__);



	/*-------------------------------------------------------------------------
		出力
	-------------------------------------------------------------------------*/
	$aryData["WORKSHEET"]	= $strWorkSheet;
	$aryData["EXCEL"]		= $strExcel;
	$aryData["FORM"]		= $strForm;
	$aryData["FORM_NAME"]	= FORM_NAME;

//	$aryData["DEBUG"]			= mb_convert_encoding( $_REQUEST['excel_file'], "UTF-8","UTF-8,UTF-8,SJIS,ASCII,JIS" );



	// テンプレート読み込み
	$objTemplate->getTemplate( "upload/parse/parse.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

// debug
//fncDebug( 'upload_parse_parse.txt', $objTemplate->strTemplate, __FILE__, __LINE__);



	// HTML出力
	echo $objTemplate->strTemplate;
	/*-----------------------------------------------------------------------*/



	unset( $exc, $aryWSData, $objMap, $objDB, $objAuth, $objTemplate, $aryData, $strWorkSheet, $strExcel, $strForm );
	return true;

?>
