<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

/**
 * 製品マスタに関連する処理を提供する
 * clsDBを使用しているが処理が不完全な為注意すること
 *
 * @see clsDB
 */
class UtilProduct extends WithQuery
{
	/**
	 * 製品コードを基に製品マスタのレコードを取得する
	 *
	 * @param string $productCode 製品コード
	 * @return 製品マスタレコード連想配列
	 */
	public function selectProductByProductCode($productCode)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array(
				"productCode" => pg_escape_string($productCode)
		);

		// 業務コードの説明を取得する
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// 一致する行が存在する場合
			if (1 <= pg_num_rows($pgResult))
			{
				$result = pg_fetch_array($pgResult, 0);
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
	 * 製品コードを基に製品名称を取得する
	 *
	 * @param string $productCode 製品コード
	 * @return 製品名称
	 */
	public function selectProductNameByProductCode($productCode, $reviseCode)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array(
				"productCode" => pg_escape_string($productCode),
				"strReviseCode" => pg_escape_string($reviseCode)
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
				$result = $record["strproductname"];
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
	 * <pre>
	 * 製品コードがマスタ上に存在するものかチェックを行う
	 * </pre>
	 *
	 * @param string $productCode
	 * @return boolean
	 */
	public function existsProductCode($productCode)
	{
		$result = false;

		if(is_string($productCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"strproductcode" => pg_escape_string($productCode)
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
					"引数1:".gettype($productCode)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 顧客品番(商品コード)が指定された製品コードと紐付くものかチェックを行う
	 * </pre>
	 *
	 * @param string $goodsCode 顧客品番(商品コード)
	 * @param string $productCode 製品コード
	 * @return boolean
	 */
	public function existsGoodsCodeWithProductCode($goodsCode, $productCode)
	{
		$result = false;

		if(is_string($goodsCode) && is_string($productCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"strgoodscode" => pg_escape_string($goodsCode),
					"strproductcode" => pg_escape_string($productCode)
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
					"引数1:".gettype($goodsCode).
					"引数2:".gettype($productCode)
					);
		}

		return $result;
	}
}
