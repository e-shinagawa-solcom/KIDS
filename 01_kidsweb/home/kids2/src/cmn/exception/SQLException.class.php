<?php

/**
 * SQL�˴ؤ����㳰
 * TODO �������̤��������饹�����
 */
class SQLException extends RuntimeException
{
	public function __construct($message, $query = "", $param = array())
	{
		$message = $message."\n".
					"������:".$query."\n".
					"�ѥ�᡼��:".var_export($param, true)."\n";

		parent::__construct($message);
	}
}
