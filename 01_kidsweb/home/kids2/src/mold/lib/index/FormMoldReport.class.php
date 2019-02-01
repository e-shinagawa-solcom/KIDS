<?php

require_once(SRC_ROOT.'/mold/lib/index/FormMoldRegistBase.class.php');

/**
 * �ⷿĢɼ�����ǤΥե�����ǡ����˥�����������٤Υ������󶡤���
 */
class FormMoldReport extends FormMoldRegistBase
{
	/**
	 * �ⷿĢɼID
	 * @var string
	 */
	const MoldReportId = "MoldReportId";

	/**
	 * ��ӥ����
	 * @var string
	 */
	const Revision = "Revision";

	/**
	 * Ģɼ��ʬ
	 * @var string
	 */
	const ReportCategory = "ReportCategory";

	/**
	 * ������
	 * @var string
	 */
	const RequestDate = "RequestDate";

	/**
	 * �����ʬ
	 * @var string
	 */
	const RequestCategory = "RequestCategory";

	/**
	 * ��˾��
	 * @var string
	 */
	const ActionRequestDate = "ActionRequestDate";

	/**
	 * ��ư��ˡ
	 * @var string
	 */
	const TransferMethod = "TransferMethod";

	/**
	 * �ⷿ����
	 * @var string
	 */
	const MoldDescription = "MoldDescription";

	/**
	 * �ؼ���ʬ
	 * @var string
	 */
	const InstructionCategory = "InstructionCategory";

	/**
	 * ������(�ܵ�:ɽ����ҥ�����)
	 * @var string
	 */
	const CustomerCode = "CustomerCode";

	/**
	 * ������(�ܵ�:ɽ�����̾)
	 * @var string
	 */
	const CustomerName = "CustomerName";

	/**
	 * ô�����롼�ץ�����
	 * @var string
	 */
	const KuwagataGroupCode = "KuwagataGroupCode";

	/**
	 * ô�����롼��ɽ��̾
	 * @var string
	 */
	const KuwagataGroupName = "KuwagataGroupName";

	/**
	 * ô����(�桼��������)
	 * @var string
	 */
	const KuwagataUserCode = "KuwagataUserCode";

	/**
	 * ô����̾(�桼��ɽ��̾)
	 * @var string
	 */
	const KuwagataUserName = "KuwagataUserName";

	/**
	 * ������ν���
	 * @var string
	 */
	const FinalKeep = "FinalKeep";

	/**
	 * �ֵ�ͽ����
	 * @var string
	 */
	const ReturnSchedule = "ReturnSchedule";

	/**
	 * ����
	 * @var string
	 */
	const Note = "Note";

	/**
	 * ������
	 * @var string
	 */
	const MarginalNote = "MarginalNote";

	/**
	 * �С������
	 * @var string
	 */
	const Version = "Version";
}