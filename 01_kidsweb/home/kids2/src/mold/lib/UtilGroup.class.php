<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

/**
 * グループマスタに関連する処理を提供する
 * clsDBを使用しているが処理が不完全な為注意すること
 *
 * @see clsDB
 */
class UtilGroup extends WithQuery
{
	/**
	 * グループコードを基にグループの表示名を取得する
	 *
	 * @param string $groupCode グループコード
	 * @return 表示グループ名
	 */
	public function selectDisplayNameByGroupCode($groupCode)
	{
		$result = false;

		if(is_string($groupCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"groupCode" => pg_escape_string($groupCode)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$record = pg_fetch_array($pgResult, 0);
					// 表示名の取得
					$result = $record["strgroupdisplayname"];
				}
				else
				{
					throw new SQLException(
							"検索条件に一致するレコードが存在しませんでした。",
							$query,
							$param
					);
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($groupCode)
					);
		}

		return $result;
	}

	/**
	 * グループコードを基に表示コードを取得する
	 *
	 * @param string $groupCode グループコード
	 * @return 表示グループ名
	 */
	public function selectDisplayCodeByGroupCode($groupCode)
	{
		$result = false;

		if(is_string($groupCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"groupCode" => pg_escape_string($groupCode)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$record = pg_fetch_array($pgResult, 0);
					// 表示名の取得
					$result = $record["strgroupdisplaycode"];
				}
				else
				{
					throw new SQLException(
							"検索条件に一致するレコードが存在しませんでした。",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($groupCode)
					);
		}

		return $result;
	}

	/**
	 * 表示グループコードを基にグループコードを取得する
	 *
	 * @param string $displayGroupCode 表示グループコード
	 * @param boolean $required 索引結果必須フラグ
	 * @return グループコード
	 */
	public function selectGroupCodeByDisplayGroupCode($displayGroupCode, $required = true)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array(
				"strgroupdisplaycode" => pg_escape_string($displayGroupCode)
		);

		// 業務コードの説明を取得する
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// 一致する行が存在する場合
			if (1 <= pg_num_rows($pgResult))
			{
				$record = pg_fetch_array($pgResult, 0);
				// 表示名の取得
				$result = $record["lnggroupcode"];
			}
			else
			{
				// 結果が必須の場合
				if ($required)
				{
					throw new SQLException(
							"検索条件に一致するレコードが存在しませんでした。",
							$query,
							$param
							);
				}
			}
		}
		else
		{
			throw new SQLException(
					"検索の問い合わせに失敗しました。",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 表示グループコードがグループマスタ上に存在するものかチェックを行う
	 * </pre>
	 *
	 * @param string $groupDisplayCode
	 * @return boolean
	 */
	public function existsGroupCode($groupDisplayCode)
	{
		$result = false;

		if(is_string($groupDisplayCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"strgroupdisplaycode" => pg_escape_string($groupDisplayCode)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$result = true;
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($groupDisplayCode)
					);
		}

		return $result;
	}
}
