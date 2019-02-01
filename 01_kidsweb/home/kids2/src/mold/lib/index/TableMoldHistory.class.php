<?php

require_once(SRC_ROOT.'/mold/lib/index/TableMetaData.class.php');

/**
 * 金型履歴のテーブル名やカラム名を提供する
 */
class TableMoldHistory extends TableMetaData
{
	/**
	 * テーブル名: 金型履歴
	 * @var string
	 */
	const TABLE_NAME = "T_MoldHistory";

	/**
	 * <pre>
	 * 金型NO
	 *
	 * 型(postgresql): text
	 *
	 * 関連: 金型マスタ.金型NO
	 * </pre>
	 *
	 * @var string
	 */
	const MoldNo = "moldno";

	/**
	 * <pre>
	 * 金型履歴NO
	 *
	 * 型(postgresql): integer
	 * </pre>
	 *
	 * @var string
	 */
	const HistoryNo = "historyno";

	/**
	 * <pre>
	 * ステータス
	 *
	 * 型(postgresql): char
	 *
	 * 関連: 業務コードマスタ.業務コード
	 * </pre>
	 *
	 * @var string
	 */
	const Status = "status";

	/**
	 * <pre>
	 * 実施日
	 *
	 * 型(postgresql): date
	 * </pre>
	 *
	 * @var string
	 */
	const ActionDate = "actiondate";

	/**
	 * <pre>
	 * 保管元工場
	 *
	 * 型(postgresql): integer
	 *
	 * 関連: 会社マスタ.会社コード
	 * </pre>
	 *
	 * @var string
	 */
	const SourceFactory = "sourcefactory";

	/**
	 * <pre>
	 * 移動先工場
	 *
	 * 型(postgresql): integer
	 *
	 * 関連: 会社マスタ.会社コード
	 * </pre>
	 *
	 * @var string
	 */
	const DestinationFactory = "destinationfactory";

	/**
	 * <pre>
	 * 備考1
	 *
	 * 型(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const Remark1 = "remark1";

	/**
	 * <pre>
	 * 備考2
	 *
	 * 型(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const Remark2 = "remark2";

	/**
	 * <pre>
	 * 備考3
	 *
	 * 型(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const Remark3 = "remark3";

	/**
	 * <pre>
	 * 備考4
	 *
	 * 型(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const Remark4 = "remark4";
}