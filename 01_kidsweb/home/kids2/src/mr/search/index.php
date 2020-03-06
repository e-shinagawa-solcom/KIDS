<?php

// ----------------------------------------------------------------------------
/**
*       金型帳票管理  検索画面
*
*       処理概要
*         ・検索画面表示処理
*/
// ----------------------------------------------------------------------------

// 設定の読み込み
include_once ( "conf.inc" );

// ライブラリ読み込み
require ( LIB_FILE );

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
$aryData = $_REQUEST;

setcookie("strSessionID", $aryData["strSessionID"]);

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
// 1900 金型管理
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1902 金型管理（検索）
if ( !fncCheckAuthority( DEF_FUNCTION_MR2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("base_mold.html", "mr/search/mr_search.tmpl", $aryData ,$objAuth );
