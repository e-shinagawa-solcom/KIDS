<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMetaData.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMold.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMoldReport.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMoldHistory.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMoldReportDetail.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMoldReportRelation.class.php');

/**
 * ���ʲ����줿�Х�ǡ������ǽ���󶡤���
 *
 */
class UtilValidation extends WithQuery
{
	/**
	 * yyyy/mm/dd���������ո��ڤ�Ԥ�
	 * @param string $date
	 * @return boolean
	 */
	public static function checkDateFormatYMD($date)
	{
		if (is_string($date))
		{
			// yyyy/mm/dd�����˥ޥå�������
			if (preg_match("/(19[0-9]{2}|2[0-9]{3})\\/(0[1-9]|1[0-2])\\/([0-2][0-9]|3[0-1])/", $date))
			{
				// ͭ�����դθ���
				list($yyyy, $mm, $dd) = explode("/", $date);
				return checkdate($mm, $dd, $yyyy);
			}
			else
			{
				return false;
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($date)
			);
		}
	}

	/**
	 * �Ϥ��줿yyyy/mm/dd���������դ�����������̤�����Ǥ��뤫Ƚ�ꤹ��
	 * @param string $date
	 * @return boolean
	 */
	public static function isFutureDate($date)
	{
		$result = false;

		if (is_string($date))
		{
			$today = date("Y/m/d");

			if (strtotime($today) < strtotime($date))
			{
				$result = true;
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($date)
					);
		}

		return $result;
	}

	/**
	 * �Ϥ��줿yyyy/mm/dd���������դ���������Ʊ�����դǤ��뤫Ƚ�ꤹ��
	 * @param string $date
	 * @return boolean
	 */
	public static function isToday(DateTime $date)
	{
		$result = false;

		if (is_string($date))
		{
			$today = date("Y/m/d");

			if (strtotime($date) == strtotime($today))
			{
				$result = true;
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($date)
					);
		}

		return $result;
	}

	/**
	 * �Ϥ��줿yyyy/mm/dd���������դ����������������Ǥ��뤫Ƚ�ꤹ��
	 * @param string $date
	 * @return boolean
	 */
	public static function isPastDate($date)
	{
		$result = false;

		if (is_string($date))
		{
			$today = date("Y/m/d");

			if (strtotime($date) < strtotime($today))
			{
				$result = true;
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($date)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �Ϥ��줿yyyy/mm/dd���������դ�2�Ĥ�����ʸ�������Ӥ���
	 *
	 * date2 < date1  : return 1
	 * date1 == date2 : return 0
	 * date1 < date2  : return -1
	 * </pre>
	 * @param string $date1
	 * @param string $date2
	 * @return integer
	 */
	public static function compareDate($date1, $date2)
	{
		if (is_string($date1) && is_string($date2))
		{
			$d1 = strtotime($date1);
			$d2 = strtotime($date2);
			return (($d2 < $d1) ? 1 : ($d1 == $d2 ? 0 : -1));
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($date1).
					"����2:".gettype($date2)
					);
		}
	}
}