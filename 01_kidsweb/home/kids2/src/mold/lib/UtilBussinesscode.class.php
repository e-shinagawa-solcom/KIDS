<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

define("M_BUSINESSCODE", "m_businesscode");

/**
 * 業務コードの解決を行う
 * clsDBを使用しているが処理が不完全な為注意すること
 *
 * @see clsDB
 */
class UtilBussinesscode extends WithQuery
{
	/**
	 * 業務コードの説明を取得する
	 *
	 * @param string $businesscodeName 業務コード名
	 * @param string $businesscode 業務コード
	 * @param boolean $exists 索引できた場合はtrueを返す
	 * @return 業務コード名 取得できなかった場合はfalseを返す
	 */
	public function getDescription($businesscodeName, $businesscode, $exists = false)
	{
		$result = false;

		if (is_string($businesscode) && is_string($businesscode))
		{
			$queryDescription = file_get_contents($this->getQueryFileName("selectDescriptionFromBussinesscode"));
			// クエリパラメータ作成(SELECT)
			$paramDescription = array(
				"businesscodeName" => pg_escape_string($businesscodeName),
				"businesscode" => pg_escape_string($businesscode)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $queryDescription);
			$pgResultDescription = pg_execute("", $paramDescription);

			// 検索の問い合わせに成功した場合
			if ($pgResultDescription)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResultDescription))
				{
					if ($exists)
					{
						$result = true;
					}
					else
					{
						// 検索結果の先頭行から業務コード説明を取得
						$resultRow = pg_fetch_array($pgResultDescription, 0, PGSQL_ASSOC);
						$result = $resultRow["description"];
					}
				}
				// 一致する行が存在しない場合
				else
				{
					if (!$exists)
					{
						throw new SQLException(
								"検索条件に一致するレコードが存在しませんでした。",
								$queryDescription,
								$paramDescription
								);
					}
				}
			}
			// 検索の問い合わせに失敗した場合
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$queryDescription,
						$paramDescription
				);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($businesscodeName)."\n".
					"引数2:".gettype($businesscode)."\n"
			);
		}

		return $result;
	}
}
