<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

/**
 * ���ʥޥ����˴�Ϣ����������󶡤���
 * clsDB����Ѥ��Ƥ��뤬�������Դ����ʰ���դ��뤳��
 *
 * @see clsDB
 */
class UtilProduct extends WithQuery
{
	/**
	 * ���ʥ����ɤ������ʥޥ����Υ쥳���ɤ��������
	 *
	 * @param string $productCode ���ʥ�����
	 * @return ���ʥޥ����쥳����Ϣ������
	 */
	public function selectProductByProductCode($productCode)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array(
				"productCode" => pg_escape_string($productCode)
		);

		// ��̳�����ɤ��������������
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// ���פ���Ԥ�¸�ߤ�����
			if (1 <= pg_num_rows($pgResult))
			{
				$result = pg_fetch_array($pgResult, 0);
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
	 * ���ʥ����ɤ�������̾�Τ��������
	 *
	 * @param string $productCode ���ʥ�����
	 * @return ����̾��
	 */
	public function selectProductNameByProductCode($productCode, $reviseCode)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array(
				"productCode" => pg_escape_string($productCode),
				"strReviseCode" => pg_escape_string($reviseCode)
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
				$result = $record["strproductname"];
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
	 * <pre>
	 * ���ʥ����ɤ��ޥ������¸�ߤ����Τ������å���Ԥ�
	 * </pre>
	 *
	 * @param string $productCode
	 * @return boolean
	 */
	public function existsProductCode($productCode)
	{
		$result = false;

		if(is_string($productCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"strproductcode" => pg_escape_string($productCode)
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
					"����1:".gettype($productCode)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ܵ�����(���ʥ�����)�����ꤵ�줿���ʥ����ɤ�ɳ�դ���Τ������å���Ԥ�
	 * </pre>
	 *
	 * @param string $goodsCode �ܵ�����(���ʥ�����)
	 * @param string $productCode ���ʥ�����
	 * @return boolean
	 */
	public function existsGoodsCodeWithProductCode($goodsCode, $productCode)
	{
		$result = false;

		if(is_string($goodsCode) && is_string($productCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"strgoodscode" => pg_escape_string($goodsCode),
					"strproductcode" => pg_escape_string($productCode)
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
					"����1:".gettype($goodsCode).
					"����2:".gettype($productCode)
					);
		}

		return $result;
	}
}
