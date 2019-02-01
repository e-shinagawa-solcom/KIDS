<?php

// ----------------------------------------------------------------------------
/**
 * 金型履歴管理 登録確認画面*
 *
 * 処理概要
 * ・登録履歴画面を表示
 */
// ----------------------------------------------------------------------------
include('conf.inc');
require(LIB_FILE);
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldReport.class.php');

$objDB = new clsDB ();
$objAuth = new clsAuth ();
$objDB->open ( "", "", "", "" );

$replacement = $_REQUEST;

// セッション確認
$objAuth = fncIsSession ( $replacement ["strSessionID"], $objAuth, $objDB);

// 1800 金型履歴管理
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1804 金型履歴管理(修正)
if ( !fncCheckAuthority( DEF_FUNCTION_MM4, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// キャッシュインスタンスの取得
$formCache = FormCache::getInstance();

// キャッシュ(フォーム)データの取り出し
$resultFormCache = $formCache->get($replacement["resultHash"]);

// キャッシュ(フォーム)データが取り出せた場合
if($resultFormCache && pg_num_rows($resultFormCache) == 1)
{
	// キャッシュレコード取得
	$workCache = pg_fetch_array($resultFormCache, 0, PGSQL_ASSOC);

	// デシリアライズ
	$workFormData = FormCache::deserialize($workCache["serializeddata"]);

	// ユーティリティインスタンスの取得
	$utilBussinesscode = UtilBussinesscode::getInstance();
	$utilMold = UtilMold::getInstance();

	// 業務コードからコード説明を索引
	$replacement["StatusDesc"] = $utilBussinesscode->getDescription('金型ステータス',  $workFormData[FormMoldHistory::Status]);

	// テンプレート読み込み
	$objTemplate = new clsTemplate ();
	$objTemplate->getTemplate ( "/mm/confirm/mm_modify_confirm.html" );

	// デシリアライズ時にUTF-8にしたものをEUC-JPに戻す
	mb_convert_variables("eucjp-win", "utf-8", $workFormData);

	// プレースホルダー置換
	$objTemplate->replace(array_merge($replacement, $workFormData));
	$objTemplate->complete();

	// cookieセット
	setcookie("strSessionID", $_REQUEST["strSessionID"]);
	setcookie("resultHash", $_REQUEST["resultHash"]);

	// html出力
	echo $objTemplate->strTemplate;
}
// キャッシュ(フォーム)データが取り出せなかった場合
else
{
	// キャッシュ取り出し失敗
	fncOutputError(9065, DEF_ERROR, "", TRUE, "", $objDB);
}

function toUTF8($str)
{
	return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"));
}