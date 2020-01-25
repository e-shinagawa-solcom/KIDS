<?php
/* ************************************************************************* */
/* UPLOAD Ver.Peruser */
/* ************************************************************************* */

/**
*
*	@charset	: utf-8
*/



	require ( 'conf.inc' );										// 設定読み込み
	require ( LIB_DEBUGFILE );									// Debugモジュール

	require ( LIB_ROOT . "mapping/conf_mapping_common.inc" );	// マッピング設定 - 共通
	require ( LIB_ROOT . "mapping/conf_mapping_estimate.inc" );	// マッピング設定 - 見積原価管理

	require ( LIB_FILE );										// ライブラリ読み込み
//	require ( LIB_EXCELPARSER_FILE );							// Excel Parser オブジェクト
	require_once ( '../cmn/peruser.php' );
	require_once ( '../cmn/lib_peruser.php' );
	
	
//	require ( LIB_ROOT . "excelparser/lib_excelparser.php" );	// Excel Parser ライブラリ
	require ( CLS_EXCELMAP_FILE );								// Excel Mapping オブジェクト
	require ( "cmn/getWorkSheet2Array.php" );					// ワークシートデータ -> 配列変換モジュール
	require ( "cmn/lib_parse.php" );							// パース処理ライブラリ

	$charset='utf-8';


	$objDB			= new clsDB();
	$objAuth		= new clsAuth();
	$objTemplate	= new clsTemplate();								// テンプレートオブジェクト生成
	//$exc			= new ExcelFileParser( "debug.log", ABC_NO_LOG );	// ABC_NO_LOG  ABC_VAR_DUMP

	$objExc			= new Excel_Peruser;
	$objExc->setErrorHandling(1);
	$objExc->setInternalCharset($charset);

	$objMap			= new clsMapping();									// マッピングオブジェクト生成

	$aryError		= array();	// エラーチェック配列



//$methods = get_class_methods("Excel_Peruser");
//fncDebug( 'upload_parse_parse_0.txt', $methods, __FILE__, __LINE__);


	//-------------------------------------------------------------------------
	// Mapping Object 初期設定
	//-------------------------------------------------------------------------
	$objMap->setArrayMap( $aryMapping );	// マッピング配列を設定
	$objMap->setProcessMode( PROC_NORMAL );	// 初回処理モードを設定

	//-------------------------------------------------------------------------
	// DBオープン
	//-------------------------------------------------------------------------
	$objDB->InputEncoding = 'UTF-8';
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
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 権限グループコードの取得
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );


	/*-------------------------------------------------------------------------
		Excelファイルパース処理
	-------------------------------------------------------------------------*/
	$aryError	= fncParseExcelFile( $objExc, $aryData );

	if ($objExc->isError($result)) {
		$errmes=$result->getMessage();
		if (strpos($errmes,$_FILES['userfile']['tmp_name'])!==false)
			$errmes=str_replace($_FILES['userfile']['tmp_name'],$_FILES['userfile']['name'],$errmes);
			$errmes=str_replace('Template file','Uploaded file',$errmes);
			var_dump($errmes);
	} else {
//		putcss($objExc);
	}

fncDebug( 'upload_parse_parse_1.txt', $objExc, __FILE__, __LINE__);

//exit;



	// Excelファイルエラーチェック
	if( $aryError["bytError"] )
	{
		fncOutputError ( 9059, DEF_WARNING, $aryError["strError"], TRUE, "", $objDB );
		unset ( $aryError );
		return false;
	}

//	putcss($objExc);


//fncDebug( 'upload_parse_parse_2.txt', $objExc->sheetnum, __FILE__, __LINE__, 'a');


	/*-------------------------------------------------------------------------
		ワークシートデータ配列／ワークシート名／ワークシート名一覧／HTMLデータ／FORMデータ取得
	-------------------------------------------------------------------------*/
	$aryWSData		= array();
	$strWSName		= "";
	$strWorkSheet	= "";
	$strExcel		= "";
	$strForm		= "";
	$strCSS			= "";
	$css_rowstyle	= "";
	
	$strCSS		= '<style type="text/css">'."\n_%css_rowstyle%_</style>\n";

	for( $ws_num=0; $ws_num < $objExc->sheetnum; $ws_num++ )
	{
		// Excelワークシートデータ -> Array
		$aryWSData	= getWorkSheet2Array( $objExc, $objMap, $ws_num );
//fncDebug( 'exc_array.txt', $aryWSData, __FILE__, __LINE__);


		// Excelワークシート名取得
		$strWSName	= getWorkSheetName( $objExc, $ws_num );

		// Excelワークシート名一覧HTML返却
		$strWorkSheet	.= getWorkSheetName2HTML( $objExc, $ws_num );

		// ExcelワークシートHTML取得
		$strExcel		.= getWorkSheet2HTML( $aryWSData, $strWSName, $ws_num, "select", $objExc );

		$css_rowstyle .= '.rowstyle'.$ws_num.' { display:none;}'."\n";

	}

	$strExcel = str_replace('_%css_rowstyle%_', $css_rowstyle, $strCSS). $strExcel;


	// FORM取得
	$strForm	= getForm( $objExc, 0, $aryData );

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




	// テンプレート読み込み
	$objTemplate->getTemplate( "upload2/parse/parse.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

// debug
fncDebug( 'upload_parse_parse.txt', $objTemplate->strTemplate, __FILE__, __LINE__);



	// HTML出力
	echo $objTemplate->strTemplate;
	/*-----------------------------------------------------------------------*/



	unset( $exc, $aryWSData, $objMap, $objDB, $objAuth, $objTemplate, $aryData, $strWorkSheet, $strExcel, $strForm );
	return true;

?>
