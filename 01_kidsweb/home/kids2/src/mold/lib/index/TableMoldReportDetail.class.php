<?php

require_once(SRC_ROOT.'/mold/lib/index/TableMetaData.class.php');

/**
 * 金型帳票詳細のテーブル名やカラム名を提供する
 */
class TableMoldReportDetail extends TableMetaData
{
	/**
	 * テーブル名: 金型帳票詳細
	 * @var string
	 */
	const TABLE_NAME = "T_MoldReportDetail";

	/**
	 * <pre>
	 * 金型帳票ID
	 *
	 * 型(postgresql): text
	 *
	 * 関連: 会社マスタ.会社コード
	 * </pre>
	 *
	 * @var string
	 */
	const MoldReportId = "moldreportid";

	/**
	 * <pre>
	 * リビジョン
	 *
	 * 型(postgresql): integer
	 * </pre>
	 *
	 * @var string
	 */
	const Revision = "revision";

	/**
	 * <pre>
	 * 順序
	 *
	 * 型(postgresql): integer
	 * </pre>
	 *
	 * @var string
	 */
	const ListOrder = "listorder";

	/**
	 * <pre>
	 * 金型NO
	 *
	 * 型(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const MoldNo = "moldno";

	/**
	 * <pre>
	 * 金型説明
	 *
	 * 型(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const MoldDescription = "molddescription";
}