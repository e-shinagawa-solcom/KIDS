<?php

// ----------------------------------------------------------------------------
/**
*       金型帳票管理  登録
*
*       処理概要
*         ・登録処理
*         ・登録処理完了後、登録完了画面へ
*
*/
// ----------------------------------------------------------------------------
include( 'conf.inc' );
require_once( LIB_FILE );
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilMold.class.php');

$objDB   = new clsDB();
$objAuth = new clsAuth();

// DBオープン
$objDB->open("", "", "", "");

// リクエスト取得
$aryData = $_REQUEST;

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 1900 金型帳票管理
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1901 金型帳票管理(登録)
if ( !fncCheckAuthority( DEF_FUNCTION_MR1, $objAuth ) )
{
	fncOutputError( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// キャッシュインスタンスの取得
$formCache = FormCache::getInstance();
$resultFormCache = $formCache->get($aryData["resultHash"]);

// キャッシュ(フォーム)データが取り出せた場合
if($resultFormCache && pg_num_rows($resultFormCache) == 1)
{
	$result = false;

	// キャッシュレコード取得
	$workCache = pg_fetch_array($resultFormCache, 0, PGSQL_ASSOC);

	// デシリアライズ
	$workFormData = FormCache::deserialize($workCache["serializeddata"]);

	// フォームデータにユーザーコードを追加
	$workFormData["UserCode"] = $objAuth->UserCode;

	// Utilインスタンスの取得
	$utilMold = UtilMold::getInstance();
	$utilMold->setUserCode($objAuth->UserCode);

	// トランザクション開始
	$objDB->transactionBegin();

	// 金型関連テーブルのロック
	pg_query("LOCK m_moldreport");
	pg_query("LOCK t_moldreportdetail");
	pg_query("LOCK t_moldreportrelation");
	pg_query("LOCK t_moldhistory");

	// 金型リストの取り出し
	$molds = $workFormData["list_moldno"];

	// 金型履歴のダイジェスト作成
	$summaryHistory = $utilMold->selectSummaryOfMoldHistory($molds);
	$digestHistory = FormCache::hash_arrays($summaryHistory);

	// 金型帳票のダイジェスト作成
	$summaryReport = $utilMold->selectSummaryOfMoldReport($molds);
	$digestReport = FormCache::hash_arrays($summaryReport);

	// 検証時の金型履歴ダイジェストと異なる場合
	if ($digestHistory != $workFormData["digest_history"])
	{
		// DBエラー
		fncOutputError ( 9051, DEF_ERROR, "選択された金型情報が変更されています。", TRUE, "", $objDB );
	}

	// 検証時の金型帳票ダイジェストと異なる場合
	if ($digestReport != $workFormData["digest_report"])
	{
		// DBエラー
		fncOutputError ( 9051, DEF_ERROR, "選択された金型情報が変更されています。", TRUE, "", $objDB );
	}
	// 金型帳票マスタへのINSERT
	if ($resultMoldReport = $utilMold->insertMoldReport($workFormData))
	{
		$result = $resultMoldReport;
	}
	// 結果が得られなかった場合
	else
	{
		// DBエラー
		fncOutputError ( 9051, DEF_ERROR, "金型帳票マスタへの登録に失敗しました。", TRUE, "", $objDB );
	}

	// 金型帳票詳細へのINSERT
	if ($resultMoldReportDetail = $utilMold->insertMoldReportDetail(
			$result[TableMoldReport::MoldReportId],
			$result[TableMoldReport::Revision],
			$workFormData))
	{
		// INSERT件数の取得
		$result["MoldCount"] = $resultMoldReportDetail;
	}
	// 結果が得られなかった場合
	else
	{
		// DBエラー
		fncOutputError ( 9051, DEF_ERROR, "金型帳票詳細への登録に失敗しました。", TRUE, "", $objDB );
	}

	// 使用したフォームデータをキャッシュテーブルから削除
	if (!$formCache->remove($aryData["resultHash"]))
	{
		// DBエラー
		fncOutputError ( 9051, DEF_ERROR, "キャッシュテーブルのレコード削除に失敗しました。", TRUE, "", $objDB );
	}

	// コミット
	$objDB->transactionCommit();

	// 業務コードユーティリティのインスタンス取得
	$utilBussinesscode = UtilBussinesscode::getInstance();
	// 帳票区分のコード説明(UTF-8)を取得
	$result[TableMoldReport::ReportCategory] =
		$utilBussinesscode->getDescription("帳票区分", $result[TableMoldReport::ReportCategory]);

	// cookieセット
	setcookie("strSessionID", $_REQUEST["strSessionID"]);
	setcookie(TableMoldReport::MoldReportId, $result[TableMoldReport::MoldReportId]);

	// テンプレート読み込み
	$objTemplate = new clsTemplate ();
	$objTemplate->getTemplate ( "/mr/finish/mr_finish.html" );

	// プレースホルダー置換
	$objTemplate->replace($result);
	$objTemplate->complete();

	$doc = new DOMDocument();

	// パースエラー抑制
	libxml_use_internal_errors(true);
	// DOMパース
	$doc->loadHTML($objTemplate->strTemplate);
	// パースエラークリア
	libxml_clear_errors();
	// パースエラー抑制解除
	libxml_use_internal_errors(false);

	$preview = $doc->getElementById("preview");
	$preview->setAttribute("moldreportid", $result[TableMoldReport::MoldReportId]);
	$preview->setAttribute("revision", $result[TableMoldReport::Revision]);
	$preview->setAttribute("version", $result[TableMoldReport::Version]);

	// HTML出力
	echo $doc->saveHTML();
}
// キャッシュ(フォーム)データが取り出せなかった場合
else
{
	// キャッシュ取り出し失敗
	fncOutputError(9065, DEF_ERROR, "", TRUE, "", $objDB);
}

// DBクローズはWithQueryのデストラクタで閉じる為、明示的には行わない