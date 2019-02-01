<?php

require_once(SRC_ROOT.'/mold/lib/index/TableMetaData.class.php');

/**
 * �ⷿĢɼ��Ϣ�Υơ��֥�̾�䥫���̾���󶡤���
 */
class TableMoldReportRelation extends TableMetaData
{
	/**
	 * �ơ��֥�̾: �ⷿĢɼ��Ϣ
	 * @var string
	 */
	const TABLE_NAME = "T_MoldReportRelation";

	/**
	 * <pre>
	 * �ⷿĢɼ��ϢID
	 *
	 * ��(postgresql):
	 * </pre>
	 *
	 * @var string
	 */
	const MoldReportRelationId = "moldreportrelationid";

	/**
	 * <pre>
	 * �ⷿNO
	 *
	 * ��(postgresql):
	 *
	 * ��Ϣ: �ⷿ����.�ⷿNO
	 * </pre>
	 *
	 * @var string
	 */
	const MoldNo = "moldno";

	/**
	 * <pre>
	 * �����ֹ�
	 *
	 * ��(postgresql):
	 *
	 * ��Ϣ: �ⷿ����.�����ֹ�
	 * </pre>
	 *
	 * @var string
	 */
	const HistoryNo = "historyno";

	/**
	 * <pre>
	 * �ⷿĢɼID
	 *
	 * ��(postgresql):
	 *
	 * ��Ϣ: �ⷿĢɼ�ܺ�.�ⷿĢɼID
	 * </pre>
	 *
	 * @var string
	 */
	const MoldReportId = "moldreportid";

	/**
	 * <pre>
	 * ��ӥ����
	 *
	 * ��(postgresql):
	 *
	 * ��Ϣ: �ⷿĢɼ�ܺ�.��ӥ����
	 * </pre>
	 *
	 * @var string
	 */
	const Revision = "revision";
}