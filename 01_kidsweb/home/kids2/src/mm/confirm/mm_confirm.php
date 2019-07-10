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

// 1801 金型履歴管理(登録)
if ( !fncCheckAuthority( DEF_FUNCTION_MM1, $objAuth ) )
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
	$objTemplate->getTemplate ( "/mm/confirm/mm_confirm.html" );

	// デシリアライズ時にUTF-8にしたものをEUC-JPに戻す
	mb_convert_variables("eucjp-win", "utf-8", $workFormData);

	// プレースホルダー置換
	$objTemplate->replace(array_merge($replacement, $workFormData));
	$objTemplate->complete();

	// 金型NOの抽出
	$listMoldNo = UtilMold::extractArray($workFormData, FormMoldReport::MoldNo);

	// 金型テーブル生成の為DOMDocumentを使用
	$doc = new DOMDocument();

	// パースエラー抑制
	libxml_use_internal_errors(true);
	// DOMパース
	$doc->loadHTML(mb_convert_encoding($objTemplate->strTemplate, "utf8", "eucjp-win"));
	// パースエラークリア
	libxml_clear_errors();
	// パースエラー抑制解除
	libxml_use_internal_errors(false);

	// 金型テーブルの取得
	$moldTable = $doc->getElementById("MoldTable");

	// 金型NOの件数分走査
	for($i = 1; $i <= count($listMoldNo); $i++)
	{
		// 金型テーブルのtr作成
		$elmTableRacord = $doc->createElement("tr");

		// 金型テーブルのtd要素作成
		$elmTableCellIndex = $doc->createElement("td");
		$elmTableCellMoldNo = $doc->createElement("td");

		// td要素内のテキスト設定
		$elmTableCellIndex->appendChild($doc->createTextNode($i));
		$elmTableCellMoldNo->appendChild($doc->createTextNode(toUTF8($listMoldNo[FormMoldReport::MoldNo.$i])));

		// td要素をtr要素に追加
		$elmTableRacord->appendChild($elmTableCellIndex);
		$elmTableRacord->appendChild($elmTableCellMoldNo);

		// 金型テーブルへtr要素を追加
		$moldTable->appendChild($elmTableRacord);
	}

	// cookieセット
	setcookie("strSessionID", $_REQUEST["strSessionID"]);
	setcookie("resultHash", $_REQUEST["resultHash"]);

	// html出力
	echo $doc->saveHTML();
}
// キャッシュ(フォーム)データが取り出せなかった場合
else
{
	// キャッシュ取り出し失敗
	fncOutputError(9065, DEF_ERROR, "", TRUE, "", $objDB);
}

function toUTF8($str)
{
	return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"), ENT_QUOTES, 'utf-8');
}