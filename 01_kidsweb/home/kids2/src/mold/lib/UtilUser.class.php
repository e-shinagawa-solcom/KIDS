<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

/**
 * �桼���ޥ����˴�Ϣ����������󶡤���
 * clsDB����Ѥ��Ƥ��뤬�������Դ����ʰ���դ��뤳��
 *
 * @see clsDB
 */
class UtilUser extends WithQuery
{
	/**
	 * �桼�������ɤ��˥桼����ɽ��̾���������
	 *
	 * @param string $userCode �桼��������
	 * @return ɽ���桼��̾
	 */
	public function selectDisplayNameByUserCode($userCode)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array(
				"userCode" => pg_escape_string($userCode)
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
				$result = $record["struserdisplayname"];
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

		return $result;
	}

	/**
	 * �桼�������ɤ��˥桼����ɽ�������ɤ��������
	 *
	 * @param string $userCode �桼��������
	 * @return ɽ���桼��̾
	 */
	public function selectDisplayCodeByUserCode($userCode)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array(
				"userCode" => pg_escape_string($userCode)
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
				$result = $record["struserdisplaycode"];
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

		return $result;
	}

	/**
	 * ɽ���桼�������ɤ��˥桼�������ɤ��������
	 *
	 * @param string $displayUserCode ɽ���桼��������
	 * @param boolean $required �������ɬ�ܥե饰
	 * @return �桼��������
	 */
	public function selectUserCodeByDisplayUserCode($displayUserCode, $required = true)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array(
				"struserdisplaycode" => pg_escape_string($displayUserCode)
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
				$result = $record["lngusercode"];
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
	 * ɽ���桼�������ɤ��桼���ޥ������¸�ߤ����Τ������å���Ԥ�
	 * </pre>
	 *
	 * @param string $userDisplayCode
	 * @return boolean
	 */
	public function existsUserCode($userDisplayCode)
	{
		$result = false;

		if(is_string($userDisplayCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"strgroupdisplaycode" => pg_escape_string($userDisplayCode)
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
					"����1:".gettype($userDisplayCode)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * ɽ���桼�������ɤ����ꤵ�줿ɽ�����롼�ץ����ɤ�ɳ�դ���Τ������å���Ԥ�
	 * </pre>
	 *
	 * @param string $userDisplayCode ɽ���桼��������
	 * @param string $groupDisplayCode ɽ�����롼�ץ�����
	 * @return boolean
	 */
	public function existsUserCodeWithGroupCode($userDisplayCode, $groupDisplayCode)
	{
		$result = false;

		if(is_string($userDisplayCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"struserdisplaycode" => pg_escape_string($userDisplayCode),
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
					"����1:".gettype($userDisplayCode).
					"����2:".gettype($groupDisplayCode)
					);
		}

		return $result;
	}
}
