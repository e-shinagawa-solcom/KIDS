<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

/**
 * ��ҥޥ����˴�Ϣ����������󶡤���
 * clsDB����Ѥ��Ƥ��뤬�������Դ����ʰ���դ��뤳��
 *
 * @see clsDB
 */
class UtilCompany extends WithQuery
{
	/**
	 * ��ҥ����ɤ��˲��ɽ��̾���������
	 *
	 * @param string $companyCode ��ҥ�����
	 * @return ɽ���桼��̾
	 */
	public function selectDisplayNameByCompanyCode($companyCode)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array(
				"companyCode" => pg_escape_string($companyCode)
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
				$result = $record["strcompanydisplayname"];
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
	 * ��ҥ����ɤ��˲��ɽ�������ɤ��������
	 *
	 * @param string $companyCode ��ҥ�����
	 * @return ɽ���桼��̾
	 */
	public function selectDisplayCodeByCompanyCode($companyCode)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array(
				"companyCode" => pg_escape_string($companyCode)
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
				$result = $record["strcompanydisplaycode"];
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
	 * ɽ����ҥ����ɤ��˲�ҥ����ɤ��������
	 *
	 * @param string $displayCompanyCode ɽ����ҥ�����
	 * @param boolean $required �������ɬ�ܥե饰
	 * @return ��ҥ�����
	 */
	public function selectCompanyCodeByDisplayCompanyCode($displayCompanyCode, $required = true)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array(
				"strcompanydisplaycode" => pg_escape_string($displayCompanyCode)
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
				$result = $record["lngcompanycode"];
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
	 * ɽ����ҥ�����(°��:�ܵ�, ����¾)����ҥޥ������¸�ߤ����Τ������å���Ԥ�
	 * </pre>
	 *
	 * @param string $companyDisplayCode
	 * @return boolean
	 */
	public function existsCustomerCode($companyDisplayCode)
	{
		$result = false;

		if(is_string($companyDisplayCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"strcompanydisplaycode" => pg_escape_string($companyDisplayCode)
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
					"����1:".gettype($companyDisplayCode)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * ɽ����ҥ�����(°��:����, ����¾)����ҥޥ������¸�ߤ����Τ������å���Ԥ�
	 * </pre>
	 *
	 * @param string $companyDisplayCode
	 * @return boolean
	 */
	public function existsFactoryCode($companyDisplayCode)
	{
		$result = false;

		if(is_string($companyDisplayCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"strcompanydisplaycode" => pg_escape_string($companyDisplayCode)
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
					"����1:".gettype($companyDisplayCode)
					);
		}

		return $result;
	}
}
