<?php

/**
 * �ơ��֥붦�̤Υ᥿�ǡ����򰷤������̾���󶡤���
 */
class TableMetaData
{
	/**
	 * <pre>
	 * �쥳���ɺ�������
	 *
	 * ��(postgresql):
	 *     timestamp without time zone
	 * </pre>
	 *
	 * @var string
	 */
	const Created = "created";

	/**
	 * <pre>
	 * ������(�桼��������)
	 *
	 * ��(postgresql): integer
	 * </pre>
	 *
	 * @var string
	 */
	const CreateBy = "createby";

	/**
	 * <pre>
	 * �쥳���ɹ�������
	 *
	 * ��(postgresql):
	 *     timestamp without time zone
	 * </pre>
	 *
	 * @var string
	 */
	const Updated = "updated";

	/**
	 * <pre>
	 * ������(�桼��������)
	 *
	 * ��(postgresql): integer
	 * </pre>
	 *
	 * @var string
	 */
	const UpdateBy = "updateby";

	/**
	 * <pre>
	 * �С������
	 *
	 * ��(postgresql): integer
	 * </pre>
	 *
	 * @var string
	 */
	const Version = "version";

	/**
	 * <pre>
	 * ����ե饰
	 *
	 * ��(postgresql): boolean
	 * </pre>
	 * @var string
	 */
	const DeleteFlag = "deleteflag";
}