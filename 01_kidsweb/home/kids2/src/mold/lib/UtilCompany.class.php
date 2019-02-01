<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

/**
 * 会社マスタに関連する処理を提供する
 * clsDBを使用しているが処理が不完全な為注意すること
 *
 * @see clsDB
 */
class UtilCompany extends WithQuery
{
	/**
	 * 会社コードを基に会社表示名を取得する
	 *
	 * @param string $companyCode 会社コード
	 * @return 表示ユーザ名
	 */
	public function selectDisplayNameByCompanyCode($companyCode)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array(
				"companyCode" => pg_escape_string($companyCode)
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
				$result = $record["strcompanydisplayname"];
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
	 * 会社コードを基に会社表示コードを取得する
	 *
	 * @param string $companyCode 会社コード
	 * @return 表示ユーザ名
	 */
	public function selectDisplayCodeByCompanyCode($companyCode)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array(
				"companyCode" => pg_escape_string($companyCode)
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
				$result = $record["strcompanydisplaycode"];
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
	 * 表示会社コードを基に会社コードを取得する
	 *
	 * @param string $displayCompanyCode 表示会社コード
	 * @param boolean $required 索引結果必須フラグ
	 * @return 会社コード
	 */
	public function selectCompanyCodeByDisplayCompanyCode($displayCompanyCode, $required = true)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array(
				"strcompanydisplaycode" => pg_escape_string($displayCompanyCode)
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
				$result = $record["lngcompanycode"];
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
	 * 表示会社コード(属性:顧客, その他)が会社マスタ上に存在するものかチェックを行う
	 * </pre>
	 *
	 * @param string $companyDisplayCode
	 * @return boolean
	 */
	public function existsCustomerCode($companyDisplayCode)
	{
		$result = false;

		if(is_string($companyDisplayCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"strcompanydisplaycode" => pg_escape_string($companyDisplayCode)
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
					"引数1:".gettype($companyDisplayCode)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 表示会社コード(属性:工場, その他)が会社マスタ上に存在するものかチェックを行う
	 * </pre>
	 *
	 * @param string $companyDisplayCode
	 * @return boolean
	 */
	public function existsFactoryCode($companyDisplayCode)
	{
		$result = false;

		if(is_string($companyDisplayCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"strcompanydisplaycode" => pg_escape_string($companyDisplayCode)
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
					"引数1:".gettype($companyDisplayCode)
					);
		}

		return $result;
	}
}
