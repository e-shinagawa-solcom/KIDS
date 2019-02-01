<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

/**
 * ���롼�ץޥ����˴�Ϣ����������󶡤���
 * clsDB����Ѥ��Ƥ��뤬�������Դ����ʰ���դ��뤳��
 *
 * @see clsDB
 */
class UtilGroup extends WithQuery
{
	/**
	 * ���롼�ץ����ɤ��˥��롼�פ�ɽ��̾���������
	 *
	 * @param string $groupCode ���롼�ץ�����
	 * @return ɽ�����롼��̾
	 */
	public function selectDisplayNameByGroupCode($groupCode)
	{
		$result = false;

		if(is_string($groupCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"groupCode" => pg_escape_string($groupCode)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$record = pg_fetch_array($pgResult, 0);
					// ɽ��̾�μ���
					$result = $record["strgroupdisplayname"];
				}
				else
				{
					throw new SQLException(
							"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
					);
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($groupCode)
					);
		}

		return $result;
	}

	/**
	 * ���롼�ץ����ɤ���ɽ�������ɤ��������
	 *
	 * @param string $groupCode ���롼�ץ�����
	 * @return ɽ�����롼��̾
	 */
	public function selectDisplayCodeByGroupCode($groupCode)
	{
		$result = false;

		if(is_string($groupCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"groupCode" => pg_escape_string($groupCode)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$record = pg_fetch_array($pgResult, 0);
					// ɽ��̾�μ���
					$result = $record["strgroupdisplaycode"];
				}
				else
				{
					throw new SQLException(
							"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($groupCode)
					);
		}

		return $result;
	}

	/**
	 * ɽ�����롼�ץ����ɤ��˥��롼�ץ����ɤ��������
	 *
	 * @param string $displayGroupCode ɽ�����롼�ץ�����
	 * @param boolean $required �������ɬ�ܥե饰
	 * @return ���롼�ץ�����
	 */
	public function selectGroupCodeByDisplayGroupCode($displayGroupCode, $required = true)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array(
				"strgroupdisplaycode" => pg_escape_string($displayGroupCode)
		);

		// ��̳�����ɤ��������������
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// ���פ���Ԥ�¸�ߤ�����
			if (1 <= pg_num_rows($pgResult))
			{
				$record = pg_fetch_array($pgResult, 0);
				// ɽ��̾�μ���
				$result = $record["lnggroupcode"];
			}
			else
			{
				// ��̤�ɬ�ܤξ��
				if ($required)
				{
					throw new SQLException(
							"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
							);
				}
			}
		}
		else
		{
			throw new SQLException(
					"�������䤤��碌�˼��Ԥ��ޤ�����",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * ɽ�����롼�ץ����ɤ����롼�ץޥ������¸�ߤ����Τ������å���Ԥ�
	 * </pre>
	 *
	 * @param string $groupDisplayCode
	 * @return boolean
	 */
	public function existsGroupCode($groupDisplayCode)
	{
		$result = false;

		if(is_string($groupDisplayCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"strgroupdisplaycode" => pg_escape_string($groupDisplayCode)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$result = true;
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($groupDisplayCode)
					);
		}

		return $result;
	}
}
