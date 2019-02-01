<?php

require_once(SRC_ROOT.'/mold/lib/index/TableMetaData.class.php');

/**
 * 金型帳票関連のテーブル名やカラム名を提供する
 */
class TableMoldReportRelation extends TableMetaData
{
	/**
	 * テーブル名: 金型帳票関連
	 * @var string
	 */
	const TABLE_NAME = "T_MoldReportRelation";

	/**
	 * <pre>
	 * 金型帳票関連ID
	 *
	 * 型(postgresql):
	 * </pre>
	 *
	 * @var string
	 */
	const MoldReportRelationId = "moldreportrelationid";

	/**
	 * <pre>
	 * 金型NO
	 *
	 * 型(postgresql):
	 *
	 * 関連: 金型履歴.金型NO
	 * </pre>
	 *
	 * @var string
	 */
	const MoldNo = "moldno";

	/**
	 * <pre>
	 * 履歴番号
	 *
	 * 型(postgresql):
	 *
	 * 関連: 金型履歴.履歴番号
	 * </pre>
	 *
	 * @var string
	 */
	const HistoryNo = "historyno";

	/**
	 * <pre>
	 * 金型帳票ID
	 *
	 * 型(postgresql):
	 *
	 * 関連: 金型帳票詳細.金型帳票ID
	 * </pre>
	 *
	 * @var string
	 */
	const MoldReportId = "moldreportid";

	/**
	 * <pre>
	 * リビジョン
	 *
	 * 型(postgresql):
	 *
	 * 関連: 金型帳票詳細.リビジョン
	 * </pre>
	 *
	 * @var string
	 */
	const Revision = "revision";
}