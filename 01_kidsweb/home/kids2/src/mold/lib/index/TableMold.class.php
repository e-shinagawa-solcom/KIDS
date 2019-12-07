<?php

require_once(SRC_ROOT.'/mold/lib/index/TableMetaData.class.php');

/**
 * �ⷿ�ޥ����Υơ��֥�̾�䥫���̾���󶡤���
 */
class TableMold extends TableMetaData
{
	/**
	 * �ơ��֥�̾: �ⷿ�ޥ���
	 * @var string
	 */
	const TABLE_NAME = "M_Mold";

	/**
	 * <pre>
	 * �ⷿNO
	 *
	 * ��(postgresql): text
	 *
	 * ��Ϣ: �����ܺ�.�ⷿNO
	 * </pre>
	 *
	 * @var string
	 */
	const MoldNo = "moldno";

	/**
	 * <pre>
	 * �����층
	 *
	 * ��(postgresql): integer
	 *
	 * ��Ϣ: ��ҥޥ���.��ҥ�����
	 * </pre>
	 *
	 * @var string
	 */
	const VenderCode = "vendercode";

	/**
	 * <pre>
	 * ���ʥ�����
	 *
	 * ��(postgresql): text
	 * </pre>
	 *
	 * ��Ϣ: �����ܺ�.���ʥ�����
	 * @var string
	 */
	const ProductCode = "productcode";

	/**
	 * <pre>
	 * ���Υ�����
	 *
	 * ��(postgresql): text
	 * </pre>
	 *
	 * ��Ϣ: �����ܺ�.���Υ�����
	 * @var string
	 */
	const strReviseCode = "strrevisecode";



}