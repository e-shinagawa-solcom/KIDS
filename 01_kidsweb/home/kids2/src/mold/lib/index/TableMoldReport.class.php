<?php

require_once(SRC_ROOT.'/mold/lib/index/TableMetaData.class.php');

/**
 * 金型帳票マスタのテーブル名やカラム名を提供する
 */
class TableMoldReport extends TableMetaData
{
	/**
	 * テーブル名: 金型帳票マスタ
	 * @var string
	 */
	const TABLE_NAME = "M_MoldReport";

	/**
	 * <pre>
	 * 金型帳票ID
	 *
	 * 型(postgresql): text
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
	 * 帳票区分
	 *
	 * 型(postgresql): char
	 *
	 * 関連: 業務コードマスタ.業務コード
	 * </pre>
	 *
	 * @var string
	 */
	const ReportCategory = "reportcategory";

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
	 * 依頼日
	 *
	 * 型(postgresql): date
	 * </pre>
	 *
	 * @var string
	 */
	const RequestDate = "requestdate";

	/**
	 * <pre>
	 * TO
	 *
	 * 型(postgresql): integer
	 *
	 * 関連: 会社マスタ.会社コード
	 * </pre>
	 *
	 * @var string
	 */
	const SendTo = "sendto";

	/**
	 * <pre>
	 * ATTN
	 *
	 * 型(postgresql): integer
	 *
	 * 関連: 会社マスタ.会社コード
	 * </pre>
	 *
	 * @var string
	 */
	const Attention = "attention";

	/**
	 * <pre>
	 * CC
	 *
	 * 型(postgresql): integer
	 *
	 * 関連: 会社マスタ.会社コード
	 * </pre>
	 *
	 * @var string
	 */
	const CarbonCopy = "carboncopy";

	/**
	 * <pre>
	 * 製品コード
	 *
	 * 型(postgresql): text
	 *
	 * 関連: 金型マスタ.製品コード
	 * </pre>
	 *
	 * @var string
	 */
	const ProductCode = "productcode";

	/**
	 * <pre>
	 * 顧客品番(商品コード)
	 *
	 * 型(postgresql): text
	 *
	 * 関連: 製品マスタ.顧客品番
	 * </pre>
	 *
	 * @var string
	 */
	const GoodsCode = "goodscode";

	/**
	 * <pre>
	 * 依頼区分
	 *
	 * 型(postgresql): char
	 *
	 * 関連: 業務コードマスタ.業務コード
	 * </pre>
	 *
	 * @var string
	 */
	const RequestCategory = "requestcategory";

	/**
	 * <pre>
	 * 希望日
	 *
	 * 型(postgresql): date
	 * </pre>
	 *
	 * @var string
	 */
	const ActionRequestDate = "actionrequestdate";

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
	 * 移動方法
	 *
	 * 型(postgresql): char
	 *
	 * 関連: 業務コードマスタ.業務コード
	 * </pre>
	 *
	 * @var string
	 */
	const TransferMethod = "transfermethod";

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
	 * 指示区分
	 *
	 * 型(postgresql): text
	 *
	 * 関連: 業務コードマスタ.業務コード
	 * </pre>
	 *
	 * @var string
	 */
	const InstructionCategory = "instructioncategory";

	/**
	 * <pre>
	 * 事業部(顧客)
	 *
	 * 型(postgresql): integer
	 *
	 * 関連: 会社マスタ.会社コード
	 * </pre>
	 *
	 * @var string
	 */
	const CustomerCode = "customercode";

	/**
	 * <pre>
	 * KWG部署
	 *
	 * 型(postgresql): integer
	 *
	 * 関連: グループマスタ.グループコード
	 * </pre>
	 *
	 * @var string
	 */
	const KuwagataGroupCode = "kuwagatagroupcode";

	/**
	 * <pre>
	 * KWG担当者
	 *
	 * 型(postgresql): integer
	 *
	 * 関連: ユーザマスタ.ユーザコード
	 * </pre>
	 *
	 * @var string
	 */
	const KuwagataUserCode = "kuwagatausercode";

	/**
	 * <pre>
	 * その他
	 *
	 * 型(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const Note = "note";

	/**
	 * <pre>
	 * 生産後の処理
	 *
	 * 型(postgresql): char
	 *
	 * 関連: 業務コードマスタ.業務コード
	 * </pre>
	 *
	 * @var string
	 */
	const FinalKeep = "finalkeep";

	/**
	 * <pre>
	 * 返却予定日
	 *
	 * 型(postgresql): date
	 * </pre>
	 *
	 * @var string
	 */
	const ReturnSchedule = "returnschedule";

	/**
	 * <pre>
	 * 欄外備考
	 *
	 * 型(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const MarginalNote = "marginalnote";

	/**
	 * <pre>
	 * 印刷済フラグ
	 *
	 * 型(postgresql): boolean
	 * </pre>
	 *
	 * @var string
	 */
	const Printed = "printed";
}