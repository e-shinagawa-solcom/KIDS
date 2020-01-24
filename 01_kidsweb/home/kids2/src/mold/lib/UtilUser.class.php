<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

/**
 * ユーザマスタに関連する処理を提供する
 * clsDBを使用しているが処理が不完全な為注意すること
 *
 * @see clsDB
 */
class UtilUser extends WithQuery
{
	/**
	 * ユーザコードを基にユーザの表示名を取得する
	 *
	 * @param string $userCode ユーザコード
	 * @return 表示ユーザ名
	 */
	public function selectDisplayNameByUserCode($userCode)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array(
				"userCode" => pg_escape_string($userCode)
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
				$result = $record["struserdisplayname"];
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

		return $result;
	}

	/**
	 * ユーザコードを基にユーザの表示コードを取得する
	 *
	 * @param string $userCode ユーザコード
	 * @return 表示ユーザ名
	 */
	public function selectDisplayCodeByUserCode($userCode)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array(
				"userCode" => pg_escape_string($userCode)
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
				$result = $record["struserdisplaycode"];
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

		return $result;
	}

	/**
	 * 表示ユーザコードを基にユーザコードを取得する
	 *
	 * @param string $displayUserCode 表示ユーザコード
	 * @param boolean $required 索引結果必須フラグ
	 * @return ユーザコード
	 */
	public function selectUserCodeByDisplayUserCode($displayUserCode, $required = true)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array(
				"struserdisplaycode" => pg_escape_string($displayUserCode)
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
				$result = $record["lngusercode"];
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
	 * 表示ユーザコードがユーザマスタ上に存在するものかチェックを行う
	 * </pre>
	 *
	 * @param string $userDisplayCode
	 * @return boolean
	 */
	public function existsUserCode($userDisplayCode)
	{
		$result = false;

		if(is_string($userDisplayCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"strgroupdisplaycode" => pg_escape_string($userDisplayCode)
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
					"引数1:".gettype($userDisplayCode)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 表示ユーザコードが指定された表示グループコードと紐付くものかチェックを行う
	 * </pre>
	 *
	 * @param string $userDisplayCode 表示ユーザコード
	 * @param string $groupDisplayCode 表示グループコード
	 * @return boolean
	 */
	public function existsUserCodeWithGroupCode($userDisplayCode, $groupDisplayCode)
	{
		$result = false;

		if(is_string($userDisplayCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"struserdisplaycode" => pg_escape_string($userDisplayCode),
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
					"引数1:".gettype($userDisplayCode).
					"引数2:".gettype($groupDisplayCode)
					);
		}

		return $result;
	}
}
