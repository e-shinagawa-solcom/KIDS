<?php

class Singleton
{
	/**
	 * ���󥹥ȥ饯�������ƽФ��Բ�
	 *
	 * @return void
	 */
	protected function __construct()
	{

	}

	/**
	 * ������¹��޻�
	 */
	private function __clone()
	{

	}

	/**
	 * ���󥷥ꥢ�饤���¹��޻�
	 */
	private function __wakeup()
	{

	}

	/**
	 * @return Singleton���饹 ���󥹥���
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