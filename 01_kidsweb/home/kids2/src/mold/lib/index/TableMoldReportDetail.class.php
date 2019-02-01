<?php

require_once(SRC_ROOT.'/mold/lib/index/TableMetaData.class.php');

/**
 * �ⷿĢɼ�ܺ٤Υơ��֥�̾�䥫���̾���󶡤���
 */
class TableMoldReportDetail extends TableMetaData
{
	/**
	 * �ơ��֥�̾: �ⷿĢɼ�ܺ�
	 * @var string
	 */
	const TABLE_NAME = "T_MoldReportDetail";

	/**
	 * <pre>
	 * �ⷿĢɼID
	 *
	 * ��(postgresql): text
	 *
	 * ��Ϣ: ��ҥޥ���.��ҥ�����
	 * </pre>
	 *
	 * @var string
	 */
	const MoldReportId = "moldreportid";

	/**
	 * <pre>
	 * ��ӥ����
	 *
	 * ��(postgresql): integer
	 * </pre>
	 *
	 * @var string
	 */
	const Revision = "revision";

	/**
	 * <pre>
	 * ���
	 *
	 * ��(postgresql): integer
	 * </pre>
	 *
	 * @var string
	 */
	const ListOrder = "listorder";

	/**
	 * <pre>
	 * �ⷿNO
	 *
	 * ��(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const MoldNo = "moldno";

	/**
	 * <pre>
	 * �ⷿ����
	 *
	 * ��(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const MoldDescription = "molddescription";
}