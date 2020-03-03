<?php

require ( 'conf.inc' );										// 設定読み込み
require ( LIB_DEBUGFILE );									// Debugモジュール

require ( LIB_FILE );										// ライブラリ読み込み

$aryData = $_POST;

$message = $aryData['message'];

// [lngLanguageCode]書き出し
$aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"] ? $aryData["lngLanguageCode"] : '0';

// [strErrorMessage]書き出し
$aryHtml["strErrorMessage"] = "ERROR! ". $message;

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "/estimate/error/parts.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryHtml );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

exit;