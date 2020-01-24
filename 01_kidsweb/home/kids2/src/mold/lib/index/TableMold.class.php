<?php

require_once(SRC_ROOT.'/mold/lib/index/TableMetaData.class.php');

/**
 * 金型マスタのテーブル名やカラム名を提供する
 */
class TableMold extends TableMetaData
{
	/**
	 * テーブル名: 金型マスタ
	 * @var string
	 */
	const TABLE_NAME = "M_Mold";

	/**
	 * <pre>
	 * 金型NO
	 *
	 * 型(postgresql): text
	 *
	 * 関連: 仕入詳細.金型NO
	 * </pre>
	 *
	 * @var string
	 */
	const MoldNo = "moldno";

	/**
	 * <pre>
	 * 仕入れ元
	 *
	 * 型(postgresql): integer
	 *
	 * 関連: 会社マスタ.会社コード
	 * </pre>
	 *
	 * @var string
	 */
	const VenderCode = "vendercode";

	/**
	 * <pre>
	 * 製品コード
	 *
	 * 型(postgresql): text
	 * </pre>
	 *
	 * 関連: 仕入詳細.製品コード
	 * @var string
	 */
	const ProductCode = "productcode";

	/**
	 * <pre>
	 * 再販コード
	 *
	 * 型(postgresql): text
	 * </pre>
	 *
	 * 関連: 仕入詳細.再販コード
	 * @var string
	 */
	const ReviseCode = "strrevisecode";



}