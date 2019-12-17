<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

define("T_CACHE", "t_cache");

/**
 * �ե�����ǡ����Υ���å��������Ԥ�<br>
 * clsDB����Ѥ��Ƥ��뤬�������Դ����ʰ���դ��뤳��
 *
 * @see clsDB
 */
class FormCache extends WithQuery
{
	function __construct()
	{
		parent::__construct();

		// ������ѥ��򥭥�å����Ѥ��ѹ�
		parent::setPathQuery("mold/sql/cache/");
	}

	/**
	 *
	 * ����å����Ǥ�դΥǡ������ɲä���
	 * ��������ʣ��������Ʊ�쥭�����ͤ��񤭤���
	 *
	 * @param  string $hashcode ����̾
	 * @param  $anyData Ǥ�դΥǡ���
	 * @return �ɲ���������TRUE���ɲä˼��Ԥ�������FALSE���֤�
	 */
	public function add($hashcode, $anyData)
	{
		$resultStatus = false;

		// ������ͭ���ʾ��
		if (is_string($hashcode) && $anyData)
		{
			// Ʊ��ϥå����ͤ�¸�ߥ����å�
			$pgResultExists = $this->get($hashcode);

			// �������䤤��碌��̤�����줿���
			if ($pgResultExists)
			{
				// ��̷�������
				$recordCount = pg_num_rows($pgResultExists);

				// Ʊ��ϥå��女���ɤ�¸�ߤ�����
				if (1 <= $recordCount)
				{
					// ������̤���Ƭ�Ԥ���С����������
					$resultRow = pg_fetch_array($pgResultExists, 0, PGSQL_ASSOC)->version;
					$version = $resultRow["version"];

					// ��������
					$setData = array("serializeddata" => $this->convertQueryableData($anyData));

					// �������
					$condition = array(
							"hashcode" => $this->convertQueryableHashcode($hashcode),
							"version" => $version,
							"updateby" => $this->getUserCode()
					);

					// Ʊ��ϥå��女���ɹԤΥǡ�������
					$pgResultUpdate = pg_update(static::$db->ConnectID, T_CACHE, $setData, $condition, PGSQL_DML_EXEC);

					// ������̤�����줿���
					if ($pgResultUpdate)
					{
						if (true)
						{
							$resultStatus = true;
						}
						// �оݥ쥳���ɤؤι��������Ԥ���(0��)���
						else
						{
							throw new SQLException(
								"����å���ǡ����ι����˼��Ԥ��ޤ�����",
								"pg_update",
								$condition
							);
						}
					}
					// ������̤������ʤ��ä����
					else
					{
						throw new SQLException(
								"����������μ¹Ԥ˼��Ԥ��ޤ�����",
								"pg_update",
								$condition
						);
					}
				}
				// Ʊ��ϥå��女���ɤ�¸�ߤ��ʤ����
				else
				{
					// ��������å���ǡ�������(INSERT)
					$paramInsert = array(
							"hashcode" => $this->convertQueryableHashcode($hashcode),
							"serializeddata" => $this->convertQueryableData($anyData),
							"createby" => $this->getUserCode(),
							"updateby" => $this->getUserCode()
					);
					// ����å���ǡ���������
					$pgResultInsert = pg_insert(static::$db->ConnectID, T_CACHE, $paramInsert);

					// ������̤�����줿���
					if ($pgResultInsert)
					{
						$resultStatus = true;
					}
					// ������̤������ʤ��ä����
					else
					{
						throw new SQLException(
								"����������μ¹Ԥ˼��Ԥ��ޤ�����",
								"pg_insert",
								$paramInsert
						);
					}
				}
			}
			// �������䤤��碌��̤������ʤ��ä����
			else
			{
				throw new SQLException(
						"����������μ¹Ԥ˼��Ԥ��ޤ�����",
						"FormCache::get"
				);
			}
		}
		// ̵���ʰ����ξ��
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($hashcode)."\n".
					"����2:".gettype($anyData)."\n"
			);
		}

		return $resultStatus;
	}

	/**
	 *
	 * ��������å��夫��Ǥ�դΥ�����������Ǥ��������
	 *
	 * @param  string  $keyName ����̾
	 * @param  boolean $exists $this->exists���������å�
	 * @return ���ʸ�����ʤ����FALSE���֤�<br>
	 *         exists��true�ξ��Ϻ������˥ҥåȤ����Ԥ�̵ͭ�򿿵����֤�
	 */
	public function get($hashcode, $exists = false)
	{
		$result = false;

		// SQL�ե�����μ���
		$queryExistsCache = file_get_contents($this->getQueryFileName("selectCacheDataByHashcode"));
		// ������ѥ�᡼������(SELECT)
		$paramExists = array($this->convertQueryableHashcode($hashcode));

		// Ʊ��ϥå����ͤ�¸�ߥ����å�
		pg_prepare(static::$db->ConnectID, "", $queryExistsCache);
		$pgResultExists = pg_execute("", $paramExists);

		// �������䤤��碌�������������
		if ($pgResultExists)
		{
			// exists�����å������ξ��
			if($exists)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResultExists))
				{
					$result = true;
				}
			}
			// ����ʳ�(�̾�)
			else
			{
				$result = $pgResultExists;
			}
		}

		return $result;
	}

	/**
	 *
	 * ����å��夫��Ǥ�դΥϥå��女���ɤ�������Ǥ�������
	 *
	 * @param  string  $hashcode �ϥå��女����
	 * @return ����Ǥ������TRUE�򡢤Ǥ��ʤ����FALSE���֤�
	 */
	public function remove($hashcode)
	{
		$resultStatus = false;

		// ͭ���ʰ����ξ��
		if (is_string($hashcode))
		{
			// SQL�ե�����μ���
			$queryDeleteCache = file_get_contents($this->getQueryFileName("deleteCacheDataByHashcode"));
			// ������ѥ�᡼������(DELETE)
			$paramDelete = array($this->convertQueryableHashcode($hashcode));

			// ��������å���ǡ������
			pg_prepare(static::$db->ConnectID, "", $queryDeleteCache);
			$pgResultDelete = pg_execute("", $paramDelete);

			// �����쥳���ɤκ���������������
			if ($pgResultDelete)
			{
				$resultStatus = true;
			}
		}
		// ̵���ʰ����ξ��
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($hashcode)
			);
		}

		return $resultStatus;
	}

	/**
	 *
	 * ����å���ǡ��������ƺ������
	 *
	 * @return ����Ǥ������TRUE�򡢤Ǥ��ʤ����FALSE���֤�
	 */
	public function clear()
	{
		$resultStatus = false;

		// SQL�ե�����μ���
		$queryDeleteCacheAll = file_get_contents($this->getQueryFileName("deleteCacheDataAll"));

		// ��������å���ǡ������
		pg_prepare(static::$db->ConnectID, "", $queryDeleteCacheAll);
		$pgResultDeleteAll = pg_execute("", array());

		// �������䤤��碌�������������
		if ($pgResultDeleteAll)
		{
			$resultStatus = true;
		}

		return $resultStatus;
	}

	/**
	 *
	 * ����å�����˻��ꤵ�줿�ϥå��女���ɤ���ĥ쥳���ɤ�¸�ߤ��뤫��ǧ��Ԥ�
	 *
	 * @param  string  $hashcode �ϥå��女����
	 * @return ���ꤵ�줿�ϥå��女���ɤ�������Ǥ�¸�ߤ������TRUE��
	 *         �ʤ����FALSE���֤�
	 */
	public function exists($hashcode)
	{
		// ��������å���Υ����å�
		return $this->get($hashcode, true);
	}

	/**
	 * ���󤫤�ϥå����ͤ���������
	 *
	 * @param array �ϥå����������δ�Ȥʤ�����
	 * @return array �ϥå�����
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
	 * �Ϥ��줿Ǥ�դΥǡ�����SQL�Ȥ������Ѳ�ǽ��ʸ����(ľ��)�ˤ����֤�
	 *
	 * @param unknown $anyData
	 * @return SQL�ˤ����Ѥ�ʸ����
	 */
	private function convertQueryableData($anyData)
	{
		return mb_convert_encoding(pg_escape_string(serialize($anyData)), "eucjp-win", "utf-8");
	}

	/**
	 * �Ϥ��줿�ϥå��女���ɤ�SQL�Ȥ������Ѳ�ǽ��ʸ����ˤ����֤�
	 *
	 * @param unknown $anyData
	 * @return SQL�ˤ����Ѥ�ʸ����
	 */
	private function convertQueryableHashcode($hashcode)
	{
		return mb_convert_encoding(pg_escape_string($hashcode), "eucjp-win", "utf-8");
	}
}
