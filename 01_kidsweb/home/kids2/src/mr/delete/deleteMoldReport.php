<?php

// ----------------------------------------------------------------------------
/**
 * �ⷿĢɼ���� �������
 */
// ----------------------------------------------------------------------------
include('conf.inc');
require(LIB_FILE);
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilGroup.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilUser.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilCompany.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReportDetail.class.php');

$objDB = new clsDB ();
$objAuth = new clsAuth ();
$objDB->open ( "", "", "", "" );

// ���å�����ǧ
$objAuth = fncIsSession ($_REQUEST["strSessionID"], $objAuth, $objDB);

// 1900 �ⷿĢɼ����
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1905 �ⷿĢɼ����(���)
if ( !fncCheckAuthority( DEF_FUNCTION_MR5, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

$moldReportId = $_REQUEST["MoldReportId"];
$revision = $_REQUEST["Revision"];
$version = $_REQUEST["Version"];

// �ⷿĢɼID���ϥ�ӥ����λ��꤬�ʤ����
if (!$moldReportId || !(0 <= $revision) || !(0 <= $version))
{
	// ����������顼
	fncOutputError(9061, DEF_ERROR, "�ѥ�᡼���������Ǥ���", TRUE, "", $objDB);
}

// �桼�ƥ���ƥ��Υ��󥹥��󥹼���
$utilMold = UtilMold::getInstance();
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();
$utilProduct = UtilProduct::getInstance();

$objDB->transactionBegin();

// �ⷿĢɼ�μ���
if (!$report = $utilMold->selectMoldReport($moldReportId, $revision, $version))
{
	// ����������顼
	fncOutputError(9061, DEF_ERROR, "�ⷿĢɼ�μ����˼��Ԥ��ޤ������оݥǡ������ѹ����줿��ǽ��������ޤ���", TRUE, "", $objDB);
}

// �ⷿĢɼ�ܺ٤μ���
if (!$detail = $utilMold->selectMoldReportDetail($moldReportId, $revision))
{
	// ����������顼
	fncOutputError(9061, DEF_ERROR, "�ⷿĢɼ�ܺ٤μ����˼��Ԥ��ޤ�����", TRUE, "", $objDB);
}

// �ⷿĢɼ��Ϣ�μ���(��Ϣ��̵ͭ������)
// �����Ǥ�������������������
if ($relation  = $utilMold->selectMoldReportRelationByReport($moldReportId, $revision))
{
	foreach ($relation as $row => $columns)
	{
		$moldNo = $columns[TableMoldReportRelation::MoldNo];
		$historyNo = $columns[TableMoldReportRelation::HistoryNo];

		$history[] = $utilMold->selectMoldHistoryWithoutVersion($moldNo, $historyNo);
	}
}

try
{
	// �ⷿĢɼ��̵����
	$utilMold->disableMoldReport($moldReportId, $revision, $report[TableMoldReport::Version]);
	// �ⷿĢɼ�ܺ٤�̵����
	$utilMold->disableMoldReportDetail($moldReportId, $revision);

	// �ⷿĢɼ��Ϣ�쥳���ɤ�¸�ߤ�����
	if($relation)
	{
		// �ⷿĢɼ��Ϣ��̵����
		$utilMold->disableMoldReportRelationByReport($moldReportId, $revision);

		// �ⷿ������ʬ���
		foreach ($history as $row => $columns)
		{
			$moldNo = $columns[TableMoldHistory::MoldNo];
			$historyNo = $columns[TableMoldHistory::HistoryNo];
			$hisVersion = $columns[TableMoldHistory::Version];

			// �ⷿ�����̵����
			$utilMold->disableMoldHistory($moldNo, $historyNo, $hisVersion);
		}
	}
}
catch (SQLException $e)
{
	// ����Хå�
	$objDB->transactionRollback();
	// ����������顼
	fncOutputError(9061, DEF_ERROR, "�оݥǡ������ѹ����줿��ǽ��������ޤ���", TRUE, "", $objDB);
}

// ���ߥå�
$objDB->transactionCommit();

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ("/mr/delete/mr_finish_delete.html");

// �ץ졼���ۥ�����ִ�
$objTemplate->replace($_REQUEST);
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;
