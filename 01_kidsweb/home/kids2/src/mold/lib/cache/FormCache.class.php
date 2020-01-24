<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

define("T_CACHE", "t_cache");

/**
 * フォームデータのキャッシュ管理を行う<br>
 * clsDBを使用しているが処理が不完全な為注意すること
 *
 * @see clsDB
 */
class FormCache extends WithQuery
{
	function __construct()
	{
		parent::__construct();

		// クエリパスをキャッシュ用に変更
		parent::setPathQuery("mold/sql/cache/");
	}

	/**
	 *
	 * キャッシュに任意のデータを追加する
	 * キーが重複した場合は同一キーの値を上書きする
	 *
	 * @param  string $hashcode キー名
	 * @param  $anyData 任意のデータ
	 * @return 追加成功時にTRUEを、追加に失敗した場合はFALSEを返す
	 */
	public function add($hashcode, $anyData)
	{
		$resultStatus = false;

		// 引数が有効な場合
		if (is_string($hashcode) && $anyData)
		{
			// 同一ハッシュ値の存在チェック
			$pgResultExists = $this->get($hashcode);

			// 検索の問い合わせ結果が得られた場合
			if ($pgResultExists)
			{
				// 結果件数を取得
				$recordCount = pg_num_rows($pgResultExists);

				// 同一ハッシュコードが存在する場合
				if (1 <= $recordCount)
				{
					// 検索結果の先頭行からバージョンを取得
					$resultRow = pg_fetch_array($pgResultExists, 0, PGSQL_ASSOC)->version;
					$version = $resultRow["version"];

					// 更新内容
					$setData = array("serializeddata" => $this->convertQueryableData($anyData));

					// 更新条件
					$condition = array(
							"hashcode" => $this->convertQueryableHashcode($hashcode),
							"version" => $version,
							"updateby" => $this->getUserCode()
					);

					// 同一ハッシュコード行のデータ更新
					$pgResultUpdate = pg_update(static::$db->ConnectID, T_CACHE, $setData, $condition, PGSQL_DML_EXEC);

					// 更新結果が得られた場合
					if ($pgResultUpdate)
					{
						if (true)
						{
							$resultStatus = true;
						}
						// 対象レコードへの更新が失敗した(0件)場合
						else
						{
							throw new SQLException(
								"キャッシュデータの更新に失敗しました。",
								"pg_update",
								$condition
							);
						}
					}
					// 更新結果が得られなかった場合
					else
					{
						throw new SQLException(
								"更新クエリの実行に失敗しました。",
								"pg_update",
								$condition
						);
					}
				}
				// 同一ハッシュコードが存在しない場合
				else
				{
					// 挿入キャッシュデータ作成(INSERT)
					$paramInsert = array(
							"hashcode" => $this->convertQueryableHashcode($hashcode),
							"serializeddata" => $this->convertQueryableData($anyData),
							"createby" => $this->getUserCode(),
							"updateby" => $this->getUserCode()
					);
					// キャッシュデータの挿入
					$pgResultInsert = pg_insert(static::$db->ConnectID, T_CACHE, $paramInsert);

					// 挿入結果が得られた場合
					if ($pgResultInsert)
					{
						$resultStatus = true;
					}
					// 挿入結果が得られなかった場合
					else
					{
						throw new SQLException(
								"挿入クエリの実行に失敗しました。",
								"pg_insert",
								$paramInsert
						);
					}
				}
			}
			// 検索の問い合わせ結果が得られなかった場合
			else
			{
				throw new SQLException(
						"検索クエリの実行に失敗しました。",
						"FormCache::get"
				);
			}
		}
		// 無効な引数の場合
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($hashcode)."\n".
					"引数2:".gettype($anyData)."\n"
			);
		}

		return $resultStatus;
	}

	/**
	 *
	 * 内部キャッシュから任意のキーを持つ要素を取得する
	 *
	 * @param  string  $keyName キー名
	 * @param  boolean $exists $this->exists向けスイッチ
	 * @return 結果文字列をなければFALSEを返す<br>
	 *         existsがtrueの場合は索引時にヒットした行の有無を真偽で返す
	 */
	public function get($hashcode, $exists = false)
	{
		$result = false;

		// SQLファイルの取得
		$queryExistsCache = file_get_contents($this->getQueryFileName("selectCacheDataByHashcode"));
		// クエリパラメータ作成(SELECT)
		$paramExists = array($this->convertQueryableHashcode($hashcode));

		// 同一ハッシュ値の存在チェック
		pg_prepare(static::$db->ConnectID, "", $queryExistsCache);
		$pgResultExists = pg_execute("", $paramExists);

		// 検索の問い合わせに成功した場合
		if ($pgResultExists)
		{
			// existsスイッチが真の場合
			if($exists)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResultExists))
				{
					$result = true;
				}
			}
			// それ以外(通常)
			else
			{
				$result = $pgResultExists;
			}
		}

		return $result;
	}

	/**
	 *
	 * キャッシュから任意のハッシュコードを持つ要素を削除する
	 *
	 * @param  string  $hashcode ハッシュコード
	 * @return 削除できた場合TRUEを、できなければFALSEを返す
	 */
	public function remove($hashcode)
	{
		$resultStatus = false;

		// 有効な引数の場合
		if (is_string($hashcode))
		{
			// SQLファイルの取得
			$queryDeleteCache = file_get_contents($this->getQueryFileName("deleteCacheDataByHashcode"));
			// クエリパラメータ作成(DELETE)
			$paramDelete = array($this->convertQueryableHashcode($hashcode));

			// 該当キャッシュデータ削除
			pg_prepare(static::$db->ConnectID, "", $queryDeleteCache);
			$pgResultDelete = pg_execute("", $paramDelete);

			// 該当レコードの削除に成功した場合
			if ($pgResultDelete)
			{
				$resultStatus = true;
			}
		}
		// 無効な引数の場合
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($hashcode)
			);
		}

		return $resultStatus;
	}

	/**
	 *
	 * キャッシュデータを全て削除する
	 *
	 * @return 削除できた場合TRUEを、できなければFALSEを返す
	 */
	public function clear()
	{
		$resultStatus = false;

		// SQLファイルの取得
		$queryDeleteCacheAll = file_get_contents($this->getQueryFileName("deleteCacheDataAll"));

		// 該当キャッシュデータ削除
		pg_prepare(static::$db->ConnectID, "", $queryDeleteCacheAll);
		$pgResultDeleteAll = pg_execute("", array());

		// 検索の問い合わせに成功した場合
		if ($pgResultDeleteAll)
		{
			$resultStatus = true;
		}

		return $resultStatus;
	}

	/**
	 *
	 * キャッシュ内に指定されたハッシュコードを持つレコードが存在するか確認を行う
	 *
	 * @param  string  $hashcode ハッシュコード
	 * @return 指定されたハッシュコードを持つ要素が存在する場合はTRUEを
	 *         なければFALSEを返す
	 */
	public function exists($hashcode)
	{
		// 内部キャッシュのチェック
		return $this->get($hashcode, true);
	}

	/**
	 * 配列からハッシュ値を生成する
	 *
	 * @param array ハッシュ値生成の基となる配列
	 * @return array ハッシュ値
	 */
	public static function hash_arrays(array $array)
	{
		return hash('sha256', serialize($array));
	}

	public static function deserialize($str)
	{
		$converted = mb_convert_encoding($str, "utf-8", "eucjp-win");
		return  unserialize($converted);
	}

	/**
	 * 渡された任意のデータをSQLとして利用可能な文字列(直列化)にして返す
	 *
	 * @param unknown $anyData
	 * @return SQLにて利用な文字列
	 */
	private function convertQueryableData($anyData)
	{
		return mb_convert_encoding(pg_escape_string(serialize($anyData)), "eucjp-win", "utf-8");
	}

	/**
	 * 渡されたハッシュコードをSQLとして利用可能な文字列にして返す
	 *
	 * @param unknown $anyData
	 * @return SQLにて利用な文字列
	 */
	private function convertQueryableHashcode($hashcode)
	{
		return mb_convert_encoding(pg_escape_string($hashcode), "eucjp-win", "utf-8");
	}
}
