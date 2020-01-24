<?php

// -----------------------------------------------------------
//
// 金型帳票クローズ処理
//
// -----------------------------------------------------------

include( 'conf.inc' );
require_once( LIB_FILE );
require_once(SRC_ROOT.'/mold/lib/UtilMold.class.php');

// ログ出力時のプレフィックス
const LOG_PREFIX = "[KIDS-CloseMoldReport] ";

$objDB   = new clsDB();
$objAuth = new clsAuth();

// DBオープン
$objDB->open("", "", "", "");

// リクエスト取得
$aryData = $_REQUEST;

// トランザクション開始
$objDB->transactionBegin();

// Utilインスタンスの取得
$utilMold = UtilMold::getInstance();

// 金型関連テーブルのロック
pg_query("LOCK m_mold");
pg_query("LOCK m_moldreport");
pg_query("LOCK t_moldreportdetail");
pg_query("LOCK t_moldreportrelation");
pg_query("LOCK t_moldhistory");

// 未完了ステータスの金型帳票レコードの取得
$reports = $utilMold->selectUnclosedMoldReport();

// 未完了帳票が0件であればロールバックして完了
if (!$reports)
{
	// ロールバック
	$objDB->transactionRollback();
	syslog(LOG_INFO, LOG_PREFIX."クローズ対象の金型帳票はありませんでした。");
	exit;
}

syslog(LOG_INFO, LOG_PREFIX."金型帳票クローズ処理開始");

// 未完了の帳票件数分走査
foreach ($reports as $report_num => $report_row)
{
	// (更新者から)ユーザIDを設定
	$userCode = $report_row[TableMoldReport::UpdateBy];
	$utilMold->setUserCode($userCode);

	$id = $report_row[TableMoldReport::MoldReportId];
	$revision = $report_row[TableMoldReport::Revision];

	// 紐付く金型帳票詳細の取得
	$details = $utilMold->selectMoldReportDetail($id, $revision);

	// 新規金型履歴レコード
	$newHistory = array();
	// 共通項目設定
	$newHistory[TableMoldHistory::Status] = $report_row[TableMoldReport::ReportCategory];
	$newHistory[TableMoldHistory::ActionDate] = $report_row[TableMoldReport::ActionRequestDate];
	$newHistory[TableMoldHistory::SourceFactory] = $report_row[TableMoldReport::SourceFactory];
	$newHistory[TableMoldHistory::DestinationFactory] = $report_row[TableMoldReport::DestinationFactory];

	// 新規金型帳票関連レコード
	$newRelation = array();
	// 共通項目設定
	$newRelation[TableMoldReportRelation::MoldReportId] = $id;
	$newRelation[TableMoldReportRelation::Revision] = $revision;

	// 金型帳票詳細の件数分走査
	foreach ($details as $detail_num => $detail_row)
	{
		// 金型番号
		$moldNo = $detail_row[TableMoldReportDetail::MoldNo];

		// 金型番号の設定
		$newHistory[TableMoldHistory::MoldNo] = $moldNo;
		$newRelation[TableMoldReportRelation::MoldNo] = $moldNo;

		// 金型履歴へのINSERTの結果が得られなかった場合
		if (!$resultHistory = $utilMold->insertMoldHistory($newHistory))
		{
			// ロールバック
			$objDB->transactionRollback();
			// メッセージ作成
			$message = LOG_PREFIX.
					"金型履歴の作成に失敗しました。"."\n".
					"MoldReportId:".$id."\n".
					"Revision:".$revision."\n".
					"MoldNo:".$moldNo."\n";

			// エラーログ出力
			error_log($message, 0);
			// エラーメール送信
			mb_send_mail(
					ERROR_MAIL_TO,
					"K.I.D.S. Error Message from " . TOP_URL,
					$message,
					"From: " . ERROR_MAIL_TO . "\nReturn-Path: " . ERROR_MAIL_TO . "\n" );
			// 金型履歴の作成失敗
			syslog(LOG_INFO, $message);
			exit;
		}

		// 金型履歴の挿入結果から履歴番号を取得
		$historyNo = $resultHistory[TableMoldHistory::HistoryNo];

		// 金型帳票関連テーブルへのINSERT
		if (!$utilMold->insertMoldReportRelation($moldNo, $historyNo, $id, $revision))
		{
			// ロールバック
			$objDB->transactionRollback();
			// メッセージ作成
			$message = LOG_PREFIX.
					"金型帳票関連の作成に失敗しました。"."\n".
					"MoldReportId:".$id."\n".
					"Revision:".$revision."\n".
					"MoldNo:".$moldNo."\n".
					"HistoryNo:".$historyNo."\n";

			// エラーログ出力
			error_log($message, 0);
			// エラーメール送信
			mb_send_mail(
					ERROR_MAIL_TO,
					"K.I.D.S. Error Message from " . TOP_URL,
					$message,
					"From: " . ERROR_MAIL_TO . "\nReturn-Path: " . ERROR_MAIL_TO . "\n" );

			// 金型帳票関連の作成失敗
			syslog(LOG_INFO, $message);
			exit;
		}
	}

	// 処理対象の金型帳票のステータスを完了(クローズ)に切り替え
	if (!$utilMold->updateCloseMoldReport($id, $revision))
	{
		// ロールバック
		$objDB->transactionRollback();

		// メッセージ作成
		$message = LOG_PREFIX.
		"金型帳票ステータスの更新に失敗しました。"."\n".
		"MoldReportId:".$id."\n".
		"Revision:".$revision."\n";

		// エラーログ出力
		error_log($message, 0);
		// エラーメール送信
		mb_send_mail(
			ERROR_MAIL_TO,
			"K.I.D.S. Error Message from " . TOP_URL,
			$message,
			"From: " . ERROR_MAIL_TO . "\nReturn-Path: " . ERROR_MAIL_TO . "\n" );
		// 金型帳票ステータスの更新失敗
		syslog(LOG_INFO, $message);
		exit;
	}
}

// コミット
$objDB->transactionCommit();

syslog(LOG_INFO, LOG_PREFIX."金型帳票クローズ処理終了");

return;