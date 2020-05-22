<?php

// ----------------------------------------------------------------------------
/**
*       金型管理  一覧検索画面
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

// 1800 金型管理
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1806 金型管理（一覧検索）
if ( !fncCheckAuthority( DEF_FUNCTION_MM6, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// TODO 権限チェックと同時に権限のないユーザーには修正/削除画面の要素を削って表示させる

// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("base_mold.html", "mm/list/mm_list_search.html", $aryData ,$objAuth );
