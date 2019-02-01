<?php

require_once(SRC_ROOT.'/mold/lib/index/TableMetaData.class.php');

/**
 * �ⷿ����Υơ��֥�̾�䥫���̾���󶡤���
 */
class TableMoldHistory extends TableMetaData
{
	/**
	 * �ơ��֥�̾: �ⷿ����
	 * @var string
	 */
	const TABLE_NAME = "T_MoldHistory";

	/**
	 * <pre>
	 * �ⷿNO
	 *
	 * ��(postgresql): text
	 *
	 * ��Ϣ: �ⷿ�ޥ���.�ⷿNO
	 * </pre>
	 *
	 * @var string
	 */
	const MoldNo = "moldno";

	/**
	 * <pre>
	 * �ⷿ����NO
	 *
	 * ��(postgresql): integer
	 * </pre>
	 *
	 * @var string
	 */
	const HistoryNo = "historyno";

	/**
	 * <pre>
	 * ���ơ�����
	 *
	 * ��(postgresql): char
	 *
	 * ��Ϣ: ��̳�����ɥޥ���.��̳������
	 * </pre>
	 *
	 * @var string
	 */
	const Status = "status";

	/**
	 * <pre>
	 * �»���
	 *
	 * ��(postgresql): date
	 * </pre>
	 *
	 * @var string
	 */
	const ActionDate = "actiondate";

	/**
	 * <pre>
	 * �ݴɸ�����
	 *
	 * ��(postgresql): integer
	 *
	 * ��Ϣ: ��ҥޥ���.��ҥ�����
	 * </pre>
	 *
	 * @var string
	 */
	const SourceFactory = "sourcefactory";

	/**
	 * <pre>
	 * ��ư�蹩��
	 *
	 * ��(postgresql): integer
	 *
	 * ��Ϣ: ��ҥޥ���.��ҥ�����
	 * </pre>
	 *
	 * @var string
	 */
	const DestinationFactory = "destinationfactory";

	/**
	 * <pre>
	 * ����1
	 *
	 * ��(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const Remark1 = "remark1";

	/**
	 * <pre>
	 * ����2
	 *
	 * ��(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const Remark2 = "remark2";

	/**
	 * <pre>
	 * ����3
	 *
	 * ��(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const Remark3 = "remark3";

	/**
	 * <pre>
	 * ����4
	 *
	 * ��(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const Remark4 = "remark4";
}