<?php

require_once(SRC_ROOT.'/mold/lib/Singleton.class.php');
require_once(SRC_ROOT.'/mold/lib/exception/NoSuchFileException.class.php');
require_once('clsdb.php');

/**
 * DB����������ȼ�����饹�δ���Ȥʤ륯�饹
 *
 */
class WithQuery extends Singleton
{
	/**
	 * SQL�����֤���Ƥ���ǥ��쥯�ȥ�ѥ�
	 * @var string
	 */
	private $pathQuery = 'mold/sql/';

	/**
	 * SQL�ե������������
	 * @var string
	 */
	private $pathSqlFileSuffix = ".sql";

	/**
	 * <pre>
	 * �桼���ޥ���.�桼��������
	 * ����ͤ�99999
	 * INSERT/UPDATE���κ�����/�����Թ��ܤ˥��åȤ����
	 * </pre>
	 *
	 * @var integer
	 */
	protected  $userCode = 99999;

	/**
	 * DB���饹
	 * @var clsDB
	 */
	protected static $db;

	/**
	 * ���󥹥ȥ饯��
	 *
	 * clsDB�ˤƥ��ͥ������򳫤�
	 *
	 * @param clsDB
	 */
	protected function __construct()
	{
		static::$db = new clsDB();
		static::$db->open("", "", "", "");
	}

	/**
	 * �ǥ��ȥ饯��
	 *
	 * clsDB�Υ꥽�����˴���Ԥ�
	 *
	 */
	function __destruct()
	{
		// ���ͥ������OPEN�ξ��
		if(static::$db->isOpen())
		{
			// �ȥ�󥶥�����󤬳��Ϥ���Ƥ�����
			if (static::$db->isTransaction())
			{
				static::$db->execute("ROLLBACK");
			}

			// ���ͥ�����󥯥���
			static::$db->close();
		}
	}

	/**
	 * ���ꤵ�줿SQL�ե�����ؤΥѥ�����������
	 *
	 * @param string $fileName
	 * @return SQL�ե�����ѥ�
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
				throw new NoSuchFileException("¸�ߤ��ʤ��ե����� :".$path);
			}

			return $path;
		}
		else
		{
			throw new InvalidArgumentException("�����η��������Ǥ�������1:".gettype($newPath));
		}
	}


	/**
	 * ���ꤵ��Ƥ륯����ѥ����֤�
	 *
	 * @return ������ѥ�
	 */
	protected function getPathQuery()
	{
		return $this->pathQuery;
	}

	/**
	 * ������ѥ����ѹ�����
	 *
	 * @param string $newPath ������������ѥ�
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

			// �ǥ��쥯�ȥ�ѥ��Υ����å�
			if(file_exists($path))
			{
				$this->pathQuery = $newPath;
			}
			else
			{
				throw new NoSuchFileException("¸�ߤ��ʤ��ѥ� :" . $path);
			}
		}
		else
		{
			throw new InvalidArgumentException("�����η��������Ǥ�������1:".gettype($newPath));
		}
	}

	/**
	 * ���ꤵ��Ƥ륯����ѥ������������֤�
	 *
	 * @return ������ѥ���������
	 */
	protected function getPathSqlFileSuffix()
	{
		return $this->pathSqlFileSuffix;
	}

	/**
	 * ������ѥ������������ѹ�����
	 *
	 * @param string $newSuffix ������������
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
			throw new InvalidArgumentException("�����η��������Ǥ�������1:".gettype($newSuffix));
		}
	}


	/**
	 * ���ꤵ��Ƥ���桼�������ɤ��֤���
	 *
	 * @return �桼��������
	 */
	public function getUserCode()
	{
		return $this->userCode;
	}

	/**
	 * ���ꤵ��Ƥ���桼�������ɤ��ѹ�����
	 *
	 * @param integer $newUsercode
	 * @return �������桼��������
	 */
	public function setUserCode($newUserCode)
	{
		$this->userCode = $newUserCode;
		return $this->userCode;
	}

}
