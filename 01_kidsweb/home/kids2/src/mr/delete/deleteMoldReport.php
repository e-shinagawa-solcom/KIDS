<?php

// ----------------------------------------------------------------------------
/**
 * 金型帳票管理 削除処理
 */
// ----------------------------------------------------------------------------
include('conf.inc');
require(LIB_FILE);
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilGroup.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilUser.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilCompany.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReportDetail.class.php');

$objDB = new clsDB ();
$objAuth = new clsAuth ();
$objDB->open ( "", "", "", "" );

// セッション確認
$objAuth = fncIsSession ($_REQUEST["strSessionID"], $objAuth, $objDB);

// 1900 金型帳票管理
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1905 金型帳票管理(削除)
if ( !fncCheckAuthority( DEF_FUNCTION_MR5, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

$moldReportId = $_REQUEST["MoldReportId"];
$revision = $_REQUEST["Revision"];
$version = $_REQUEST["Version"];

// 金型帳票ID又はリビジョンの指定がない場合
if (!$moldReportId || !(0 <= $revision) || !(0 <= $version))
{
	// 情報取得エラー
	fncOutputError(9061, DEF_ERROR, "パラメータが不正です。", TRUE, "", $objDB);
}

// ユーティリティのインスタンス取得
$utilMold = UtilMold::getInstance();
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();
$utilProduct = UtilProduct::getInstance();

$objDB->transactionBegin();

// 金型帳票の取得
if (!$report = $utilMold->selectMoldReport($moldReportId, $revision, $version))
{
	// 情報取得エラー
	fncOutputError(9061, DEF_ERROR, "金型帳票の取得に失敗しました。対象データが変更された可能性があります。", TRUE, "", $objDB);
}

// 金型帳票詳細の取得
if (!$detail = $utilMold->selectMoldReportDetail($moldReportId, $revision))
{
	// 情報取得エラー
	fncOutputError(9061, DEF_ERROR, "金型帳票詳細の取得に失敗しました。", TRUE, "", $objDB);
}

// 金型帳票関連の取得(関連の有無は不問)
// 索引できた場合は履歴を索引する
if ($relation  = $utilMold->selectMoldReportRelationByReport($moldReportId, $revision))
{
	foreach ($relation as $row => $columns)
	{
		$moldNo = $columns[TableMoldReportRelation::MoldNo];
		$historyNo = $columns[TableMoldReportRelation::HistoryNo];

		$history[] = $utilMold->selectMoldHistoryWithoutVersion($moldNo, $historyNo);
	}
}

try
{
	// 金型帳票の無効化
	$utilMold->disableMoldReport($moldReportId, $revision, $report[TableMoldReport::Version]);
	// 金型帳票詳細の無効化
	$utilMold->disableMoldReportDetail($moldReportId, $revision);

	// 金型帳票関連レコードが存在する場合
	if($relation)
	{
		// 金型帳票関連の無効化
		$utilMold->disableMoldReportRelationByReport($moldReportId, $revision);

		// 金型履歴件数分操作
		foreach ($history as $row => $columns)
		{
			$moldNo = $columns[TableMoldHistory::MoldNo];
			$historyNo = $columns[TableMoldHistory::HistoryNo];
			$hisVersion = $columns[TableMoldHistory::Version];

			// 金型履歴の無効化
			$utilMold->disableMoldHistory($moldNo, $historyNo, $hisVersion);
		}
	}
}
catch (SQLException $e)
{
	// ロールバック
	$objDB->transactionRollback();
	// 情報取得エラー
	fncOutputError(9061, DEF_ERROR, "対象データが変更された可能性があります。", TRUE, "", $objDB);
}

// コミット
$objDB->transactionCommit();

// テンプレート読み込み
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ("/mr/delete/mr_finish_delete.html");

// プレースホルダー置換
$objTemplate->replace($_REQUEST);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;
