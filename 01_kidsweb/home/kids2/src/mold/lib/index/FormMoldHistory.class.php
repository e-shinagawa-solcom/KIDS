<?php

require_once(SRC_ROOT.'/mold/lib/index/FormMoldRegistBase.class.php');

/**
 * 金型履歴処理でのフォームデータにアクセスする為のキーを提供する
 */
class FormMoldHistory extends FormMoldRegistBase
{
	/**
	 * 金型履歴NO
	 *
	 * @var string
	 */
	const HistoryNo = "HistoryNo";

	/**
	 * ステータス
	 *
	 * @var string
	 */
	const Status = "Status";

	/**
	 * 実施日
	 *
	 * @var string
	 */
	const ActionDate = "ActionDate";

	/**
	 * 備考1
	 *
	 * @var string
	 */
	const Remark1 = "Remark1";

	/**
	 * 備考2
	 *
	 * @var string
	 */
	const Remark2 = "Remark2";

	/**
	 * 備考3
	 *
	 * @var string
	 */
	const Remark3 = "Remark3";

	/**
	 * 備考4
	 *
	 * @var string
	 */
	const Remark4 = "Remark4";

}