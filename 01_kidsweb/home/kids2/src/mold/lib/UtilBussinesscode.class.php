<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

define("M_BUSINESSCODE", "m_businesscode");

/**
 * ��̳�����ɤβ���Ԥ�
 * clsDB����Ѥ��Ƥ��뤬�������Դ����ʰ���դ��뤳��
 *
 * @see clsDB
 */
class UtilBussinesscode extends WithQuery
{
	/**
	 * ��̳�����ɤ��������������
	 *
	 * @param string $businesscodeName ��̳������̾
	 * @param string $businesscode ��̳������
	 * @param boolean $exists �����Ǥ�������true���֤�
	 * @return ��̳������̾ �����Ǥ��ʤ��ä�����false���֤�
	 */
	public function getDescription($businesscodeName, $businesscode, $exists = false)
	{
		$result = false;

		if (is_string($businesscode) && is_string($businesscode))
		{
			$queryDescription = file_get_contents($this->getQueryFileName("selectDescriptionFromBussinesscode"));
			// ������ѥ�᡼������(SELECT)
			$paramDescription = array(
				"businesscodeName" => pg_escape_string($businesscodeName),
				"businesscode" => pg_escape_string($businesscode)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $queryDescription);
			$pgResultDescription = pg_execute("", $paramDescription);

			// �������䤤��碌�������������
			if ($pgResultDescription)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResultDescription))
				{
					if ($exists)
					{
						$result = true;
					}
					else
					{
						// ������̤���Ƭ�Ԥ����̳���������������
						$resultRow = pg_fetch_array($pgResultDescription, 0, PGSQL_ASSOC);
						$result = $resultRow["description"];
					}
				}
				// ���פ���Ԥ�¸�ߤ��ʤ����
				else
				{
					if (!$exists)
					{
						throw new SQLException(
								"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
								$queryDescription,
								$paramDescription
								);
					}
				}
			}
			// �������䤤��碌�˼��Ԥ������
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$queryDescription,
						$paramDescription
				);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($businesscodeName)."\n".
					"����2:".gettype($businesscode)."\n"
			);
		}

		return $result;
	}
}
