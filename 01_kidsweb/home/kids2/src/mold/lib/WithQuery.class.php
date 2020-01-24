<?php

require_once(SRC_ROOT.'/mold/lib/Singleton.class.php');
require_once(SRC_ROOT.'/mold/lib/exception/NoSuchFileException.class.php');
require_once('clsdb.php');

/**
 * DBアクセスを伴うクラスの基底となるクラス
 *
 */
class WithQuery extends Singleton
{
	/**
	 * SQLが配置されているディレクトリパス
	 * @var string
	 */
	private $pathQuery = 'mold/sql/';

	/**
	 * SQLファイルの接尾辞
	 * @var string
	 */
	private $pathSqlFileSuffix = ".sql";

	/**
	 * <pre>
	 * ユーザマスタ.ユーザコード
	 * 初期値は99999
	 * INSERT/UPDATE時の作成者/更新者項目にセットされる
	 * </pre>
	 *
	 * @var integer
	 */
	protected  $userCode = 99999;

	/**
	 * DBクラス
	 * @var clsDB
	 */
	protected static $db;

	/**
	 * コンストラクタ
	 *
	 * clsDBにてコネクションを開く
	 *
	 * @param clsDB
	 */
	protected function __construct()
	{
		static::$db = new clsDB();
		static::$db->open("", "", "", "");
	}

	/**
	 * デストラクタ
	 *
	 * clsDBのリソース破棄を行う
	 *
	 */
	function __destruct()
	{
		// コネクションがOPENの場合
		if(static::$db->isOpen())
		{
			// トランザクションが開始されている場合
			if (static::$db->isTransaction())
			{
				static::$db->execute("ROLLBACK");
			}

			// コネクションクローズ
			static::$db->close();
		}
	}

	/**
	 * 指定されたSQLファイルへのパスを生成する
	 *
	 * @param string $fileName
	 * @return SQLファイルパス
	 * @throws InvalidArgumentException
	 * @see InvalidArgumentException
	 */
	protected function getQueryFileName($fileName)
	{
		if (is_string($fileName))
		{
			$path = SRC_ROOT.$this->pathQuery.$fileName.$this->pathSqlFileSuffix;

			if (!file_exists($path))
			{
				throw new NoSuchFileException("存在しないファイル :".$path);
			}

			return $path;
		}
		else
		{
			throw new InvalidArgumentException("引数の型が不正です。引数1:".gettype($newPath));
		}
	}


	/**
	 * 設定されてるクエリパスを返す
	 *
	 * @return クエリパス
	 */
	protected function getPathQuery()
	{
		return $this->pathQuery;
	}

	/**
	 * クエリパスを変更する
	 *
	 * @param string $newPath 新しいクエリパス
	 * @return void
	 * @throws NoSuchFileException
	 * @throws InvalidArgumentException
	 * @see NoSuchFileException
	 * @see InvalidArgumentException
	 */
	protected function setPathQuery($newPath)
	{
		if (is_string($newPath))
		{
			$path = SRC_ROOT.$newPath;

			// ディレクトリパスのチェック
			if(file_exists($path))
			{
				$this->pathQuery = $newPath;
			}
			else
			{
				throw new NoSuchFileException("存在しないパス :" . $path);
			}
		}
		else
		{
			throw new InvalidArgumentException("引数の型が不正です。引数1:".gettype($newPath));
		}
	}

	/**
	 * 設定されてるクエリパスの接尾辞を返す
	 *
	 * @return クエリパスの接尾辞
	 */
	protected function getPathSqlFileSuffix()
	{
		return $this->pathSqlFileSuffix;
	}

	/**
	 * クエリパスの接尾辞を変更する
	 *
	 * @param string $newSuffix 新しい接尾辞
	 * @return void
	 * @throws InvalidArgumentException
	 * @see InvalidArgumentException
	 */
	protected  function setPathSqlFileSuffix($newSuffix)
	{
		if (is_string($newSuffix))
		{
			$this->pathSqlFileSuffix = $newSuffix;
		}
		else
		{
			throw new InvalidArgumentException("引数の型が不正です。引数1:".gettype($newSuffix));
		}
	}


	/**
	 * 設定されているユーザコードを返す。
	 *
	 * @return ユーザコード
	 */
	public function getUserCode()
	{
		return $this->userCode;
	}

	/**
	 * 設定されているユーザコードを変更する
	 *
	 * @param integer $newUsercode
	 * @return 新しいユーザコード
	 */
	public function setUserCode($newUserCode)
	{
		$this->userCode = $newUserCode;
		return $this->userCode;
	}

}
