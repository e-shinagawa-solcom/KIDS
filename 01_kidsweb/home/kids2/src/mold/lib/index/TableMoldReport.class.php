<?php

require_once(SRC_ROOT.'/mold/lib/index/TableMetaData.class.php');

/**
 * �ⷿĢɼ�ޥ����Υơ��֥�̾�䥫���̾���󶡤���
 */
class TableMoldReport extends TableMetaData
{
	/**
	 * �ơ��֥�̾: �ⷿĢɼ�ޥ���
	 * @var string
	 */
	const TABLE_NAME = "M_MoldReport";

	/**
	 * <pre>
	 * �ⷿĢɼID
	 *
	 * ��(postgresql): text
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
	 * Ģɼ��ʬ
	 *
	 * ��(postgresql): char
	 *
	 * ��Ϣ: ��̳�����ɥޥ���.��̳������
	 * </pre>
	 *
	 * @var string
	 */
	const ReportCategory = "reportcategory";

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
	 * ������
	 *
	 * ��(postgresql): date
	 * </pre>
	 *
	 * @var string
	 */
	const RequestDate = "requestdate";

	/**
	 * <pre>
	 * TO
	 *
	 * ��(postgresql): integer
	 *
	 * ��Ϣ: ��ҥޥ���.��ҥ�����
	 * </pre>
	 *
	 * @var string
	 */
	const SendTo = "sendto";

	/**
	 * <pre>
	 * ATTN
	 *
	 * ��(postgresql): integer
	 *
	 * ��Ϣ: ��ҥޥ���.��ҥ�����
	 * </pre>
	 *
	 * @var string
	 */
	const Attention = "attention";

	/**
	 * <pre>
	 * CC
	 *
	 * ��(postgresql): integer
	 *
	 * ��Ϣ: ��ҥޥ���.��ҥ�����
	 * </pre>
	 *
	 * @var string
	 */
	const CarbonCopy = "carboncopy";

	/**
	 * <pre>
	 * ���ʥ�����
	 *
	 * ��(postgresql): text
	 *
	 * ��Ϣ: �ⷿ�ޥ���.���ʥ�����
	 * </pre>
	 *
	 * @var string
	 */
	const ProductCode = "productcode";

	/**
	 * <pre>
	 * �ܵ�����(���ʥ�����)
	 *
	 * ��(postgresql): text
	 *
	 * ��Ϣ: ���ʥޥ���.�ܵ�����
	 * </pre>
	 *
	 * @var string
	 */
	const GoodsCode = "goodscode";

	/**
	 * <pre>
	 * �����ʬ
	 *
	 * ��(postgresql): char
	 *
	 * ��Ϣ: ��̳�����ɥޥ���.��̳������
	 * </pre>
	 *
	 * @var string
	 */
	const RequestCategory = "requestcategory";

	/**
	 * <pre>
	 * ��˾��
	 *
	 * ��(postgresql): date
	 * </pre>
	 *
	 * @var string
	 */
	const ActionRequestDate = "actionrequestdate";

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
	 * ��ư��ˡ
	 *
	 * ��(postgresql): char
	 *
	 * ��Ϣ: ��̳�����ɥޥ���.��̳������
	 * </pre>
	 *
	 * @var string
	 */
	const TransferMethod = "transfermethod";

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
	 * �ؼ���ʬ
	 *
	 * ��(postgresql): text
	 *
	 * ��Ϣ: ��̳�����ɥޥ���.��̳������
	 * </pre>
	 *
	 * @var string
	 */
	const InstructionCategory = "instructioncategory";

	/**
	 * <pre>
	 * ������(�ܵ�)
	 *
	 * ��(postgresql): integer
	 *
	 * ��Ϣ: ��ҥޥ���.��ҥ�����
	 * </pre>
	 *
	 * @var string
	 */
	const CustomerCode = "customercode";

	/**
	 * <pre>
	 * KWG����
	 *
	 * ��(postgresql): integer
	 *
	 * ��Ϣ: ���롼�ץޥ���.���롼�ץ�����
	 * </pre>
	 *
	 * @var string
	 */
	const KuwagataGroupCode = "kuwagatagroupcode";

	/**
	 * <pre>
	 * KWGô����
	 *
	 * ��(postgresql): integer
	 *
	 * ��Ϣ: �桼���ޥ���.�桼��������
	 * </pre>
	 *
	 * @var string
	 */
	const KuwagataUserCode = "kuwagatausercode";

	/**
	 * <pre>
	 * ����¾
	 *
	 * ��(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const Note = "note";

	/**
	 * <pre>
	 * ������ν���
	 *
	 * ��(postgresql): char
	 *
	 * ��Ϣ: ��̳�����ɥޥ���.��̳������
	 * </pre>
	 *
	 * @var string
	 */
	const FinalKeep = "finalkeep";

	/**
	 * <pre>
	 * �ֵ�ͽ����
	 *
	 * ��(postgresql): date
	 * </pre>
	 *
	 * @var string
	 */
	const ReturnSchedule = "returnschedule";

	/**
	 * <pre>
	 * ������
	 *
	 * ��(postgresql): text
	 * </pre>
	 *
	 * @var string
	 */
	const MarginalNote = "marginalnote";

	/**
	 * <pre>
	 * �����ѥե饰
	 *
	 * ��(postgresql): boolean
	 * </pre>
	 *
	 * @var string
	 */
	const Printed = "printed";
}