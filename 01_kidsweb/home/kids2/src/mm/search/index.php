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

// 1800 金型履歴管理
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1802 金型履歴管理（検索）
if ( !fncCheckAuthority( DEF_FUNCTION_MM2, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// TODO 権限チェックと同時に権限のないユーザーには修正/削除画面の要素を削って表示させる

// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("base_mold.html", "mm/search/mm_search.tmpl", $aryData ,$objAuth );
