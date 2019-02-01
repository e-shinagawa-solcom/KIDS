<?php

class Singleton
{
	/**
	 * コンストラクタ外部呼出し不可
	 *
	 * @return void
	 */
	protected function __construct()
	{

	}

	/**
	 * クローン実行抑止
	 */
	private function __clone()
	{

	}

	/**
	 * アンシリアライズ実行抑止
	 */
	private function __wakeup()
	{

	}

	/**
	 * @return Singletonクラス インスタンス
	 */
	public static function getInstance()
	{
		static $instance = array();

		$calledClass = get_called_class();

		if (!isset($instances[$calledClass]))
		{
			$instances[$calledClass] = new $calledClass();
		}

		return $instances[$calledClass];
	}
}