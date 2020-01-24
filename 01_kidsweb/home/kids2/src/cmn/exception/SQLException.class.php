<?php

/**
 * SQLに関する例外
 * TODO ケース別に派生クラスを作成
 */
class SQLException extends RuntimeException
{
	public function __construct($message, $query = "", $param = array())
	{
		$message = $message."\n".
					"クエリ:".$query."\n".
					"パラメータ:".var_export($param, true)."\n";

		parent::__construct($message);
	}
}
