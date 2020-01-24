<?php

require_once(SRC_ROOT.'/mold/lib/index/FormMoldRegistBase.class.php');

/**
 * 金型帳票処理でのフォームデータにアクセスする為のキーを提供する
 */
class FormMoldReport extends FormMoldRegistBase
{
	/**
	 * 金型帳票ID
	 * @var string
	 */
	const MoldReportId = "MoldReportId";

	/**
	 * リビジョン
	 * @var string
	 */
	const Revision = "Revision";

	/**
	 * 帳票区分
	 * @var string
	 */
	const ReportCategory = "ReportCategory";

	/**
	 * 依頼日
	 * @var string
	 */
	const RequestDate = "RequestDate";

	/**
	 * 依頼区分
	 * @var string
	 */
	const RequestCategory = "RequestCategory";

	/**
	 * 希望日
	 * @var string
	 */
	const ActionRequestDate = "ActionRequestDate";

	/**
	 * 移動方法
	 * @var string
	 */
	const TransferMethod = "TransferMethod";

	/**
	 * 金型説明
	 * @var string
	 */
	const MoldDescription = "MoldDescription";

	/**
	 * 指示区分
	 * @var string
	 */
	const InstructionCategory = "InstructionCategory";

	/**
	 * 事業部(顧客:表示会社コード)
	 * @var string
	 */
	const CustomerCode = "CustomerCode";

	/**
	 * 事業部(顧客:表示会社名)
	 * @var string
	 */
	const CustomerName = "CustomerName";

	/**
	 * 担当グループコード
	 * @var string
	 */
	const KuwagataGroupCode = "KuwagataGroupCode";

	/**
	 * 担当グループ表示名
	 * @var string
	 */
	const KuwagataGroupName = "KuwagataGroupName";

	/**
	 * 担当者(ユーザコード)
	 * @var string
	 */
	const KuwagataUserCode = "KuwagataUserCode";

	/**
	 * 担当者名(ユーザ表示名)
	 * @var string
	 */
	const KuwagataUserName = "KuwagataUserName";

	/**
	 * 生産後の処理
	 * @var string
	 */
	const FinalKeep = "FinalKeep";

	/**
	 * 返却予定日
	 * @var string
	 */
	const ReturnSchedule = "ReturnSchedule";

	/**
	 * 備考
	 * @var string
	 */
	const Note = "Note";

	/**
	 * 欄外備考
	 * @var string
	 */
	const MarginalNote = "MarginalNote";

	/**
	 * バージョン
	 * @var string
	 */
	const Version = "Version";
}