<?php

/**
 * テーブル共通のメタデータを扱うカラム名を提供する
 */
class TableMetaData
{
	/**
	 * <pre>
	 * レコード作成日時
	 *
	 * 型(postgresql):
	 *     timestamp without time zone
	 * </pre>
	 *
	 * @var string
	 */
	const Created = "created";

	/**
	 * <pre>
	 * 作成者(ユーザコード)
	 *
	 * 型(postgresql): integer
	 * </pre>
	 *
	 * @var string
	 */
	const CreateBy = "createby";

	/**
	 * <pre>
	 * レコード更新日時
	 *
	 * 型(postgresql):
	 *     timestamp without time zone
	 * </pre>
	 *
	 * @var string
	 */
	const Updated = "updated";

	/**
	 * <pre>
	 * 更新者(ユーザコード)
	 *
	 * 型(postgresql): integer
	 * </pre>
	 *
	 * @var string
	 */
	const UpdateBy = "updateby";

	/**
	 * <pre>
	 * バージョン
	 *
	 * 型(postgresql): integer
	 * </pre>
	 *
	 * @var string
	 */
	const Version = "version";

	/**
	 * <pre>
	 * 削除フラグ
	 *
	 * 型(postgresql): boolean
	 * </pre>
	 * @var string
	 */
	const DeleteFlag = "deleteflag";
}