<?php

/**
*	ファイル概要	: マッピング共通設定
*
*	@charset	: utf-8
*/



	define ( "ENC_CHARSET",		"UTF-8" );			// 処理文字コード

	define ( "APP_EXCEL_TYPE",	"application/vnd.ms-excel" );	// Excelアプリケーションタイプ

	define ( "TMP_CHARSET",		"utf-8" );			// パース処理テンプレート文字コード
	define ( "FORM_NAME",		"frmFile" );			// フォーム名称
	define ( "PARSE_EOF",		"__EOF__" );			// パース終了キー
	define ( "COL_EOF_INT",		11 );				// パース終了列 - 数値
	define ( "COL_EOF",		"K" );				// パース終了列 - 文字列

	define ( "SPLIT_TYPE_CHAR",	".:" );				// 「Cell値」データ型変換区切り文字
	define ( "SPLIT_CODE_CHAR",	":" );				// 「コード・名前」分割区切り文字

	define ( "ROW",			"row" );			// ワークシート行番号
	define ( "COL",			"col" );			// ワークシート列文字列

	define ( "DECIMAL_DIGIT_US",	4 );				// USドル小数点桁数
	define ( "DECIMAL_DIGIT_HKD",	4 );				// HKD小数点桁数
	define ( "DECIMAL_DIGIT_YEN",	2 );				// 円小数点桁数


?>
